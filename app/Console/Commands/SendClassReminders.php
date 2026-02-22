<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\democlasses;
use App\Models\studentprofile;
use App\Models\tutorprofile;
use App\Models\tutorregistration;
use App\Models\SlotBooking;
use App\Models\studentregistration;
use App\Models\zoom_classes;
use App\Models\subjects;
use App\Helpers\TimezoneHelper;
use App\Services\TwilioWhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        Log::info('SendClassReminders command started');

        $now = Carbon::now('UTC');

        // ================= DEMO CLASSES =================
        $classes = democlasses::where('status', 3)
            ->whereNotNull('slot_confirmed')
            ->whereNull('reminder_sent_at')
            ->whereRaw(
                'TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), slot_confirmed) BETWEEN 25 AND 35'
            )
            ->get();


        Log::info('Demo classes fetched', ['count' => $classes->count()]); // LOG

        foreach ($classes as $class) {

            Log::info('Processing demo class', [
                'class_id' => $class->id,
                'slot_confirmed' => $class->slot_confirmed,
            ]);

            $student = studentprofile::where('student_id', $class->student_id)->first();
            $tutor   = tutorprofile::where('tutor_id', $class->slot_confirmed_by)->first();

            if (!$student || !$tutor) {
                Log::warning('Demo class skipped - missing student or tutor', [ // LOG
                    'class_id' => $class->id,
                    'student_found' => (bool) $student,
                    'tutor_found' => (bool) $tutor,
                ]);
                continue;
            }

            $subject = subjects::find($class->subject_id);
            $meetingLink = $class->demo_link ?? "https://mychoicetutor.com/waiting-room";

            $studentReg = studentregistration::find($class->student_id);
            $tutorReg   = tutorregistration::find($class->slot_confirmed_by);
            $formattedForStudent = TimezoneHelper::formatInUserTz($class->slot_confirmed, 'd M Y h:i A', 'UTC', $studentReg);
            $formattedForTutor   = TimezoneHelper::formatInUserTz($class->slot_confirmed, 'd M Y h:i A', 'UTC', $tutorReg);

            Log::info('Demo reminder claimed', ['class_id' => $class->id]); // LOG

            $sentStudent = false;
            $sentTutor   = false;
            if (!empty($student->mobile)) {
                try {
                    Log::info('Sending demo reminder to student', [ // LOG
                        'class_id' => $class->id,
                        'mobile' => $student->mobile,
                    ]);

                    $sentStudent = $this->whatsApp->sendMessage(
                          $student->mobile,
                        [
                            $student->name ?? 'Student',
                            $subject->name ?? 'Demo Class',
                            $formattedForStudent,
                            $tutor->name ?? 'Tutor',
                            $meetingLink,
                        ],
                        1644
                    );
                } catch (\Exception $e) {
                    Log::error('Student demo reminder failed', [ // LOG
                        'class_id' => $class->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (!empty($tutor->mobile)) {
                try {
                    Log::info('Sending demo reminder to tutor', [ // LOG
                        'class_id' => $class->id,
                        'mobile' => $tutor->mobile,
                    ]);

                    $sentTutor = $this->whatsApp->sendMessage(
                          $tutor->mobile,
                        [
                            $tutor->name ?? 'Tutor',
                            $subject->name ?? 'Demo Class',
                            $formattedForTutor,
                            $student->name ?? 'Student',
                            $meetingLink,
                        ],
                        1642
                    );
                } catch (\Exception $e) {
                    Log::error('Tutor demo reminder failed', [ // LOG
                        'class_id' => $class->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            if ($sentStudent || $sentTutor) {
                democlasses::where('id', $class->id)
                    ->update(['reminder_sent_at' => now('UTC')]);

                Log::info('Demo reminder marked as sent', [
                    'class_id' => $class->id,
                ]);
            } else {
                Log::warning('Demo reminder NOT marked (WhatsApp failed)', [
                    'class_id' => $class->id,
                ]);
            }
        }
        // ================= REGULAR CLASSES (SlotBooking) =================


        $slotBookings = SlotBooking::where('status', 1)
            ->whereNotNull('student_id')
            ->whereNotNull('tutor_id')
            ->whereNotNull('meeting_id')
            ->whereNull('reminder_sent_at')
            ->where('is_class_scheduled', 1)
            ->whereRaw(
                'TIMESTAMPDIFF(
                MINUTE,
                UTC_TIMESTAMP(),
                STR_TO_DATE(CONCAT(date, " ", slot), "%Y-%m-%d %H:%i:%s")
            ) BETWEEN 25 AND 35'
            )->get();
        // dd($slotBookings);

        Log::info('Regular classes fetched', [
            'count' => $slotBookings->count()
        ]);

        $templateIdRegularStudent = 1643;
        $templateIdRegularTutor   = 1641;

        foreach ($slotBookings as $booking) {

            Log::info('Processing regular class', [
                'booking_id' => $booking->id,
                'slot_time'  => $booking->date . ' ' . $booking->slot,
            ]);

            $studentProfile = studentprofile::where('student_id', $booking->student_id)->first();
            $tutorReg       = tutorregistration::find($booking->tutor_id);
            $subject        = subjects::find($booking->subject_id);
            $zoomClass      = zoom_classes::find($booking->meeting_id);

            if (!$studentProfile || !$tutorReg) {
                Log::warning('Regular class skipped - missing student or tutor', [
                    'booking_id' => $booking->id
                ]);
                continue;
            }

            $meetingLink = $zoomClass->join_url
                ?? $zoomClass->start_url
                ?? "https://mychoicetutor.com/waiting-room";

            $studentReg = studentregistration::find($booking->student_id);
            $formattedForStudent = TimezoneHelper::formatInUserTz(
                $booking->date . ' ' . $booking->slot,
                'd M Y h:i A',
                'UTC',
                $studentReg
            );
            $formattedForTutor = TimezoneHelper::formatInUserTz(
                $booking->date . ' ' . $booking->slot,
                'd M Y h:i A',
                'UTC',
                $tutorReg
            );

            $sentStudent = false;
            $sentTutor   = false;
            // ================= SEND TO STUDENT =================
            if (!empty($studentProfile->mobile)) {
                try {
                    $sentStudent = $this->whatsApp->sendMessage(
                         $studentProfile->mobile,
                        [
                            $studentProfile->name ?? 'Student',
                            $subject->name ?? 'Class',
                            $formattedForStudent,
                            // $tutorReg->name ?? 'Tutor',
                            $meetingLink,
                        ],
                        $templateIdRegularStudent
                    );
                } catch (\Exception $e) {
                    Log::error('Student regular reminder failed', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // ================= SEND TO TUTOR =================
            if (!empty($tutorReg->mobile)) {
                try {
                    $sentTutor = $this->whatsApp->sendMessage(
                         $tutorReg->mobile,
                        [
                            $tutorReg->name ?? 'Tutor',
                            $subject->name ?? 'Class',
                            $formattedForTutor,
                            $studentProfile->name ?? 'Student',
                            $meetingLink,
                        ],
                        $templateIdRegularTutor
                    );
                } catch (\Exception $e) {
                    Log::error('Tutor regular reminder failed', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($sentStudent || $sentTutor) {
                SlotBooking::where('id', $booking->id)
                    ->update(['reminder_sent_at' => now('UTC')]);

                Log::info('Regular reminder marked as sent', [
                    'booking_id' => $booking->id,
                ]);
            } else {
                Log::warning('Regular reminder NOT marked (WhatsApp failed)', [
                    'booking_id' => $booking->id,
                ]);
            }
        }

        Log::info('SendClassReminders command finished'); // LOG

        $this->info("30-minute reminders sent successfully.");
    }
}
