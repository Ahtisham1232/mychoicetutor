<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\studentprofile;
use App\Models\studentregistration;
use Illuminate\Http\Request;
use App\Models\tutorprofile;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\zoom_classes;
use App\Models\Notification;
use App\Models\OnlineTests;
use Illuminate\Support\Carbon;
use App\Models\payments\paymentdetails;
use DB;
class DashboardController extends Controller
{
    public function index(){

        $studentpro = tutorprofile::select('*')
            // ->where('tutor_id', '=', session('userid')->id)
            ->first();
        $pending_demos = democlasses::whereIn('status', [1, 6])
        // ->where('tutor_id', '=', session('userid')->id)
        ->count();
        $stud_count = studentregistration::select('*')->count();
        $classes_taken = zoom_classes::where('zoom_classes.is_completed',1)
        // ->where('tutor_id', '=', session('userid')->id)
        ->count();
        $moneyEarned = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
                    // ->where('tutor_id', session('userid')->id)
                    ->selectRaw('COUNT(DISTINCT student_id) as student_count, SUM(amount) as total_earned')
                    ->first();
        // democlasses taken and queries for percentage bar
        $demosTaken = democlasses::join('subjects', 'subjects.id', 'democlasses.subject_id')
                    ->where('democlasses.status', 4)
                    // ->where('democlasses.tutor_id', '=', session('userid')->id)
                    ->groupBy('subjects.id')
                    ->selectRaw('subjects.id, COUNT(*) as subject_count')
                    ->get();
        $totalDemosTaken = democlasses::where('status', 4)
        // ->where('tutor_id', '=', session('userid')->id)
        ->count();
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

        $upcomingClasses = zoom_classes::select('zoom_classes.id as class_id','zoom_classes.started_at','zoom_classes.start_time', 'zoom_classes.completed_at', 'zoom_classes.meeting_id as meeting_id', 'zoom_classes.topic_name as topic_name', 'zoom_classes.status as meeting_status', 'zoom_classes.start_time as meeting_start_time', 'zoom_classes.is_completed as is_completed', 'zoom_classes.recording_link as recording_link', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name', 'tutorregistrations.mobile as tutor_mobile', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile', 'topics.name as topic_name', 'subjects.name as subject_name', 'classes.name as class_name')
            ->leftjoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftjoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftjoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->leftjoin('classes', 'classes.id', '=', 'subjects.class_id')
            ->orderby('zoom_classes.created_at', 'desc')
            ->paginate(5);




        $upcomingQuizes = OnlineTests::where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
                ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });

        $tUpcomingClasses = zoom_classes::where('start_time', '>', Carbon::now())
        ->orderBy('start_time', 'asc')
        ->get();

        $totalUpcomingClasses = $tUpcomingClasses->count();

        $latest_payments = paymentdetails::leftjoin('paymentstudents','paymentstudents.transaction_id','paymentdetails.transaction_id')
                    ->leftjoin('subjects','subjects.id','paymentstudents.subject_id')
                    // ->where('paymentstudents.tutor_id', '=', session('userid')->id)
                    ->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        $upcoming_demos = democlasses::select('democlasses.*','subjects.name as subject','studentprofiles.name as student','studentprofiles.profile_pic as student_img','tutorregistrations.name as tutor_name')
        ->join('studentprofiles','studentprofiles.student_id','democlasses.student_id')
        ->join('subjects','subjects.id','democlasses.subject_id')
        ->join('tutorregistrations','tutorregistrations.id','democlasses.tutor_id')
        // ->where('democlasses.slot_confirmed', '>', Carbon::now())
        ->where('democlasses.status','!=','5')
        // ->where('democlasses.tutor_id', '=', session('userid')->id)
        ->orderBy('democlasses.slot_confirmed', 'asc')->take(5)->get();
        $upcoming_demos->transform(function ($demos) {
            $demos->slot_confirmed = Carbon::parse($demos->slot_confirmed);
            $demos->slot_1 = Carbon::parse($demos->slot_1);
            return $demos;
        });
        // dd($upcoming_demos);


        return view('admin.dashboard',get_defined_vars());
    }

    public function notificationslist(){
        $notifications = Notification::select('*')
        // ->where('show_to_admin',1)
        ->orderBy('created_at','desc')
        // ->where('show_to_student_id', session('userid')->id)
        ->paginate(20);
        return view('admin.notificationslist', compact('notifications'));
    }
    public function notificationdelete($id){

        $data = Notification::find($id);

        $res = $data->delete();

        if($res){
            return back()->with('success','Notification deleted successfully.');
        }
        else{
            return back()->with('error','Something went wrong.');
        }
    }
}
