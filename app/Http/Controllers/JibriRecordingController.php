<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JibriRecordingController extends Controller
{
    private $jibriApiUrl = 'http://localhost:2222/jibri/api/v1.0';
    
    /**
     * Start recording via Jibri HTTP API (bypass buggy XMPP)
     */
    public function startRecording(Request $request)
    {
        try {
            $roomJid = $request->input('room_jid');
            $sessionId = $request->input('session_id', uniqid('recording_'));
            
            $payload = [
                'sessionId' => $sessionId,
                'callParams' => [
                    'callUrlInfo' => [
                        'baseUrl' => 'https://meet.mychoicetutor.com',
                        'callName' => $roomJid
                    ]
                ],
                'callLoginParams' => [
                    'domain' => config('services.jibri.domain'),
                    'username' => config('services.jibri.username'),
                    'password' => config('services.jibri.password')
                ],
                'sinkType' => 'FILE'
            ];
            
            $response = Http::timeout(30)->post("{$this->jibriApiUrl}/startService", $payload);
            
            if ($response->successful()) {
                Log::info("Recording started successfully", ['session_id' => $sessionId]);
                return response()->json([
                    'success' => true,
                    'session_id' => $sessionId,
                    'message' => 'Recording started'
                ]);
            } else {
                Log::error("Failed to start recording", ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to start recording'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("Recording error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Stop recording
     */
    public function stopRecording(Request $request)
    {
        try {
            $response = Http::timeout(30)->post("{$this->jibriApiUrl}/stopService");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recording stopped'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to stop recording'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get Jibri status
     */
    public function getStatus()
    {
        try {
            $response = Http::timeout(5)->get("{$this->jibriApiUrl}/health");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'status' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Jibri not available'
                ], 503);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
