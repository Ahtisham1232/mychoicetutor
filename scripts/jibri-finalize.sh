#!/bin/bash

# Jibri Finalize Script
# This script is called by Jibri after a recording finishes.
# It finds the recording file and notifies the Laravel application.

RECORDINGS_DIR=$1
LOG_FILE="/tmp/jibri-finalize.log"

echo "[$(date)] Starting finalize script for directory: $RECORDINGS_DIR" >> $LOG_FILE

# Check if directory exists
if [ ! -d "$RECORDINGS_DIR" ]; then
    echo "[$(date)] Error: Directory $RECORDINGS_DIR does not exist." >> $LOG_FILE
    exit 1
fi

# Find the .mp4 file (Jibri usually creates one mp4 file per recording session)
# We use find to get the file.
VIDEO_FILE=$(find "$RECORDINGS_DIR" -name "*.mp4" | head -n 1)

if [ -z "$VIDEO_FILE" ]; then
    echo "[$(date)] Error: No MP4 file found in $RECORDINGS_DIR" >> $LOG_FILE
    exit 1
fi

echo "[$(date)] Found video file: $VIDEO_FILE" >> $LOG_FILE

# Ensure the file is readable by the web server user (www-data)
chmod 644 "$VIDEO_FILE"
# Also ensure the directory is readable/executable
chmod 755 "$RECORDINGS_DIR"

# Extract metadata if possible (metadata.json is sometimes generated)
METADATA_FILE=$(find "$RECORDINGS_DIR" -name "metadata.json" | head -n 1)
ROOM_NAME=""

if [ -f "$METADATA_FILE" ]; then
    # Try to extract room name from metadata if available
    # This depends on jq being installed, otherwise we might need another way or rely on filename/directory
    if command -v jq &> /dev/null; then
        ROOM_NAME=$(jq -r '.meeting_id // empty' "$METADATA_FILE")
    fi
fi

# If room name is still empty, try to parse it from the directory name or file name
# Jibri directory format is often: <room_name>_<timestamp>
if [ -z "$ROOM_NAME" ]; then
    DIR_NAME=$(basename "$RECORDINGS_DIR")
    # Extract everything before the last underscore (timestamp)
    ROOM_NAME=$(echo "$DIR_NAME" | sed 's/_[^_]*$//')
fi

echo "[$(date)] Detected Room Name: $ROOM_NAME" >> $LOG_FILE

# Prepare JSON payload
# We need to send the absolute path to the file so Laravel can pick it up
# Note: This assumes Jibri and Laravel are on the same server or share the filesystem.
JSON_PAYLOAD=$(cat <<EOF
{
  "event": "recording.completed",
  "recording": {
    "name": "$ROOM_NAME",
    "url": "$VIDEO_FILE",
    "status": "off"
  }
}
EOF
)

# Send webhook to Laravel
WEBHOOK_URL="https://127.0.0.1/webhook/jitsi/recording"
SECRET_KEY="MySecureKey2024Random123" # Match this with your .env JITSI_WEBHOOK_SECRET if set

echo "[$(date)] Sending webhook to $WEBHOOK_URL" >> $LOG_FILE

RESPONSE=$(curl -v -s -L -k -H "Host: mychoicetutor.com" -w "\nHTTP_STATUS:%{http_code}" -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Jitsi-Secret: $SECRET_KEY" \
  -d "$JSON_PAYLOAD")

HTTP_STATUS=$(echo "$RESPONSE" | grep "HTTP_STATUS" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | sed 's/HTTP_STATUS:.*//')

echo "[$(date)] Webhook response: $HTTP_STATUS - $BODY" >> $LOG_FILE

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo "[$(date)] Success: Webhook delivered." >> $LOG_FILE
    exit 0
else
    echo "[$(date)] Failed: Webhook returned status $HTTP_STATUS" >> $LOG_FILE
    exit 1
fi
