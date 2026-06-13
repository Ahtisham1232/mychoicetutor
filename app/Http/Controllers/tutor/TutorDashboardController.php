<?php

namespace App\Http\Controllers\tutor;

use App\Http\Controllers\Controller;
use App\Models\tutorprofile;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\payout;
use App\Models\studentregistration;
use App\Models\studentprofile;
use Illuminate\Http\Request;
use App\Models\zoom_classes;
use App\Models\Notification;
use App\Models\OnlineTests;
use Illuminate\Support\Carbon;
use App\Models\payments\paymentdetails;
use App\Models\teacherclassmapping;
use DB;

class TutorDashboardController extends Controller
{
    public function index(){

        $studentpro = tutorprofile::select('*')
            ->where('tutor_id', '=', session('userid')->id)
            ->first();
            $studentpro = tutorprofile::select('*')
            ->where('tutor_id', '=', session('userid')->id)
            ->first();
            $studentpro = tutorprofile::select('*')
            ->where('tutor_id', '=', session('userid')->id)
            ->first();
        $classmapping = teacherclassmapping::select('*')->where('teacher_id',session('userid')->id)->first();
        // if(!$classmapping){
        //     return redirect('tutor/profileupdate')->with('fail', 'Kindly update your profile & add Class/Grade Mapping.')->with('success', 'Registration Successfull. You can use your registered "Mobile Number" & "Password" to login');
        // }

        if (!$classmapping) {
            // Optionally you can redirect with a fail/success message in case you still need the redirect as a fallback
            return redirect('tutor/profileupdate')
                ->with('show_popup', true)
                ->with('fail', 'Kindly update your profile & add Class/Grade Mapping.')
                ->with('success', 'Registration Successful. You can use your registered "Mobile Number" & "Password" to login.');
        }

        $pending_demos = democlasses::whereIn('status', [1, 6])->where('tutor_id', '=', session('userid')->id)->count();
        $classes_taken = zoom_classes::where('zoom_classes.is_completed',1)->where('tutor_id', '=', session('userid')->id)->count();
        // $moneyEarned = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
        //             ->where('tutor_id', session('userid')->id)
        //             ->selectRaw('COUNT(DISTINCT student_id) as student_count, SUM(amount) as total_earned')
        //             ->first();
        $moneyEarned = payout::where('tutor_id', session('userid')->id)->sum('net_amount_received');

        // democlasses taken and queries for percentage bar
        $demosTaken = democlasses::join('subjects', 'subjects.id', 'democlasses.subject_id')
                    ->where('democlasses.status', 4)
                    ->where('democlasses.tutor_id', '=', session('userid')->id)
                    ->groupBy('subjects.id')
                    ->selectRaw('subjects.id, COUNT(*) as subject_count')
                    ->get();
        $totalDemosTaken = democlasses::where('status', 4)->where('tutor_id', '=', session('userid')->id)->count();
        $subjectData = [];
        foreach ($demosTaken as $demo) {
            $subjectName = subjects::find($demo->id)->name;
            $subjectCount = $demo->subject_count;
            $percentage = ($subjectCount / $totalDemosTaken) * 100;
            $subjectData[] = [
                'subject_id' => $demo->id,
                'subject_name' => $subjectName,
                'percentage' => $percentage,
            ];
        }

        // $upcomingClasses = zoom_classes::select(
        //     'zoom_classes.*',
        //     'subjects.name as subject',
        //     DB::raw('JSON_LENGTH(batchstudentmappings.student_data) as student_count')
        // )
        // ->join('topics', 'topics.id', '=', 'zoom_classes.batch_id')
        // ->join('subjects', 'subjects.id', '=', 'topics.subject_id')
        // ->join('batchstudentmappings', 'batchstudentmappings.batch_id', '=', 'zoom_classes.batch_id')
        // ->where('zoom_classes.tutor_id', '=', session('userid')->id)
        // ->where('zoom_classes.is_completed', 0)
        // ->where('zoom_classes.start_time', '>', Carbon::now()) // Add condition to check if the start time is greater than the current time
        // ->orderBy('zoom_classes.start_time', 'asc')
        // ->take(5)
        // ->get();
        $upcomingClasses = zoom_classes::select('zoom_classes.*', 'zoom_classes.id as liveclass_id', 'studentregistrations.name as studentname', 'subjects.name as subject', 'classes.name as classname', 'slot_bookings.date as slotdate', 'slot_bookings.slot as slottime')
            ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
            ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
            ->join('paymentstudents', 'paymentstudents.id', 'slot_bookings.class_schedule_id')
            ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
            ->join('classes', 'classes.id', 'paymentstudents.class_id')
            ->where('zoom_classes.is_completed', 0)
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.tutor_id', session('userid')->id)
            // ->where('zoom_classes.start_time','>=',Carbon::now())
            ->orderby('slot_bookings.date', 'desc')
            ->get();

        $upcomingClasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time);
            return $class;
        });






        $upcomingQuizes = OnlineTests::where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
                                       ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });

        $tUpcomingClasses = zoom_classes::where('tutor_id', '=', session('userid')->id)
    ->where('start_time', '>', Carbon::now())
    ->where('is_completed',0)
    ->orderBy('start_time', 'asc')
    ->get();

    $totalUpcomingClasses = zoom_classes::select('zoom_classes.*', 'zoom_classes.id as liveclass_id', 'studentregistrations.name as studentname', 'subjects.name as subjectname', 'classes.name as classname', 'slot_bookings.date as slotdate', 'slot_bookings.slot as slottime')
    ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
    ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
    ->join('paymentstudents', 'paymentstudents.id', 'slot_bookings.class_schedule_id')
    ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
    ->join('classes', 'classes.id', 'paymentstudents.class_id')
    ->where('zoom_classes.is_completed', 0)
    ->where('zoom_classes.is_active', 1)
    ->where('zoom_classes.tutor_id', session('userid')->id)
    ->orderby('zoom_classes.created_at', 'desc')
    ->count();;

        $latest_payments = paymentdetails::leftjoin('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
                    ->leftjoin('subjects','subjects.id','paymentstudents.subject_id')->where('paymentstudents.tutor_id', '=', session('userid')->id)->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        // $upcoming_demos = democlasses::select(
        //     'democlasses.*',
        //     'subjects.name as subject',
        //     'studentprofiles.name as student',
        //     'studentprofiles.profile_pic as student_img'
        // )
        // ->join('studentprofiles', 'studentprofiles.student_id', '=', 'democlasses.student_id')
        // ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
        // ->where('democlasses.tutor_id', '=', session('userid')->id)
        // ->where('democlasses.status', '=', 1) // Add condition to filter by status
        // ->orderBy('democlasses.slot_confirmed', 'asc')
        // ->take(5)
        // ->get();
        $upcoming_demos = democlasses::select('*','democlasses.id as demo_id','tutorregistrations.name as tutor','studentprofiles.name as student','studentprofiles.profile_pic as student_img','tutorregistrations.mobile as tutor_mobile','subjects.name as subject','subjects.id as subjectid','statuses.name as currentstatus','classes.name as class_name','studentregistrations.id as student_id','studentregistrations.name as student_name','studentregistrations.mobile as student_mobile','classes.name as classname')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
        ->join('subjects', 'subjects.id','=','democlasses.subject_id')
        ->join('studentprofiles', 'studentprofiles.student_id', '=', 'democlasses.student_id')
        ->join('classes', 'classes.id','=','subjects.class_id')
        ->join('statuses', 'statuses.id','=','democlasses.status')
        ->join('studentregistrations','studentregistrations.id','=','democlasses.student_id')
        ->where('democlasses.tutor_id','=', session('userid')->id)
        ->where('democlasses.slot_confirmed','>=', Carbon::now())
        ->orderBy('democlasses.created_at', 'desc')
        ->take(5)
        ->get();


    $upcoming_demos->transform(function ($demos) {
        $demos->slot_confirmed = Carbon::parse($demos->slot_confirmed);
        $demos->slot_1 = Carbon::parse($demos->slot_1);
        return $demos;
    });

        // Students Count
        $studentscount = studentprofile::join('studentregistrations', 'studentregistrations.id', '=', 'studentprofiles.student_id')
    ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
    ->join('classes', 'classes.id', '=', 'paymentstudents.class_id')
    ->join('subjects', 'subjects.id', '=', 'paymentstudents.subject_id')
    ->where('paymentstudents.tutor_id', session('userid')->id)
    ->where('studentregistrations.is_active', 1)
    ->distinct()
    ->count('studentprofiles.student_id');

            // dd($studentscount);

        return view('tutor.dashboard',get_defined_vars());
    }

    public function notificationslist() {
        // Get the current date minus 30 days
        $dateLimit = now()->subDays(30);

        // Start building the base query for notifications
        $notifications = Notification::select('notifications.*')
            ->where('notifications.show_to_tutor', 1)
            ->where('notifications.show_to_tutor_id', session('userid')->id)
            ->where('notifications.created_at', '>=', $dateLimit) // Filter last 30 days
            ->orderBy('notifications.created_at', 'desc');

        // Apply LEFT JOINs based on the initiator_role
        $notifications->leftJoin('admins', function($join) {
            $join->on('notifications.initiator_id', '=', 'admins.id')
                 ->where('notifications.initiator_role', '=', 1);
        });

        $notifications->leftJoin('tutorregistrations', function($join) {
            $join->on('notifications.initiator_id', '=', 'tutorregistrations.id')
                 ->where('notifications.initiator_role', '=', 2);
        });

        $notifications->leftJoin('studentregistrations', function($join) {
            $join->on('notifications.initiator_id', '=', 'studentregistrations.id')
                 ->where('notifications.initiator_role', '=', 3);
        });

        // Add the conditional selection for sender_name
        $notifications->addSelect(DB::raw("
            CASE
                WHEN notifications.initiator_role = 1 THEN admins.name
                WHEN notifications.initiator_role = 2 THEN tutorregistrations.name
                WHEN notifications.initiator_role = 3 THEN studentregistrations.name
                ELSE 'Unknown'
            END as sender_name
        "));

        // Paginate the result with 20 notifications per page
        $notifications = $notifications->paginate(20);

        // Return to the view
        return view('tutor.notificationslist', compact('notifications'));
    }


}
