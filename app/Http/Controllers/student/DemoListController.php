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
        // $studentprofile = studentprofile::select('*')->where('student_id',session('userid')->id)->first();
        $profchk = studentprofile::select('email', 'mobile')->where('student_id', session('userid')->id)->first();

        $tutor = tutorregistration::select('*')->where('id', $request->demotutorid)->first();
        if (!$profchk || $profchk->email === null) {
            return back()->with('fail', 'Please update your profile first. Visit your profile section to update');
        }
        // Check if the demo class already exists
        $existingDemo = democlasses::where('student_id', session('userid')->id)
            ->where('tutor_id', $request->demotutorid)
            ->where('subject_id', $request->demosubjectid)
            ->first();

        if ($existingDemo) {
        // If the record already exists, redirect back with an error message
            return back()->with('fail','You have already taken the demo.');
        }
        // dd($request->message);
        $demo = new democlasses();
        $demo->student_id = session('userid')->id;
        $demo->tutor_id = $request->demotutorid;
        $demo->subject_id = $request->demosubjectid;
        // $demo->subject_id = $request->demosubjectid;
        $demo->slot_1 = $request->demoslotfirst;
        $demo->slot_2 = $request->demoslotsecond;
        $demo->slot_3 = $request->demoslotthird;
        $demo->remarks = $request->message;
        // $demo->slot_confirmed = "";
        // $demo->slot_confirmed_at = "";
        // $demo->slot_confirmed_by = "";
        $demo->status = "1";

        $res = $demo->save();

        // Send welcome mail
        $details = [
            'name' => session('userid')->name,
            'slot_1' => $request->demoslotfirst,
            'slot_2' => $request->demoslotsecond,
            'slot_3' => $request->demoslotthird,
            'tutor_name' => $tutor->name,
            'mailtype' => 2,
        ];

       
        try {
            Mail::to(session('userid')->email)->send(new SendMail($details));
            // success response if needed
        } catch (\Exception $e) {
            // log error for debugging
        }

        if ($res) {
            $notificationdata = new Notification();
            $notificationdata->alert_type = 2;
            $notificationdata->notification = 'New Trial Class Scheduled By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $demo->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            $notificationdata->show_to_admin = 1;
            // $notificationdata->show_to_admin_id = $request->receiver_id;
            $notificationdata->show_to_all_admin = 1;
            $notificationdata->show_to_tutor = 1;
            $notificationdata->show_to_tutor_id = $request->demotutorid;
            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();

            // Format slots before sending WhatsApp
            $formattedSlot1 = $request->demoslotfirst 
            ? Carbon::parse($request->demoslotfirst)->format('d M Y, h:i A') 
            : 'N/A';

            $formattedSlot2 = $request->demoslotsecond 
            ? Carbon::parse($request->demoslotsecond)->format('d M Y, h:i A') 
            : 'N/A';

            $formattedSlot3 = $request->demoslotthird 
            ? Carbon::parse($request->demoslotthird)->format('d M Y, h:i A') 
            : 'N/A';

            // Send WhatsApp to Tutor
            if (!empty($tutor->mobile)) {
                try {
                    $templateIdTutor = 1634;
                   $tutorNumber = $tutor->mobile;
                    $bodyVariablesTutor = [
                        $tutor->name,
                        $formattedSlot1,
                        $formattedSlot2,
                        $formattedSlot3,
                        $student->name,
                    ];
                    $whatsApp->sendMessage($tutorNumber, $bodyVariablesTutor, $templateIdTutor);
                } catch (\Exception $e) {
                }
            }
        
            // Send WhatsApp to Student
            if (!empty($profchk->mobile)) {
                try {
                    $templateIdStudent = 1635;
                    $studentNumber = $profchk->mobile;
                    $bodyVariablesStudent = [
                        $student->name,
                        $tutor->name,
                        $formattedSlot1,
                        $formattedSlot2,
                        $formattedSlot3,
                    ];
                    $whatsApp->sendMessage($studentNumber, $bodyVariablesStudent, $templateIdStudent);
                } catch (\Exception $e) {
                }
            }
            broadcast(new RealTimeMessage('$notification'));


            return redirect()->to('student/trialsuccess')->with('success', 'Trial Scheduled Successfully. Please login to class using your registered Email Id.');
        } else {
            return redirect()->to('student/searchtutor')->with('fail', 'Something Went Wrong. Try Again Later');
        }
    }
    public function trialsuccess()
    {

        return view('student.trialsuccess');
    }
}

// democlasses
