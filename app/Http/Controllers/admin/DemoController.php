<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\democlasses;
use App\Models\status;
use App\Models\classes;
use App\Models\subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Events\RealTimeMessage;
use App\Models\studentregistration;
use App\Models\tutorregistration;
use App\Helpers\CommonHelper;
use App\Services\TwilioWhatsAppService;
use Illuminate\Support\Facades\Log;
use App\Helpers\TimezoneHelper;

class DemoController extends Controller
{
    public function index()
    {
        $type = 'all';
        $pageTitle = "All Trials";
        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'tutorregistrations.name as tutor', 'tutorregistrations.mobile as tutor_mobile', 'subjects.name as subject', 'subjects.id as subjectid', 'classes.name as class_name', 'statuses.name as currentstatus', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('studentregistrations', 'studentregistrations.id', '=', 'democlasses.student_id')
            ->orderby('democlasses.created_at', 'desc')
            // ->where('democlasses.student_id','=', session('userid')->id)
            ->paginate(10);
        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        return view('admin.demolist', get_defined_vars());
    }

    public function pendingTrial()
    {
        $type = 'pending';
        $pageTitle = "Pending Trials";
        $demos = democlasses::select(
            '*',
            'democlasses.id as demo_id',
            'tutorregistrations.name as tutor',
            'tutorregistrations.mobile as tutor_mobile',
            'subjects.name as subject',
            'subjects.id as subjectid',
            'classes.name as class_name',
            'statuses.name as currentstatus',
            'studentregistrations.id as student_id',
            'studentregistrations.name as student_name',
            'studentregistrations.mobile as student_mobile'
        )
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('studentregistrations', 'studentregistrations.id', '=', 'democlasses.student_id')

            // IMPORTANT FILTER (Pending trials)
            ->where('democlasses.status', '!=', 'Attended')
            // OR if status is ID-based:
            // ->where('democlasses.status', '!=', 1)

            ->orderBy('democlasses.created_at', 'desc')
            ->paginate(10);

        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();

        $pageTitle = "Pending Trials";

        return view('admin.demolist', get_defined_vars());
    }
    // search functionality
    public function demolistsearch(Request $request)
    {
        $type = $request->type ?? 'all';
        $query = democlasses::select('*', 'democlasses.id as demo_id', 'tutorregistrations.name as tutor', 'tutorregistrations.mobile as tutor_mobile', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('studentregistrations', 'studentregistrations.id', '=', 'democlasses.student_id');

        // ->where('democlasses.student_id','=', session('userid')->id)
        // ->get();
        if ($type == 'pending') {
            $query->where('statuses.name', '!=', 'Attended');
        }

        if ($type == 'attended') {
            $query->where('statuses.name', 'Attended');
        }


        if ($request->student_name) {
            $query->where('studentregistrations.name', 'like', '%' . $request->student_name . '%');
        }
        if ($request->student_mobile) {
            $query->where('studentregistrations.mobile', 'like', '%' . $request->student_mobile . '%');
        }
        if ($request->tutor_name) {
            $query->where('tutorregistrations.name', 'like', '%' . $request->tutor_name . '%');
        }
        if ($request->tutor_mobile) {
            $query->where('tutorregistrations.mobile', 'like', '%' . $request->tutor_mobile . '%');
        }
        if ($request->class_name) {
            $query->where('classes.id', $request->class_name);
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
        $type = "admin";
        $viewTable = view('admin.partials.democlass-search', compact('demos', 'type'))->render();
        $viewPagination = $demos->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }


    public function democancel(Request $request)
    {
        $demo = democlasses::find($request->id);
        // echo $demo;
        $demo->status = "5";
        $res = $demo->save();
        if ($res) {

            return back()->with('success', 'Trial Cancelled Successfully');
        } else {
            return back()->with('fail', 'Something Went Wrong. Try Again Later');
        }
    }

    public function bookdemo(Request $request)
    {
        $demo = new democlasses();
        $demo->student_id = session('userid')->id;
        $demo->tutor_id = $request->demotutorid;
        $demo->subject_id = $request->demosubjectid;
        // $demo->subject_id = $request->demosubjectid;
        $demo->slot_1 = $request->demoslotfirst;
        $demo->slot_2 = $request->demoslotsecond;
        $demo->slot_3 = $request->demoslotthird;
        // $demo->slot_confirmed = "";
        // $demo->slot_confirmed_at = "";
        // $demo->slot_confirmed_by = "";
        $demo->status = "1";

        $res = $demo->save();
        if ($res) {

            return back()->with('success', 'Trial Scheduled Successfully');
        } else {
            return back()->with('fail', 'Something Went Wrong. Try Again Later');
        }
    }

    public function demodetails($id)
    {
        $demo = democlasses::find($id);

        $tutor = session('userid');
        $tutorTz = TimezoneHelper::userTimezone($tutor);

        if ($demo) {

            $demo->slot_1_local = $demo->slot_1
                ? Carbon::parse($demo->slot_1, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d-m-Y h:i A')
                : null;

            $demo->slot_2_local = $demo->slot_2
                ? Carbon::parse($demo->slot_2, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d-m-Y h:i A')
                : null;

            $demo->slot_3_local = $demo->slot_3
                ? Carbon::parse($demo->slot_3, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d-m-Y h:i A')
                : null;

            // ⚠ For datetime-local input (update modal)
            $demo->slot_1_input = $demo->slot_1
                ? Carbon::parse($demo->slot_1, 'UTC')
                ->setTimezone($tutorTz)
                ->format('Y-m-d\TH:i')
                : null;

            $demo->slot_2_input = $demo->slot_2
                ? Carbon::parse($demo->slot_2, 'UTC')
                ->setTimezone($tutorTz)
                ->format('Y-m-d\TH:i')
                : null;

            $demo->slot_3_input = $demo->slot_3
                ? Carbon::parse($demo->slot_3, 'UTC')
                ->setTimezone($tutorTz)
                ->format('Y-m-d\TH:i')
                : null;
        }

        return response()->json([$demo]);
    }


    public function demoupdate(Request $request)
    {
        $request->validate([
            'slotupdate1' => 'required',
            'statusupdate' => 'required'
        ]);

        $dcnf = democlasses::find($request->demoupdateid);

        $tutor = session('userid');
        $tutorTz = TimezoneHelper::userTimezone($tutor);

        // Convert Local → UTC before saving
        $dcnf->slot_1 = $request->slotupdate1
            ? Carbon::parse($request->slotupdate1, $tutorTz)
            ->setTimezone('UTC')
            : null;

        $dcnf->slot_2 = $request->slotupdate2
            ? Carbon::parse($request->slotupdate2, $tutorTz)
            ->setTimezone('UTC')
            : null;

        $dcnf->slot_3 = $request->slotupdate3
            ? Carbon::parse($request->slotupdate3, $tutorTz)
            ->setTimezone('UTC')
            : null;
        $dcnf->status = $request->statusupdate;

        $res = $dcnf->save();
        if ($res) {
            return back()->with('success', 'Slot updated successfully');
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }

    public function tutordemolist()
    {

        $tutor = session('userid');
        $tutorTz = TimezoneHelper::userTimezone($tutor);

        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'tutorregistrations.name as tutor', 'tutorregistrations.mobile as tutor_mobile', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus', 'classes.name as class_name', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile', 'classes.name as classname')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('studentregistrations', 'studentregistrations.id', '=', 'democlasses.student_id')
            ->where('democlasses.tutor_id', '=', session('userid')->id)->orderBy('democlasses.created_at', 'desc')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | Convert UTC Slots → Tutor Timezone
        |--------------------------------------------------------------------------
        */

        foreach ($demos as $demo) {

            $demo->slot_1_local = $demo->slot_1
                ? \Carbon\Carbon::parse($demo->slot_1, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d M Y, h:i A')
                : null;

            $demo->slot_2_local = $demo->slot_2
                ? \Carbon\Carbon::parse($demo->slot_2, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d M Y, h:i A')
                : null;

            $demo->slot_3_local = $demo->slot_3
                ? \Carbon\Carbon::parse($demo->slot_3, 'UTC')
                ->setTimezone($tutorTz)
                ->format('d M Y, h:i A')
                : null;
        }

        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        return view('tutor.demolist-new', get_defined_vars());
    }

    // search functionaity tutor
    public function tutorDemolistsearch(Request $request)
    {


        $query = democlasses::select('*', 'democlasses.id as demo_id', 'tutorregistrations.name as tutor', 'tutorregistrations.mobile as tutor_mobile', 'subjects.name as subject', 'classes.name as class_name', 'subjects.id as subjectid', 'statuses.name as currentstatus', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('studentregistrations', 'studentregistrations.id', '=', 'democlasses.student_id')
            ->where('democlasses.tutor_id', '=', session('userid')->id);

        // ->where('democlasses.student_id','=', session('userid')->id)
        // ->get();


        if ($request->student_name) {
            $query->where('studentregistrations.name', 'like', '%' . $request->student_name . '%');
        }
        if ($request->student_mobile) {
            $query->where('studentregistrations.mobile', 'like', '%' . $request->student_mobile . '%');
        }
        // if ($request->tutor_name) {
        //     $query->where('tutorregistrations.name','like', '%' . $request->tutor_name . '%');
        // }
        // if ($request->tutor_mobile) {
        //     $query->where('tutorregistrations.mobile','like', '%' . $request->tutor_mobile . '%');
        // }
        if ($request->class_name) {
            $query->where('classes.id', $request->class_name);
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
        $type = "tutor";
        $viewTable = view('admin.partials.democlass-search', compact('demos', 'type'))->render();
        $viewPagination = $demos->links()->render();

        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        return view('tutor.demolist-new', get_defined_vars());
    }


    public function tutordemoupdate(Request $request)
    {
        $request->validate([
            'statusupdate' => 'required',
            'demoid' => 'required'
        ]);

        $data = democlasses::find($request->demoid);
        $data->status = $request->statusupdate;
        $data->remarks = $request->remarks;

        $res = $data->save();
        if ($res) {
            return back()->with('success', 'Trial details updated successfully');
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }

    public function demostatusupdate(Request $request, TwilioWhatsAppService $whatsApp)
    {
        $data = democlasses::find($request->id);
        $data->status = '8';
        $res = $data->save();
        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 2;
            $notificationdata->notification = 'Trial class started by ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $data->id;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            // $notificationdata->show_to_admin = 1;
            //     $notificationdata->show_to_admin_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
            // $notificationdata->show_to_tutor = 1;
            // $notificationdata->show_to_tutor_id = $tutor_id->assigned_by;
            // $notificationdata->show_to_all_tutor = 0;
            // }
            // Sending to student
            // if($request->receiver_role_id == 3){
            $notificationdata->show_to_student = 1;
            $notificationdata->show_to_student_id = $data->student_id;
            $notificationdata->show_to_all_student = 0;
            // }
            // // Sending to parent
            // if($request->receiver_role_id == 3){
            // $notificationdata->show_to_parent = 1;
            // $notificationdata->show_to_parent_id = $request->receiver_id;
            // $notificationdata->show_to_all_parent = 0;
            // }
            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();
            broadcast(new RealTimeMessage('$notification'));

            // Send WhatsApp notification to the student (Demo Class) using template 2404
            try {
                $student = studentregistration::find($data->student_id);
                $tutor   = tutorregistration::find($data->tutor_id);

                if ($student && $tutor && !empty($student->mobile)) {
                    // $templateIdClassConfirm = 1939;
                    $templateIdClassConfirm = 2404;

                    // Determine class type label
                    $classType = 'Demo Class';

                    // Format phone number
                    $studentNumber = $student->mobile;

                    $bodyVariablesStudent = [
                        $student->name,
                        $classType,
                        $tutor->name,
                    ];
                    $meetingUrl = $data->demo_link ?? 'Link not available for democlass';
                    $buttonVariables = [$meetingUrl]; // Wrap in array

                    $sent = $whatsApp->sendMessage(
                        $studentNumber,
                        $bodyVariablesStudent,
                        $templateIdClassConfirm,
                        $buttonVariables
                    );

                    if ($sent) {
                        Log::info("WHATSAPP SENT: {$classType} started", [
                            'student_name'   => $student->name,
                            'student_mobile' => $studentNumber,
                            'class_type'     => $classType,
                        ]);
                    } else {
                        Log::warning("WHATSAPP FAILED: {$classType}", [
                            'student_mobile' => $studentNumber,
                            'class_type'     => $classType,
                        ]);
                    }
                } else {
                    Log::warning('Class start WhatsApp skipped (demo): missing student/tutor or mobile', [
                        'demo_id'    => $data->id ?? null,
                        'student_id' => $data->student_id ?? null,
                        'tutor_id'   => $data->tutor_id ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send WhatsApp for demo class start: ' . $e->getMessage(), [
                    'demo_id' => $data->id ?? null,
                ]);
            }

            return json_encode(array('statusCode' => 200));
        } else {
            return back()->with('fail', 'Something went wrong, please try again later');
        }
    }
}
