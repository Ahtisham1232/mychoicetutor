<?php

namespace App\Http\Controllers;

use App\Events\RealTimeMessage;
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\batches;
use App\Models\democlasses;
use App\Models\Notification;
use App\Models\payments\paymentstudents;
use App\Models\SlotBooking;
use App\Models\studentprofile;
use App\Models\studentregistration;
use App\Models\subjects;
use App\Models\tutorregistration;
use App\Models\zoom_classes;
use DateTime;
use DateTimeZone;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use App\Helpers\TimezoneHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Helpers\CommonHelper;
use App\Services\TwilioWhatsAppService;
use App\Services\JitsiMeetService;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{

    public function classlist()
    {
        try {
            // Get scheduled classes (with meeting_id and zoom_classes)
            $scheduledClasses = zoom_classes::select(
                'zoom_classes.*',
                'zoom_classes.id as liveclass_id',
                'studentregistrations.name as studentname',
                'subjects.name as subjectname',
                'classes.name as classname',
                'slot_bookings.date as slotdate',
                'slot_bookings.slot as slottime',
                'slot_bookings.id as slot_id'
            )
                ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
                ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
                ->join('paymentstudents', 'paymentstudents.id', 'slot_bookings.class_schedule_id')
                ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
                ->join('classes', 'classes.id', 'paymentstudents.class_id')
                ->where('zoom_classes.is_completed', 0)
                ->where('zoom_classes.is_active', 1)
                ->where('zoom_classes.tutor_id', session('userid')->id)
                ->get();

            // Get booked slots that are not yet scheduled (status=1, no meeting_id)
            $bookedSlots = SlotBooking::select(
                'slot_bookings.*',
                'slot_bookings.id as liveclass_id',
                'studentregistrations.name as studentname',
                'subjects.name as subjectname',
                'classes.name as classname',
                'slot_bookings.date as slotdate',
                'slot_bookings.slot as slottime',
                'slot_bookings.id as slot_id'
            )
                ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
                ->join('paymentstudents', 'paymentstudents.id', 'slot_bookings.class_schedule_id')
                ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
                ->join('classes', 'classes.id', 'paymentstudents.class_id')
                ->where('slot_bookings.tutor_id', session('userid')->id)
                ->where('slot_bookings.status', 1) // Confirmed bookings
                ->whereNull('slot_bookings.meeting_id') // Not yet scheduled
                ->whereDate('slot_bookings.date', '>=', Carbon::now('UTC')->toDateString()) // Upcoming slots
                ->get();

            // Merge both collections
            $liveclasses = $scheduledClasses->concat($bookedSlots)->sortByDesc('slotdate')->values();

            // Convert slot dates and times from UTC to user (tutor) timezone for display
            $liveclasses->each(function ($class) {
                $class->slotdate = TimezoneHelper::dateToUserTz($class->slotdate);
                $class->slottime = TimezoneHelper::timeToUserTz($class->slottime);
            });

            $classes = (new CommonController)->classes();
            return view('tutor.liveclasses', compact('liveclasses', 'classes'));
        } catch (\Exception $e) {
            Log::error('Error fetching class list: ' . $e->getMessage());
            return back()->with('fail', 'Error loading scheduled classes. Please try again.');
        }
    }

    public function scheduleclass(Request $request, TwilioWhatsAppService $whatsApp)
    {

        try {
            if (session('userid')->is_active == 0) {
                return back()->with('fail', 'Sorry! your Account is not verified. Please contact administrator');
            }

            $request->validate([
                'classpassword' => 'required',
                'classslotid' => 'required|exists:slot_bookings,id',
            ]);

            $classdata = SlotBooking::select('*')->where('id', $request->classslotid)->first();

            if (!$classdata) {
                return back()->with('fail', 'Slot booking not found.');
            }

            // Only allow scheduling when admin has approved the enrollment (slot status = 1)
            if ((int) $classdata->status !== 1) {
                return back()->with('fail', 'You can only schedule a class after the enrollment request has been approved by admin. This slot is still pending approval.');
            }

            $studentpayment = paymentstudents::select('*')->where('id', $classdata->class_schedule_id)->first();
            if (!$studentpayment) {
                return back()->with('fail', 'Payment record not found.');
            }

            $student = studentregistration::find($studentpayment->student_id);
            if (!$student) {
                return back()->with('fail', 'Student not found.');
            }

            $tutor = tutorregistration::find($studentpayment->tutor_id);
            if (!$tutor) {
                return back()->with('fail', 'Tutor not found.');
            }

            // Get the actual date and time from slot booking
            $slotDate = Carbon::parse($classdata->date);
            $slotTime = Carbon::parse($classdata->slot);
            $classstarttime = $slotDate->copy()->setTime($slotTime->hour, $slotTime->minute);
            $classduration = 60;
            $classpassword = $request->input('classpassword');

            // Get subject name for meeting room
            $subject = subjects::find($classdata->subject_id);
            $subjectName = $subject ? $subject->name : 'Live Class';

            // Use Jitsi Meet instead of Google Meet (FREE, no account needed per user)
            $jitsiService = app(JitsiMeetService::class);
            $meeting = $jitsiService->createClassMeeting(
                $tutor->id,
                $student->id,
                $subjectName,
                $classpassword
            );
            if ($meeting['success']) {
                try {
                    $data = new zoom_classes(); // Using zoom_classes table but with Jitsi Meet links

                    $data->tutor_id = session('userid')->id;
                    $data->batch_id = $student->id;
                    $data->student_id = $student->id;
                    $data->uuid = $meeting['room_name']; // Store room name as UUID
                    $data->meeting_id = $meeting['meeting_url']; // Store meeting URL as meeting_id
                    $data->host_id = (string)$tutor->id;
                    $data->host_email = $tutor->email;
                    $data->topic_id = $classdata->subject_id ?? 0;
                    $data->topic_name = $subjectName;
                    $data->type = 2; // 2 = scheduled meeting
                    $data->status = 'confirmed';
                    $data->start_time = $classstarttime->format(DateTime::RFC3339);
                    $data->duration = $classduration;
                    $data->timezone = 'Asia/Kolkata';
                    $data->agenda = 'Live Class for ' . $student->name . ' by ' . $tutor->name;
                    $data->start_url = $meeting['start_url'];
                    $data->join_url = $meeting['join_url'];
                    $data->password = $classpassword ?? '';
                    $data->h323_password = $classpassword ?? '';
                    $data->pstn_password = $classpassword ?? '';
                    $data->encrypted_password = $classpassword ?? '';

                    $res = $data->save();
                    if ($res) {
                        try {
                            // Retrieve the id after saving
                            $lastInsertedId = $data->id;

                            // Update SlotBooking with the meeting_id
                            $slotbooking = SlotBooking::find($request->classslotid);
                            if ($slotbooking) {
                                $slotbooking->is_class_scheduled = 1;
                                $slotbooking->meeting_id = $lastInsertedId;
                                $slotbooking->update();
                            }

                            // Send notification
                            try {
                                $notificationdata = new Notification();
                                $notificationdata->alert_type = 7;
                                $notificationdata->notification = "Your slot has been confirmed";
                                $notificationdata->initiator_id = session('userid')->id;
                                $notificationdata->initiator_role = session('userid')->role_id;
                                $notificationdata->event_id = $request->classslotid;
                                $notificationdata->show_to_student = 1;
                                $notificationdata->show_to_student_id = $student->id;
                                $notificationdata->read_status = 0;
                                $notificationdata->save();
                                broadcast(new RealTimeMessage($notificationdata));
                            } catch (\Exception $e) {
                                Log::error('Failed to send notification of regular class: ' . $e->getMessage());
                            }

                            // =======================
                            // WhatsApp Message (Non-blocking) Student
                            // =======================
                            if (!empty($student->mobile)) {
                                try {
                                    $templateIdClassConfirm = 1630;

                                    $classDateTimeFormatted = TimezoneHelper::formatInUserTz(
                                        $classdata->date . ' ' . $classdata->slot,
                                        'd M Y',
                                        'UTC',
                                        $student
                                    );
                                    $classTimeFormatted = TimezoneHelper::formatInUserTz(
                                        $classdata->date . ' ' . $classdata->slot,
                                        'h:i A',
                                        'UTC',
                                        $student
                                    );
                                    $studentNumber = $student->mobile;

                                    $bodyVariablesStudent = [
                                        $student->name,
                                        $subjectName,
                                        $tutor->name,
                                        $classDateTimeFormatted,
                                        $classTimeFormatted,
                                    ];

                                    $sent = $whatsApp->sendMessage(
                                        $studentNumber,
                                        $bodyVariablesStudent,
                                        $templateIdClassConfirm
                                    );
                                    if ($sent) {
                                        Log::info('WHATSAPP SENT SUCCESSFULLY for democlass in google calender controller', [
                                            'subject_name' => $subjectName,
                                            'student_mobile' => $studentNumber,
                                            'student_name' => $student->name,
                                        ]);
                                    } else {
                                        Log::warning('WHATSAPP FAILED Google Calender Controller', [
                                            'subject_name' => $subjectName,
                                            'student_mobile' => $studentNumber,
                                            'student_name' => $student->name,

                                        ]);
                                    }
                                } catch (\Exception $e) {
                                    // IMPORTANT: Do NOT stop execution
                                    Log::error(
                                        'WhatsApp send failed for regular class scheduling: ' . $e->getMessage(),
                                        [
                                            'student_id' => $student->id,
                                            'mobile' => $student->mobile
                                        ]
                                    );
                                }
                            }


                            return redirect()->to('/tutor/getclasslist')->with('success', 'Class scheduled successfully!');
                        } catch (\Exception $e) {
                            Log::error('Error updating slot booking: ' . $e->getMessage());
                            return back()->with('fail', 'Class created but failed to update slot. Please contact support.');
                        }
                    } else {
                        return back()->with('fail', 'Failed to save class. Please try again.');
                    }
                } catch (\Exception $e) {
                    Log::error('Error saving zoom class: ' . $e->getMessage());
                    return back()->with('fail', 'Failed to save class. Please try again.');
                }
            } else {
                Log::error('Jitsi Meet creation failed: ' . ($meeting['error'] ?? 'Unknown error'));
                return back()->with('fail', 'Failed to create meeting. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error in scheduleclass: ' . $e->getMessage());
            return back()->with('fail', 'An error occurred. Please try again later.');
        }
    }
    public function oauthCallback(Request $request)
    {
        $code = $request->get('code');

        if ($code) {
            $client = new Google_Client();
            $client->setClientId('676549087074-1ueuq9ch025rdru9tu8043qfg8o54cso.apps.googleusercontent.com');
            $client->setClientSecret('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');
            $client->setRedirectUri('https://mychoicetutor.com/tutor/dashboard/oauth2callback');
            $client->addScope('https://www.googleapis.com/auth/calendar');
            $client->authenticate($code);

            $accessToken = $client->getAccessToken();

            // Store the access token in the session
            $request->session()->put('access_token', $accessToken);
            // dd($accessToken);

            // Redirect to the create event page
            return redirect()->route('tutor.tutorslots');
        }

        return redirect()->route('error')->with('message', 'Authentication failed.');
    }

    public function democonfirm(Request $request, TwilioWhatsAppService $whatsApp)
    {
        try {
            $request->validate([
                'slot' => 'required',
            ]);

            $demodata = democlasses::select('*')->where('id', $request->confirmid)->first();
            if (!$demodata) {
                return back()->with('fail', 'Demo class not found.');
            }

            $demostudent = studentprofile::select('*')->where('student_id', $demodata->student_id)->first();
            if (!$demostudent) {
                return back()->with('fail', 'Student profile not found.');
            }

            $classstarttime = $request->input('slot');
            $classduration = 60;
            $classpassword = $request->input('demopassword') ?? '12345678';

            $subjectdata = subjects::select('*')->where('id', $demodata->subject_id)->first();
            $subjectName = $subjectdata ? $subjectdata->name : 'Demo Class';

            // Use Jitsi Meet instead of Google Meet (FREE, no account needed per user)
            $jitsiService = app(JitsiMeetService::class);
            $tutorId = session('userid')->id;
            $studentId = $demodata->student_id;

            $meeting = $jitsiService->createClassMeeting(
                $tutorId,
                $studentId,
                $subjectName,
                $classpassword
            );

            if ($meeting['success']) {
                $dcnf = democlasses::find($request->confirmid);
                // slot_confirmed is already in UTC from the form, so we can store it directly
                $dcnf->slot_confirmed = $request->slot;
                $dcnf->slot_confirmed_at = Carbon::now();
                $dcnf->slot_confirmed_by = session('userid')->id;
                $dcnf->demo_link = $meeting['meeting_url'];
                $dcnf->remarks = $request->demoremarks;
                $dcnf->status = 3;
                $res = $dcnf->save();

                try {
                    $details = [
                        'name' => $demostudent->name,
                        'confirmed_slot' => $request->slot,
                        'tutor_name' => session('userid')->name,
                        'mailtype' => 3,
                    ];

                    try {
                        Mail::to($demostudent->email)->send(new SendMail($details));
                    } catch (\Exception $e) {
                    }
                } catch (\Throwable $e) {
                    Log::error('Mail failed: ' . $e->getMessage());
                }

                if ($res) {
                    $notificationdata = new Notification();
                    $notificationdata->alert_type = 2;
                    $notificationdata->notification = 'Trial Class Confirmed By ' . session('userid')->name;
                    $notificationdata->initiator_id = session('userid')->id;
                    $notificationdata->initiator_role = session('userid')->role_id;
                    $notificationdata->event_id = $dcnf->id;
                    $notificationdata->show_to_admin = 1;
                    $notificationdata->show_to_all_admin = 1;
                    $notificationdata->show_to_student = 1;
                    $notificationdata->show_to_student_id = $demodata->student_id;
                    $notificationdata->read_status = 0;
                    $notified = $notificationdata->save();

                    broadcast(new RealTimeMessage('$notification'));
                    if (!empty($demostudent->mobile)) {
                        try {
                            $templateIdDemoConfirm = 1645;
                            $studentNumber = $demostudent->mobile;
                            $bodyVariablesStudent = [
                                $demostudent->name,
                                $subjectName,
                                TimezoneHelper::formatInUserTz($dcnf->slot_confirmed, 'd M Y h:i A', 'UTC', $demostudent),
                                session('userid')->name,
                                $meeting['meeting_url'],
                            ];
                            $sent = $whatsApp->sendMessage($studentNumber, $bodyVariablesStudent, $templateIdDemoConfirm);
                            if ($sent) {
                                Log::info('WHATSAPP SENT SUCCESSFULLY for democlass in google calender controller', [
                                    'demo_id' => $dcnf->id,
                                    'mobile' => $studentNumber,
                                ]);
                            } else {
                                Log::warning('WHATSAPP FAILED', [
                                    'demo_id' => $dcnf->id,
                                    'mobile' => $studentNumber,
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('WhatsApp send failed for demo confirmation: ' . $e->getMessage());
                        }
                    }

                    return redirect()->to('/tutor/demolist')->with('success', 'Trial confirmed successfully');
                } else {
                    return back()->with('fail', 'Something went wrong. Please try again later');
                }
            } else {
                Log::error('Jitsi Meet creation failed for demo: ' . ($meeting['error'] ?? 'Unknown error'));
                return back()->with('fail', 'Failed to create meeting. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error in democonfirm: ' . $e->getMessage());
            return back()->with('fail', 'An error occurred. Please try again later.');
        }
    }

    public function demoend(Request $request)
    {

        // dd($request->demoendid);
        $dcnf = democlasses::find($request->demoendid);
        if (!$dcnf) {
        return back()->with('fail', 'Class record not found.');
        }

        $dcnf->remarks = $request->demoendremarks;
        $dcnf->status = 4;

        $res = $dcnf->save();

        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 2;
            $notificationdata->notification = 'Trial Class Completed By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $dcnf->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            $notificationdata->show_to_admin = 1;
            // $notificationdata->show_to_admin_id = $request->receiver_id;
            $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
            // $notificationdata->show_to_tutor = 1;
            // $notificationdata->show_to_tutor_id = $demo->tutor_id;
            // $notificationdata->show_to_all_tutor = 0;
            // }
            // Sending to student
            // if($request->receiver_role_id == 3){
            $notificationdata->show_to_student = 1;
            $notificationdata->show_to_student_id = $dcnf->student_id;
            //     // $notificationdata->show_to_all_student = 0;
            // }
            // // Sending to parent
            // if($request->receiver_role_id == 3){
            //     $notificationdata->show_to_parent = 1;
            //     $notificationdata->show_to_parent_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_parent = 0;
            // }
            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();
            broadcast(new RealTimeMessage('$notification'));

            return back()->with('success', 'Trial ended successfully');
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }
    public function oauth2callbackdemo(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId('676549087074-1ueuq9ch025rdru9tu8043qfg8o54cso.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('https://mychoicetutor.com/tutor/dashboard/oauth2callback');
        $client->addScope('https://www.googleapis.com/auth/calendar');

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            if (!isset($token['error'])) {
                $request->session()->put('access_token', $token);
                return redirect()->route('tutor.demolist')->with('success', 'Trial confirmed successfully');
            }
        }

        return redirect()->route('tutor.demolist')->with('fail', 'Something went wrong. Please try again later');
    }
}
