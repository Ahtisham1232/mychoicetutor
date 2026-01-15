<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\democlasses;
use App\Models\studentprofile;
use App\Models\tutorprofile;
use App\Models\tutorregistration;
use App\Models\SlotBooking;
use App\Models\zoom_classes;
use App\Models\subjects;
use App\Services\TwilioWhatsAppService;
use Carbon\Carbon;

class SendClassReminders extends Command
{
    protected $signature = 'classes:send-reminders';
    protected $description = 'Send WhatsApp reminders 30 minutes before demo classes and regular classes';

    protected $whatsApp;

    public function __construct(TwilioWhatsAppService $whatsApp)
    {
        parent::__construct();
        $this->whatsApp = $whatsApp;
    }

    public function handle()
    {
        // Set timezone based on environment variable
        $timezone = env('APP_TIMEZONE', 'UTC');

        // Map common timezone names to proper timezone identifiers
        $timezoneMap = [
            'pakistan' => 'Asia/Karachi',
            'england' => 'Europe/London',
            'london' => 'Europe/London',
            'karachi' => 'Asia/Karachi',
        ];

        $timezoneIdentifier = $timezoneMap[strtolower($timezone)] ?? $timezone;

        // Set the timezone for all Carbon operations in this command
        $now = Carbon::now($timezoneIdentifier);

        // Target = classes starting 30 minutes from now
        $targetTime = $now->copy()->addMinutes(30);

        $classes = democlasses::where('status', 3) // confirmed
            ->whereNotNull('slot_confirmed')
            ->whereBetween('slot_confirmed', [
                $targetTime->copy()->subMinute(5), // small window
                $targetTime->copy()->addMinute(5)
            ])
            ->get();

        // Process demo classes
        foreach ($classes as $class) {
            $student = studentprofile::where('student_id', $class->student_id)->first();
            $tutor   = tutorprofile::where('tutor_id', $class->slot_confirmed_by)->first();

            if (!$student || !$tutor) continue;

            $subject = subjects::find($class->subject_id);
            $meetingLink = $class->demo_link ?? "https://mychoicetutor.com/waiting-room";
            
            // Use template for demo class reminders
            $templateIdDemo = 1644; // TODO: Replace with actual template ID for demo class reminder
            $templateIdDemoTutor = 1642; // TODO: Replace with actual template ID for demo class reminder
            
            $classDateTime = Carbon::parse($class->slot_confirmed, $timezoneIdentifier);
            $formattedDateTime = $classDateTime->format('d M Y h:i A');

            // Send to student
            if (!empty($student->mobile)) {
                try {
                    $studentNumber = "+92" . ltrim($student->mobile, "0");
                    $bodyVariablesStudent = [
                        $student->name ?? 'Student',
                        $subject->name ?? 'Demo Class',
                        $formattedDateTime,
                        $tutor->name ?? 'Tutor',
                        $meetingLink,
                    ];
                    $this->whatsApp->sendMessage($studentNumber, $bodyVariablesStudent, $templateIdDemo);
                } catch (\Exception $e) {
                    $this->error('Failed to send reminder to student: ' . $e->getMessage());
                }
            }

            // Send to tutor
            if (!empty($tutor->mobile)) {
                try {
                    $tutorNumber = "+92" . ltrim($tutor->mobile, "0");
                    $bodyVariablesTutor = [
                        $tutor->name ?? 'Tutor',
                        $subject->name ?? 'Demo Class',
                        $formattedDateTime,
                        $student->name ?? 'Student',
                        $meetingLink,
                    ];
                    $this->whatsApp->sendMessage($tutorNumber, $bodyVariablesTutor, $templateIdDemoTutor);
                } catch (\Exception $e) {
                    $this->error('Failed to send reminder to tutor: ' . $e->getMessage());
                }
            }
        }

        // Process regular classes (SlotBooking)
        // Get slot bookings that are scheduled and starting in 30 minutes
        $targetDate = $targetTime->format('Y-m-d');
        $targetSlotTime = $targetTime->format('H:i');
        
        // Small window for slot matching (within 5 minutes)
        $slotBookings = SlotBooking::where('status', 1) // confirmed bookings
            ->where('date', $targetDate)
            ->whereNotNull('student_id')
            ->whereNotNull('meeting_id')
            ->get()
            ->filter(function ($booking) use ($targetTime) {
                // Parse the slot time and check if it's within the window
                try {
                    $slotDateTime = Carbon::parse($booking->date . ' ' . $booking->slot, $timezoneIdentifier);
                    return $slotDateTime->between(
                        $targetTime->copy()->subMinute(5),
                        $targetTime->copy()->addMinute(5)
                    );
                } catch (\Exception $e) {
                    return false;
                }
            });

        $templateIdRegular = 1643; // TODO: Replace with actual template ID for regular class reminder
        $templateIdRegularTutor = 1641; // TODO: Replace with actual template ID for regular class reminder

        foreach ($slotBookings as $booking) {
            $studentProfile = studentprofile::where('student_id', $booking->student_id)->first();
            $tutorReg = tutorregistration::find($booking->tutor_id);
            $subject = subjects::find($booking->subject_id);
            $zoomClass = zoom_classes::find($booking->meeting_id);

            if (!$studentProfile || !$tutorReg) continue;

            $meetingLink = $zoomClass->join_url ?? $zoomClass->start_url ?? "https://mychoicetutor.com/waiting-room";
            $classDateTime = Carbon::parse($booking->date . ' ' . $booking->slot, $timezoneIdentifier);
            $formattedDateTime = $classDateTime->format('d M Y h:i A');

            // Send to student
            if (!empty($studentProfile->mobile)) {
                try {
                    $studentNumber = "+92" . ltrim($studentProfile->mobile, "0");
                    $bodyVariablesStudent = [
                        $studentProfile->name ?? 'Student',
                        $subject->name ?? 'Class',
                        $formattedDateTime,
                        $tutorReg->name ?? 'Tutor',
                        $meetingLink,
                    ];
                    $this->whatsApp->sendMessage($studentNumber, $bodyVariablesStudent, $templateIdRegular);
                } catch (\Exception $e) {
                    $this->error('Failed to send reminder to student: ' . $e->getMessage());
                }
            }

            // Send to tutor
            if (!empty($tutorReg->mobile)) {
                try {
                    $tutorNumber = "+92" . ltrim($tutorReg->mobile, "0");
                    $bodyVariablesTutor = [
                        $tutorReg->name ?? 'Tutor',
                        $subject->name ?? 'Class',
                        $formattedDateTime,
                        $studentProfile->name ?? 'Student',
                        $meetingLink,
                    ];
                    $this->whatsApp->sendMessage($tutorNumber, $bodyVariablesTutor, $templateIdRegularTutor);
                } catch (\Exception $e) {
                    $this->error('Failed to send reminder to tutor: ' . $e->getMessage());
                }
            }
        }

        $this->info("30-minute reminders sent successfully.");
    }
}
