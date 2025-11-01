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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Services\TwilioWhatsAppService;
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
                ->whereDate('slot_bookings.date', '>=', Carbon::today()) // Upcoming slots
                ->get();

            // Merge both collections
            $liveclasses = $scheduledClasses->concat($bookedSlots)->sortByDesc('slotdate')->values();

            $classes = (new CommonController)->classes();
            return view('tutor.liveclasses', compact('liveclasses', 'classes'));
        } catch (\Exception $e) {
            Log::error('Error fetching class list: ' . $e->getMessage());
            return back()->with('fail', 'Error loading scheduled classes. Please try again.');
        }
    }
   
    public function scheduleclass(Request $request)
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
        // $slotbooking->class_schedule_id = $studentpayment->id;

        // try {
            // Initialize the Google API client with OAuth 2.0 credentials
            $client = new Google_Client();
            $client->setClientId('676549087074-1ueuq9ch025rdru9tu8043qfg8o54cso.apps.googleusercontent.com');
            $client->setClientSecret('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');
            $client->setRedirectUri('https://mychoicetutor.com/tutor/dashboard/oauth2callback');
            $client->addScope('https://www.googleapis.com/auth/calendar');
            $client->setAccessType('offline'); // Allows access when the user is not present

            // Check if the user is already authenticated
            if (!$request->session()->has('access_token')) {
                // Redirect the user to Google's OAuth 2.0 consent screen
                $authUrl = $client->createAuthUrl();
                return redirect()->away($authUrl);
            }
            $client->setAccessToken($request->session()->get('access_token'));

            $service = new Google_Service_Calendar($client);

            // Get the actual date and time from slot booking
            $slotDate = Carbon::parse($classdata->date);
            $slotTime = Carbon::parse($classdata->slot);
            $classstarttime = $slotDate->copy()->setTime($slotTime->hour, $slotTime->minute);
            $classduration = 60;
            $classpassword = $request->input('classpassword');

            $event = new Google_Service_Calendar_Event([
                'summary' => 'Live Class By ' . $tutor->name . '',
                'description' => 'Live for student : ' . $student->name . ', by tutor : ' . $tutor->name,
                'start' => [
                    'dateTime' => $classstarttime->format(DateTime::RFC3339),
                    'timeZone' => 'Asia/Kolkata',
                ],
                'end' => [
                    'dateTime' => $classstarttime->copy()->addMinutes($classduration)->format(DateTime::RFC3339),
                    'timeZone' => 'Asia/Kolkata',
                ],
                'conferenceData' => [
                    'createRequest' => [
                        'requestId' => uniqid(),
                    ],
                    'password' => $classpassword,
                ],
                'attendees' => [['email' => $student->email]],
            ]);
            $calendarId = 'primary';

            $response = null;
            try {
                // Code that makes the API request to Google Calendar
                $response = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
            } catch (\Google\Service\Exception $e) {
                // Handle Google API exceptions
                Log::error('Google Calendar API error: ' . $e->getMessage());
                $authUrl = $client->createAuthUrl();
                return redirect()->away($authUrl)->with('fail', 'Google Calendar authentication required.');
            } catch (\Exception $e) {
                // Handle other exceptions
                Log::error('Error creating Google Calendar event: ' . $e->getMessage());
                return back()->with('fail', 'Failed to create calendar event. Please try again.');
            }

            if ($response && $response->status == 'confirmed') {
                try {
                    $data = new zoom_classes(); // zoom_classes -> Currently we are using gmeet to host meeting

                    $data->tutor_id = session('userid')->id;
                    $data->batch_id = $student->id;
                    $data->student_id = $student->id;
                    $data->uuid = $response->id;
                    $data->meeting_id = $response->hangoutLink;
                    $data->host_id = 'info@sofabespoke.co.uk';
                    $data->host_email = 'info@sofabespoke.co.uk';
                    $data->topic_id = 0;
                    $data->topic_name = 'On Demand';
                    $data->type = 2;
                    $data->status = $response->status;
                    $data->start_time = $classstarttime->format(DateTime::RFC3339);
                    $data->duration = $classduration;
                    $data->timezone = 'Asia/Kolkata';
                    $data->agenda = $response->description ?? 'Live Class';
                    $data->start_url = $response->hangoutLink ?? '';
                    $data->join_url = $response->hangoutLink ?? '';
                    $data->password = $classpassword;
                    $data->h323_password = $classpassword;
                    $data->pstn_password = $classpassword;
                    $data->encrypted_password = $classpassword;

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
                                Log::error('Failed to send notification: ' . $e->getMessage());
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
            return back()->with('fail', 'Failed to confirm calendar event. Please try again.');
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
        $request->validate([
            'slot' => 'required',
        ]);

        $demodata = democlasses::select('*')->where('id', $request->confirmid)->first();
        $demostudent = studentprofile::select('*')->where('student_id', $demodata->student_id)->first();
        $client = new Google_Client();
        $client->setClientId('676549087074-1ueuq9ch025rdru9tu8043qfg8o54cso.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('https://mychoicetutor.com/tutor/dashboard/oauth2callback');
        $client->addScope('https://www.googleapis.com/auth/calendar');
        $client->setAccessType('offline');

        if (!$request->session()->has('access_token')) {
            $authUrl = $client->createAuthUrl();
            return redirect()->away($authUrl);
        }

        $client->setAccessToken($request->session()->get('access_token'));
        $service = new Google_Service_Calendar($client);

        $classstarttime = $request->input('slot');
        $classduration = 60;
        $classpassword = '12345678';
        $attendees[] = ['email' => $demostudent->email];

        $subjectdata = subjects::select('*')->where('id', $demodata->subject_id)->first();
        $batchdata = batches::select('*')->where('id', $request->batchid)->first();

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Demo Class(' . $subjectdata->name . ')',
            'description' => $subjectdata->name,
            'start' => [
                'dateTime' => date('c', strtotime($classstarttime)),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => date('c', strtotime($classstarttime . ' + ' . $classduration . ' minutes')),
                'timeZone' => 'Asia/Kolkata',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid(),
                ],
                'password' => $classpassword,
            ],
            'attendees' => $attendees,
        ]);

        $calendarId = 'primary';
        $response = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

        if ($response->status == 'confirmed') {
            $dcnf = democlasses::find($request->confirmid);
            $dcnf->slot_confirmed = $request->slot;
            $dcnf->slot_confirmed_at = Carbon::now();
            $dcnf->slot_confirmed_by = session('userid')->id;
            $dcnf->demo_link = $response->hangoutLink;
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
                    // If meeting link is available, use it; otherwise fallback
                    $meetingLink = $response->hangoutLink ?? null;

                    $message = "👋 Hello {$demostudent->name},\n\n".
                            "Your trial/demo class has been confirmed ✅\n".
                            "Subject: {$subjectdata->name}\n".
                            "Date/Time: " . date('d M Y h:i A', strtotime($classstarttime)) . "\n".
                            "Tutor: " . session('userid')->name . "\n";

                    if ($meetingLink) {
                        $message .= "Meeting Link: {$meetingLink}\n\n";
                    } else {
                        $message .= "Meeting link will be shared shortly. 📩\n\n";
                    }

                    $message .= "Please be on time.";

                    $to = "+92" . ltrim($demostudent->mobile, "0"); // format to E.164
                    $whatsApp->sendMessage($to, $message);
                }


                return redirect()->to('/tutor/demolist')->with('success', 'Trial confirmed successfully');
            } else {
                return back()->with('fail', 'Something went wrong. Please try again later');
            }
        } else {
            return response()->json(['error' => 'Failed to confirm demo'], 500);
        }
    }
    public function demoend(Request $request)
    {

        // dd($request->demoendid);
        $dcnf = democlasses::find($request->demoendid);

        // $dcnf->slot_confirmed = $request->slot;
        // $dcnf->slot_confirmed_at = Carbon::now();
        // $dcnf->slot_confirmed_by = session('userid')->id;
        // $dcnf->demo_link = $response->hangoutLink;
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
