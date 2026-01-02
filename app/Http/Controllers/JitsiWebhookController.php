<?php

namespace App\Http\Controllers;

use App\Models\zoom_classes;
use App\Models\SlotBooking;
use App\Models\Notification;
use App\Models\tutorregistration;
use App\Events\RealTimeMessage;
use App\Services\RecordingStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JitsiWebhookController extends Controller
{
    protected $recordingStorage;

    public function __construct(RecordingStorageService $recordingStorage)
    {
        $this->recordingStorage = $recordingStorage;
    }

    /**
     * Handle Jitsi recording webhook notifications
     * This endpoint receives notifications when recordings are ready
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Log incoming webhook for debugging
            Log::info('Jitsi webhook received', ['data' => $request->all()]);

            // Validate webhook secret if configured
            // NOTE: JITSI_WEBHOOK_SECRET is OPTIONAL - you create this yourself (not from a Jitsi account)
            // It's just a random string you generate for security, like: "MySecureKey2024Random123"
            $webhookSecret = env('JITSI_WEBHOOK_SECRET');
            if ($webhookSecret && $request->header('X-Jitsi-Secret') !== $webhookSecret) {
                Log::warning('Invalid webhook secret');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Parse webhook data
            $eventType = $request->input('event', '');
            $recordingData = $request->input('recording', []);

            // Handle different event types
            switch ($eventType) {
                case 'recording.status.changed':
                    $this->handleRecordingStatusChanged($recordingData);
                    break;
                case 'recording.completed':
                    $this->handleRecordingCompleted($recordingData);
                    break;
                default:
                    Log::info('Unknown webhook event type: ' . $eventType);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle recording status changed event
     */
    private function handleRecordingStatusChanged($recordingData)
    {
        $roomName = $recordingData['name'] ?? null;
        $status = $recordingData['status'] ?? null;

        if (!$roomName) {
            Log::warning('No room name in recording data');
            return;
        }

        // Find the zoom_class by room name (stored in uuid or meeting_id)
        $zoomClass = zoom_classes::where('uuid', 'like', '%' . $roomName . '%')
            ->orWhere('meeting_id', 'like', '%' . $roomName . '%')
            ->first();

        if ($zoomClass) {
            $statusMap = [
                'on' => 'processing',
                'off' => 'completed',
                'failed' => 'failed',
            ];

            $zoomClass->recording_status = $statusMap[$status] ?? 'pending';
            $zoomClass->recording_started_at = $status === 'on' ? now() : $zoomClass->recording_started_at;
            $zoomClass->save();

            Log::info('Recording status updated', [
                'room' => $roomName,
                'status' => $zoomClass->recording_status,
            ]);
        }
    }

    /**
     * Handle recording completed event
     * Auto-save recording when it's ready
     */
    private function handleRecordingCompleted($recordingData)
    {
        $roomName = $recordingData['name'] ?? null;
        $recordingUrl = $recordingData['url'] ?? null;
        $duration = $recordingData['duration'] ?? null;
        $fileSize = $recordingData['size'] ?? null;

        if (!$roomName || !$recordingUrl) {
            Log::warning('Missing room name or recording URL');
            return;
        }

        // Find the zoom_class by room name
        $zoomClass = zoom_classes::where('uuid', 'like', '%' . $roomName . '%')
            ->orWhere('meeting_id', 'like', '%' . $roomName . '%')
            ->first();

        if (!$zoomClass) {
            Log::warning('Zoom class not found for room: ' . $roomName);
            return;
        }

        try {
            // Save recording to local storage
            $saveResult = $this->recordingStorage->saveRecording(
                $recordingUrl,
                $zoomClass->tutor_id,
                $zoomClass->student_id ?? 0,
                $zoomClass->topic_name ?? 'Recording',
                $zoomClass->meeting_id ?? $roomName
            );

            if ($saveResult['success']) {
                // Update zoom_class with recording info
                $zoomClass->recording_link = $saveResult['relative_path'];
                $zoomClass->recording_file_path = $saveResult['file_path'];
                $zoomClass->recording_status = 'completed';
                $zoomClass->recording_duration = $duration ?? null;
                $zoomClass->recording_file_size = $saveResult['file_size'] ?? $fileSize ?? null;
                $zoomClass->recording_completed_at = now();
                
                // Auto-finalize class
                $zoomClass->is_completed = 1;
                $zoomClass->status = 'Completed';
                $zoomClass->completed_at = Carbon::now();
                $zoomClass->save();

                // Update SlotBooking
                $slotupdate = SlotBooking::where('meeting_id', '=', $zoomClass->id)->first();
                if ($slotupdate) {
                    $slotupdate->is_class_scheduled = 2;
                    $slotupdate->update();
                }

                // Send Notification to student
                $tutor = tutorregistration::find($zoomClass->tutor_id);
                $tutorName = $tutor ? $tutor->name : 'Tutor';

                $notificationdata = new Notification();
                $notificationdata->alert_type = 7;
                $notificationdata->notification = 'Class has been completed and recording is now available.';
                $notificationdata->initiator_id = $zoomClass->tutor_id;
                $notificationdata->initiator_role = 2; // Tutor role
                $notificationdata->event_id = $zoomClass->id;
                $notificationdata->show_to_student = 1;
                $notificationdata->show_to_student_id = $zoomClass->student_id;
                $notificationdata->show_to_all_student = 0;
                $notificationdata->read_status = 0;
                $notificationdata->save();

                broadcast(new RealTimeMessage('$notification'));

                Log::info('Recording saved and class finalized automatically', [
                    'room' => $roomName,
                    'file_path' => $saveResult['relative_path'],
                ]);
            } else {
                // Fallback: Save URL if file save fails
                $zoomClass->recording_link = $recordingUrl;
                $zoomClass->recording_status = 'completed';
                $zoomClass->recording_completed_at = now();
                $zoomClass->save();

                Log::warning('Recording file save failed, saved URL instead', [
                    'error' => $saveResult['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process recording completion: ' . $e->getMessage());
            
            // Fallback: Save URL even if processing fails
            $zoomClass->recording_link = $recordingUrl;
            $zoomClass->recording_status = 'completed';
            $zoomClass->save();
        }
    }

    /**
     * Manual trigger to check and process recordings
     * Can be called via cron job or scheduled task
     */
    public function processPendingRecordings()
    {
        // This would be called periodically to check for pending recordings
        // and process them if webhooks are not configured
        Log::info('Processing pending recordings');
        
        // Implementation depends on Jitsi API availability
        // For now, this is a placeholder for future enhancement
    }
}

