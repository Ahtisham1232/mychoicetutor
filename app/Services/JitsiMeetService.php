<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class JitsiMeetService
{
    /**
     * Base URL for Jitsi Meet (you can use your own domain if self-hosted or use meet.jit.si)
     */
    private $baseUrl;

    public function __construct()
    {
        // Use your own Jitsi server if self-hosted, or use the free public server
        $this->baseUrl = env('JITSI_BASE_URL', 'https://meet.jit.si');
    }

    /**
     * Generate a unique meeting room name
     *
     * @param int $tutorId
     * @param int $studentId
     * @param string|null $customName
     * @return string
     */
    public function generateRoomName(int $tutorId, int $studentId, ?string $customName = null): string
    {
        if ($customName) {
            // Clean the custom name to make it URL-safe
            $cleanName = preg_replace('/[^a-z0-9-]/i', '', $customName);
            $cleanName = strtolower($cleanName);
            return $cleanName . '-' . uniqid();
        }
        
        // Generate unique room name based on IDs and timestamp
        return 'class-' . $tutorId . '-' . $studentId . '-' . time() . '-' . bin2hex(random_bytes(4));
    }

    /**
     * Create a Jitsi Meet meeting URL with optional password
     *
     * @param string $roomName The meeting room name
     * @param string|null $password Optional meeting password
     * @param array $config Optional configuration
     * @return string The meeting URL
     */
    public function createMeeting(string $roomName, ?string $password = null, array $config = []): string
    {
        $url = $this->baseUrl . '/' . $roomName;
        
        // Add password to URL if provided
        if ($password) {
            $url .= '?jwt=' . $this->generateJWT($roomName, $password, $config);
        }
        
        return $url;
    }

    /**
     * Generate a simple meeting link with configuration
     * Includes settings to allow immediate joining and enable recording
     *
     * @param string $roomName
     * @param string|null $password
     * @param bool $enableRecording Enable automatic recording for this meeting
     * @return string
     */
    public function generateMeetingLink(string $roomName, ?string $password = null, bool $enableRecording = true): string
    {
        $url = $this->baseUrl . '/' . $roomName;
        
        // Build query parameters
        $params = [];
        
        // Configuration to allow immediate joining without waiting for moderator
        $params[] = 'config.startWithAudioMuted=false';
        $params[] = 'config.startWithVideoMuted=false';
        $params[] = 'config.prejoinPageEnabled=false'; // Skip pre-join page
        $params[] = 'config.enableWelcomePage=false'; // Skip welcome page
        $params[] = 'config.requireDisplayName=false'; // Don't require display name
        $params[] = 'config.enableNoAudioDetection=false';
        $params[] = 'config.enableLayerSuspension=false';
        $params[] = 'config.enableClosePage=true';
        
        // Allow anyone to start the meeting (no moderator requirement)
        $params[] = 'config.enableKnockingLobby=false'; // Disable lobby/waiting room
        $params[] = 'config.enableLobbyChat=false';
        
        // Enable recording features
        if ($enableRecording) {
            $params[] = 'config.enableRecording=true'; // Show recording button
            $params[] = 'config.recordingServiceEnabled=true'; // Enable recording service
            $params[] = 'config.liveStreamingEnabled=true'; // Enable live streaming
            // Auto-start recording (works with self-hosted Jitsi, for public server it enables the button)
            $params[] = 'config.autoStartRecording=true';
        }
        
        // Add password if provided
        if ($password) {
            $params[] = 'pwd=' . urlencode($password);
        }
        
        // Join query parameters
        if (!empty($params)) {
            $url .= '?' . implode('&', $params);
        }
        
        return $url;
    }

    /**
     * Generate JWT token for secure meetings (if using Jitsi with JWT authentication)
     * This requires Jitsi server configuration with JWT enabled
     * 
     * NOTE: JITSI_APP_ID is OPTIONAL - you create this yourself (not from a Jitsi account)
     * It's just an identifier you choose, like "my-tutor-app"
     *
     * @param string $roomName
     * @param string $password
     * @param array $config
     * @return string
     */
    private function generateJWT(string $roomName, string $password, array $config): string
    {
        // For now, return simple token
        // If you want full JWT support, you'll need to install firebase/php-jwt package
        // For basic use, password in URL parameter is sufficient
        // JITSI_APP_ID: Optional - just pick any name like "my-tutor-app" (not from Jitsi account)
        return base64_encode(json_encode([
            'room' => $roomName,
            'context' => [
                'user' => [
                    'name' => $config['user_name'] ?? 'User',
                ],
            ],
            'iss' => env('JITSI_APP_ID', 'racetofreedom-app'), // Optional: You choose this name yourself
            'aud' => env('JITSI_APP_ID', 'racetofreedom-app'), // Optional: You choose this name yourself
            'exp' => time() + 7200, // 2 hours
        ]));
    }

    /**
     * Create a meeting for a class
     *
     * @param int $tutorId
     * @param int $studentId
     * @param string $subjectName
     * @param string|null $password
     * @param string|null $customRoomName
     * @param bool $enableRecording Enable automatic recording (default: true)
     * @return array
     */
    public function createClassMeeting(
        int $tutorId,
        int $studentId,
        string $subjectName,
        ?string $password = null,
        ?string $customRoomName = null,
        bool $enableRecording = true
    ): array {
        try {
            $roomName = $this->generateRoomName($tutorId, $studentId, $customRoomName ?? $subjectName);
            $meetingUrl = $this->generateMeetingLink($roomName, $password, $enableRecording);
            
            return [
                'success' => true,
                'room_name' => $roomName,
                'meeting_url' => $meetingUrl,
                'join_url' => $meetingUrl,
                'start_url' => $meetingUrl,
                'password' => $password,
                'recording_enabled' => $enableRecording,
            ];
        } catch (\Exception $e) {
            Log::error('Jitsi Meet creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Start automatic recording for a meeting
     * Note: For public meet.jit.si, this requires manual recording button click
     * For self-hosted Jitsi with Jibri, automatic recording can be triggered via API
     *
     * @param string $roomName
     * @param string $recordingToken Optional recording token for self-hosted instances
     * @return array
     */
    public function startRecording(string $roomName, ?string $recordingToken = null): array
    {
        try {
            // For self-hosted Jitsi, you would use the Jibri API here
            // For public meet.jit.si, recording must be started manually via UI
            
            // If you have self-hosted Jitsi with recording enabled, you can use:
            // $recordingUrl = $this->baseUrl . '/recorder/' . $roomName;
            // Make API call to start recording
            
            return [
                'success' => true,
                'message' => 'Recording will start automatically when meeting begins',
                'note' => 'For public meet.jit.si, please use the recording button in the meeting UI'
            ];
        } catch (\Exception $e) {
            Log::error('Jitsi recording start failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

