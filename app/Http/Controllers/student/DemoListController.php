<?php

namespace App\Http\Controllers\student;

use App\Events\RealTimeMessage;
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\democlasses;
use App\Models\Notification;
use App\Models\status;
use App\Models\studentprofile;
use App\Models\subjects;
use App\Models\tutorregistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Helpers\CommonHelper;
use App\Services\TwilioWhatsAppService;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class DemoListController extends Controller
{
    public function index()
    {

        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'classes.name as class_name', 'tutorregistrations.name as tutor', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->where('democlasses.student_id', '=', session('userid')->id)
            ->orderBy('democlasses.created_at', 'desc')
            ->paginate(100);

        $subjects = subjects::where('is_active', 1)->where('class_id', session('userid')->class_id)->get();
        $statuses = status::select('*')->get();
        $tutors = tutorregistration::select('*')->get();
        return view('student.demolist', get_defined_vars());
    }
    public function parentindex()
    {

        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'classes.name as class_name', 'tutorregistrations.name as tutor', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->where('democlasses.student_id', '=', session('userid')->id)
            ->orderBy('democlasses.created_at', 'desc')
            ->paginate(10);
        $subjects = subjects::where('is_active', 1)->where('class_id', session('userid')->class_id)->get();
        $statuses = status::select('*')->get();
        $tutors = tutorregistration::select('*')->get();
        return view('parent.demolist', get_defined_vars());
    }
    public function demolistSearch(Request $request)
    {
        // return $request->all();
        $query = democlasses::select('*', 'democlasses.id as demo_id', 'tutorregistrations.name as tutor', 'classes.name as class_name', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->where('democlasses.student_id', '=', session('userid')->id);

        if ($request->tutor) {
            $query->where('democlasses.tutor_id', $request->tutor);
        }
        if ($request->subject_name) {
            $query->where('democlasses.subject_id', $request->subject_name);
        }

        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(democlasses.slot_confirmed)'), '<=', $request->end_date);
        }
        if ($request->status) {
            $query->where('democlasses.status', $request->status);
        }
        $demos = $query->paginate(10);
        $type = "student";
        $viewTable = view('admin.partials.democlass-search', compact('demos', 'type'))->render();
        $viewPagination = $demos->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination,
        ]);

    }
    public function democancel(Request $request)
    {
        $demo = democlasses::find($request->id);
        // echo $demo;
        $demo->status = "5";
        $res = $demo->save();
        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 2;
            $notificationdata->notification = 'Trial Class Cancelled By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $demo->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            $notificationdata->show_to_admin = 1;
            // $notificationdata->show_to_admin_id = $request->receiver_id;
            $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
            $notificationdata->show_to_tutor = 1;
            $notificationdata->show_to_tutor_id = $demo->tutor_id;
            // $notificationdata->show_to_all_tutor = 0;
            // }
            // Sending to student
            // if($request->receiver_role_id == 3){
            //     $notificationdata->show_to_student = 1;
            //     $notificationdata->show_to_student_id = $request->receiver_id;
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

            return back()->with('success', 'Trial Cancelled Successfully');
        } else {
            return back()->with('fail', 'Something Went Wrong. Try Again Later');

        }
    }

    public function bookdemo(Request $request, TwilioWhatsAppService $whatsApp)
    {
        $student = session('userid');
        $studentTz = TimezoneHelper::userTimezone($student);

        $profchk = studentprofile::select('email', 'mobile')
            ->where('student_id', $student->id)
            ->first();

        if (!$profchk || empty($profchk->email)) {
            return back()->with('fail', 'Please update your profile first. Visit your profile section to update');
        }

        $tutor = tutorregistration::find($request->demotutorid);
        if (!$tutor) {
            return back()->with('fail', 'Tutor not found.');
        }

        // Check duplicate demo
        $existingDemo = democlasses::where('student_id', $student->id)
            ->where('tutor_id', $request->demotutorid)
            ->where('subject_id', $request->demosubjectid)
            ->first();

        if ($existingDemo) {
            return back()->with('fail', 'You have already taken the demo.');
        }

        /*
        |--------------------------------------------------------------------------
        | Convert Student Selected Slots → UTC (for storage)
        |--------------------------------------------------------------------------
        */

        $slot_1_utc = $request->demoslotfirst
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->demoslotfirst, $studentTz)->setTimezone('UTC')
            : null;

        $slot_2_utc = $request->demoslotsecond
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->demoslotsecond, $studentTz)->setTimezone('UTC')
            : null;

        $slot_3_utc = $request->demoslotthird
            ? Carbon::createFromFormat('Y-m-d\TH:i', $request->demoslotthird, $studentTz)->setTimezone('UTC')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Save Demo (UTC Stored)
        |--------------------------------------------------------------------------
        */

        $demo = new democlasses();
        $demo->student_id = $student->id;
        $demo->tutor_id = $request->demotutorid;
        $demo->subject_id = $request->demosubjectid;
        $demo->slot_1 = $slot_1_utc?->toDateTimeString();
        $demo->slot_2 = $slot_2_utc?->toDateTimeString();
        $demo->slot_3 = $slot_3_utc?->toDateTimeString();
        $demo->remarks = $request->message;
        $demo->status = "1";

        $res = $demo->save();

        if (!$res) {
            return redirect()->to('student/searchtutor')
                ->with('fail', 'Something Went Wrong. Try Again Later');
        }

        /*
        |--------------------------------------------------------------------------
        | Format Slots For Email (Student Timezone)
        |--------------------------------------------------------------------------
        */

        $slot1Student = $slot_1_utc
            ? TimezoneHelper::formatInUserTz($slot_1_utc, 'd M Y, h:i A', 'UTC', $student)
            : 'N/A';

        $slot2Student = $slot_2_utc
            ? TimezoneHelper::formatInUserTz($slot_2_utc, 'd M Y, h:i A', 'UTC', $student)
            : 'N/A';

        $slot3Student = $slot_3_utc
            ? TimezoneHelper::formatInUserTz($slot_3_utc, 'd M Y, h:i A', 'UTC', $student)
            : 'N/A';

        /*
        |--------------------------------------------------------------------------
        | Send Email (Student Timezone)
        |--------------------------------------------------------------------------
        */

        $details = [
            'name' => $student->name,
            'slot_1' => $slot1Student,
            'slot_2' => $slot2Student,
            'slot_3' => $slot3Student,
            'tutor_name' => $tutor->name,
            'mailtype' => 2,
        ];

        try {
            Mail::to($student->email)->send(new SendMail($details));
        } catch (\Exception $e) {
            // Optional: log error
        }

        /*
        |--------------------------------------------------------------------------
        | Create Notification
        |--------------------------------------------------------------------------
        */

        $notification = new Notification();
        $notification->alert_type = 2;
        $notification->notification = 'New Trial Class Scheduled By ' . $student->name;
        $notification->initiator_id = $student->id;
        $notification->initiator_role = $student->role_id;
        $notification->event_id = $demo->id;
        $notification->show_to_admin = 1;
        $notification->show_to_all_admin = 1;
        $notification->show_to_tutor = 1;
        $notification->show_to_tutor_id = $tutor->id;
        $notification->read_status = 0;
        $notification->save();

        /*
        |--------------------------------------------------------------------------
        | Format Slots For Tutor (Tutor Timezone)
        |--------------------------------------------------------------------------
        */

        $slot1Tutor = $slot_1_utc
            ? TimezoneHelper::formatInUserTz($slot_1_utc, 'd M Y, h:i A', 'UTC', $tutor)
            : 'N/A';

        $slot2Tutor = $slot_2_utc
            ? TimezoneHelper::formatInUserTz($slot_2_utc, 'd M Y, h:i A', 'UTC', $tutor)
            : 'N/A';

        $slot3Tutor = $slot_3_utc
            ? TimezoneHelper::formatInUserTz($slot_3_utc, 'd M Y, h:i A', 'UTC', $tutor)
            : 'N/A';

        /*
        |--------------------------------------------------------------------------
        | Send WhatsApp - Tutor (Tutor TZ)
        |--------------------------------------------------------------------------
        */

        if (!empty($tutor->mobile)) {
            try {
                $whatsApp->sendMessage(
                    $tutor->mobile,
                    [
                        $tutor->name,
                        $slot1Tutor,
                        $slot2Tutor,
                        $slot3Tutor,
                        $student->name,
                    ],
                    1634
                );
            } catch (\Exception $e) {}
        }

        /*
        |--------------------------------------------------------------------------
        | Send WhatsApp - Student (Student TZ)
        |--------------------------------------------------------------------------
        */

        if (!empty($profchk->mobile)) {
            try {
                $whatsApp->sendMessage(
                    $profchk->mobile,
                    [
                        $student->name,
                        $tutor->name,
                        $slot1Student,
                        $slot2Student,
                        $slot3Student,
                    ],
                    1635
                );
            } catch (\Exception $e) {}
        }

        broadcast(new RealTimeMessage('notification'));

        return redirect()->to('student/trialsuccess')
            ->with('success', 'Trial Scheduled Successfully. Please login to class using your registered Email Id.');
    }

    public function trialsuccess()
    {

        return view('student.trialsuccess');
    }
}

// democlasses
