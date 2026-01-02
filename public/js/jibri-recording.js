/**
 * Jibri HTTP Recording Manager
 * Bypasses buggy XMPP in Jibri 8.0 by using HTTP API directly
 */
class JibriRecordingManager {
    constructor() {
        this.isRecording = false;
        this.sessionId = null;
        this.roomName = null;
    }

    /**
     * Start server-side recording via Jibri HTTP API
     */
    async startRecording(roomJid) {
        try {
            this.sessionId = `recording_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            this.roomName = roomJid;

            const response = await fetch('/jibri/start-recording', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    room_jid: roomJid,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.isRecording = true;
                console.log('✅ Recording started:', data.session_id);
                return { success: true, sessionId: data.session_id };
            } else {
                console.error('❌ Failed to start recording:', data.error);
                return { success: false, error: data.error };
            }
        } catch (error) {
            console.error('❌ Recording error:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Stop server-side recording
     */
    async stopRecording() {
        try {
            const response = await fetch('/jibri/stop-recording', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.isRecording = false;
                console.log('✅ Recording stopped');
                return { success: true };
            } else {
                console.error('❌ Failed to stop recording:', data.error);
                return { success: false, error: data.error };
            }
        } catch (error) {
            console.error('❌ Stop recording error:', error);
            return { success: false, error: error.message };
        }
    }

    /**
     * Check if Jibri is available
     */
    async checkStatus() {
        try {
            const response = await fetch('/jibri/status');
            const data = await response.json();
            return data.success && data.status.status.busyStatus === 'IDLE';
        } catch (error) {
            console.error('❌ Status check error:', error);
            return false;
        }
    }
}

// Export for use in Jitsi Meet
window.JibriRecordingManager = JibriRecordingManager;
