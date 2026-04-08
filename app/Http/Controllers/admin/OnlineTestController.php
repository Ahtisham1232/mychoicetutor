<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\OnlineTests;
use App\Models\questionbank;
use App\Models\subjects;
use App\Models\classes;
use App\Models\AssignTest;
use App\Models\topics;
use App\Models\OnlineTest;
use App\Models\testattempted;
use App\Models\testresponssheet;
use App\Models\tutorsubjectmapping;
use App\Models\TemporarySubjective;
use App\Models\SubjectiveResponse;
use App\Models\studentregistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Events\RealTimeMessage;
use App\Models\Notification;
use App\Services\TwilioWhatsAppService;
use Illuminate\Support\Facades\Log;
use App\Helpers\TimezoneHelper;
use App\Models\tutorregistration;

class OnlineTestController extends Controller
{
    public function index()
    {
        $testlists = OnlineTests::select('*', 'online_tests.id as test_id', 'online_tests.name as test_name', 'online_tests.description as test_description', 'online_tests.is_active as test_status', 'classes.name as class_name', 'subjects.name as subject_name', 'online_tests.topic_name as topic_name')
            ->join('classes', 'classes.id', 'online_tests.class_id')
            ->join('subjects', 'subjects.id', 'online_tests.subject_id')->orderby('online_tests.created_at', 'desc')
            ->paginate(10);
        $classes = classes::where('is_active', 1)->get();
        $subjects = subjects::where('is_active', 1)->get();
        $topics = topics::where('is_active', 1)->get();

        return view('admin.onlinetestlist', get_defined_vars());
    }

    public function onlinetestSearch(Request $request)
    {
        // return $request->all();
        $query = OnlineTests::select('*', 'online_tests.id as test_id', 'online_tests.name as test_name', 'online_tests.description as test_description', 'online_tests.is_active as test_status', 'classes.name as class_name', 'subjects.name as subject_name', 'online_tests.topic_name as topic_name')
            ->join('classes', 'classes.id', 'online_tests.class_id')
            ->join('subjects', 'subjects.id', 'online_tests.subject_id');
        // ->get();
        if ($request->test_name) {
            $query->where('online_tests.name', 'like', '%' . $request->test_name . '%');
        }
        if ($request->class_name) {
            $query->where('online_tests.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('online_tests.subject_id', $request->subject_name);
        }
        if ($request->topic_name) {
            $query->where('online_tests.topic_id', $request->topic_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(online_tests.test_start_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(online_tests.test_end_date)'), '<=', $request->end_date);
        }
        if ($request->status_field) {
            if ($request->status_field == '2') {
                $request->status_field = '0';
            }
            $query->where('online_tests.is_active', $request->status_field);
        }
        $testlists = $query->paginate(10);
        $type = 'testlists';
        $viewTable = view('admin.partials.students-tutor-search', compact('testlists', 'type'))->render();
        $viewPagination = $testlists->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }


    public function create()
    {
        $classes = (new CommonController)->classes();
        return view('admin.onlinetestnew', compact('classes'));
    }

    public function fetchquestions(Request $request)
    {
        // echo $request->topic_id;

        $questions = questionbank::select('*')
            ->where('subject_id', $request->subject_id)
            ->where('is_active', 1)
            ->where('type', $request->type)
            ->get();

        return $questions;
    }

    public function store(Request $request)
    {
        $request->validate([
            'testname' => 'required',
            'testdescription' => 'required',
            'classname' => 'required',
            'subject' => 'required',
            'topic' => 'required',
            'maxattempt' => 'required',
            'duration' => 'required',
            'tstartdate' => 'required',
            'testenddate' => 'required',
            'questiondata' => 'required',
        ]);

        if ($request->id) {
            $data = OnlineTests::find($request->id);
            $msg = 'Test updated successfully';
        } else {
            $data = new OnlineTests();
            $msg = 'Test added successfully';
        }
        $data->name = $request->testname;
        $data->test_type = $request->test_type;
        $data->description = $request->testdescription;
        $data->class_id = $request->classname;
        $data->subject_id = $request->subject;
        $data->topic_name = $request->topic;
        $data->max_attempt = $request->maxattempt;
        $data->test_duration = $request->duration;
        $data->test_start_date = $request->tstartdate;
        $data->test_end_date = $request->testenddate;
        $data->question_id = json_encode($request->questiondata);
        $data->tutor_id = session('userid')->id;
        $res = $data->save();

        if ($res) {
            return back()->with('success', $msg);
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }

    public function edit($id)
    {
        $tdata = OnlineTests::select('*')->where('id', $id)->first();
        $classes = (new CommonController)->classes();
        $subjects = subjects::select('*')->where('class_id', $tdata->class_id)->where('is_active', 1)->get();
        $topics = topics::select('*')->where('subject_id', $tdata->subject_id)->where('is_active', 1)->get();
        $questions = questionbank::select('*')->where('subject_id', $tdata->subject_id)->where('type', $tdata->test_type)->where('is_active', 1)->get();
        $questiondatas = OnlineTests::select('question_id')->where('id', $tdata->id)->first();

        // $questiondata = explode(',', $tdata->question_id);
        // $data = ModelName::find($id);
        $qstn = explode('"', $tdata->question_id);
        // return view('package.edit', ['data' => $data,'months' => $SelectedMonths]);
        // foreach($prodmulti as $test)
        // echo $months ;
        // echo "<pre>";
        // dd($months);
        // endforeach
        // $keywords = preg_split('/[\s,-,"]+/', $tdata->question_id);
        // dd($months);
        return view('admin.onlinetestnew', compact(['tdata', 'classes', 'subjects', 'topics', 'questions', 'questiondatas', 'qstn']));
    }

    public function viewquestions($id)
    {
        // Fetch question details based on testid -> Using jQuerry
        $data['questions'] = OnlineTests::where("id", $id)->first();
        return response()->json($data);
    }

    public function studentexams()
    {
        $classes = (new CommonController)->classes();
        $subjects = subjects::where('is_active', 1)->get();
        $studentId = session('userid')->id;
        $nowUtc = Carbon::now('UTC');

        // NOTE: The student-visible exam window is defined per-assignment (assign_tests.start_time/end_time),
        // stored in UTC. online_tests.test_start_date/test_end_date are admin-level fields and may differ.
        $exams = OnlineTests::select(
            'online_tests.*',
            'classes.name as class',
            'subjects.name as subject',
            'online_tests.topic_name as topic',
            'assign_tests.start_time as assigned_start_time',
            'assign_tests.end_time as assigned_end_time'
        )
            ->join('assign_tests', 'assign_tests.test_id', '=', 'online_tests.id')
            ->leftJoin('classes', 'classes.id', '=', 'online_tests.class_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'online_tests.subject_id')
            ->where('assign_tests.status', 1)
            ->where('assign_tests.is_attempted', 0)
            ->where('assign_tests.student_id', $studentId)
            ->orderBy('assign_tests.start_time', 'asc')
            ->get();

        foreach ($exams as $exam) {
            $startUtc = $exam->assigned_start_time ? Carbon::parse($exam->assigned_start_time, 'UTC') : null;
            $endUtc = $exam->assigned_end_time ? Carbon::parse($exam->assigned_end_time, 'UTC') : null;

            $exam->display_start = TimezoneHelper::formatInUserTz($exam->assigned_start_time, 'd-m-Y h:i A', 'UTC');
            $exam->display_end = TimezoneHelper::formatInUserTz($exam->assigned_end_time, 'd-m-Y h:i A', 'UTC');

            $exam->can_start = false;
            $exam->start_status = 'Unavailable';

            if ($startUtc && $endUtc) {
                if ($nowUtc->lt($startUtc)) {
                    $exam->start_status = 'Not started yet';
                } elseif ($nowUtc->gt($endUtc)) {
                    $exam->start_status = 'Expired';
                } else {
                    $exam->can_start = true;
                    $exam->start_status = 'Available';
                }
            }
        }
        // dd($exams);
        // foreach ($exams as $exam) {
        //     $exam->attemptsRemaining = $exam->max_attempt - testattempted::where('student_id', session('userid')->id)
        //         ->where('test_id', $exam->id)
        //         ->count();
        // }

        $extakens = testattempted::select(
            'testattempteds.*',
            'online_tests.name as exam_name',
            'online_tests.description as exam_description',
            'online_tests.test_duration as duration',
            'assign_tests.start_time as assigned_start_time',
            'assign_tests.end_time as assigned_end_time'
        )
            ->join('online_tests', 'online_tests.id', '=', 'testattempteds.test_id')
            ->leftJoin('assign_tests', function ($join) use ($studentId) {
                $join->on('assign_tests.test_id', '=', 'testattempteds.test_id')
                    ->where('assign_tests.student_id', '=', $studentId);
            })
            ->where('testattempteds.student_id', $studentId)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'asc')
            ->get();

        foreach ($extakens as $extaken) {
            $extaken->display_start = TimezoneHelper::formatInUserTz($extaken->assigned_start_time, 'd-m-Y h:i A', 'UTC');
            $extaken->display_end = TimezoneHelper::formatInUserTz($extaken->assigned_end_time, 'd-m-Y h:i A', 'UTC');
        }

        return view('student.exam', get_defined_vars());
    }

    public function studentexamsParent()
    {

        $classes = (new CommonController)->classes();
        $subjects = subjects::where('is_active', 1)->get();
        $exams = OnlineTests::select('online_tests.*', 'classes.name as class', 'subjects.name as subject', 'topics.name as topic')
            ->join('classes', 'classes.id', 'online_tests.class_id')
            ->join('subjects', 'subjects.id', 'online_tests.subject_id')
            ->join('topics', 'topics.id', 'online_tests.topic_id')
            ->get();
        foreach ($exams as $exam) {
            $exam->attemptsRemaining = $exam->max_attempt - testattempted::where('student_id', session('userid')->id)
                ->where('test_id', $exam->id)
                ->count();
        }

        $extakens = testattempted::select('testattempteds.*', 'online_tests.name as exam_name', 'online_tests.description as exam_description', 'online_tests.test_duration as duration', 'online_tests.test_start_date as test_start_date', 'online_tests.test_end_date as test_end_date')
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)->where('testattempteds.is_active', 1)->orderBy('testattempteds.created_at', 'desc')->get();

        return view('parent.exam', get_defined_vars());
    }

    // search functionality
    public function studentexamsSearch(Request $request)
    {
        // return $request->all();
        $classes = (new CommonController)->classes();
        $subjects = subjects::where('is_active', 1)->get();
        $studentId = session('userid')->id;
        $nowUtc = Carbon::now('UTC');

        $query = OnlineTests::select(
            'online_tests.*',
            'classes.name as class',
            'subjects.name as subject',
            'online_tests.topic_name as topic',
            'assign_tests.start_time as assigned_start_time',
            'assign_tests.end_time as assigned_end_time'
        )
            ->leftJoin('classes', 'classes.id', '=', 'online_tests.class_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'online_tests.subject_id')
            ->join('assign_tests', 'assign_tests.test_id', '=', 'online_tests.id')
            ->where('assign_tests.status', 1)
            ->where('assign_tests.is_attempted', 0)
            ->where('assign_tests.student_id', $studentId);
        // ->get();


        if ($request->class_name) {
            $query->where('online_tests.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('online_tests.subject_id', $request->subject_name);
        }
        if ($request->topic) {
            $query->where('online_tests.topic_name', 'like', '%' . $request->topic . '%');
        }
        $exams = $query->paginate(10);

        foreach ($exams as $exam) {
            $startUtc = $exam->assigned_start_time ? Carbon::parse($exam->assigned_start_time, 'UTC') : null;
            $endUtc = $exam->assigned_end_time ? Carbon::parse($exam->assigned_end_time, 'UTC') : null;

            $exam->display_start = TimezoneHelper::formatInUserTz($exam->assigned_start_time, 'd-m-Y h:i A', 'UTC');
            $exam->display_end = TimezoneHelper::formatInUserTz($exam->assigned_end_time, 'd-m-Y h:i A', 'UTC');

            $exam->can_start = false;
            $exam->start_status = 'Unavailable';
            if ($startUtc && $endUtc) {
                if ($nowUtc->lt($startUtc)) {
                    $exam->start_status = 'Not started yet';
                } elseif ($nowUtc->gt($endUtc)) {
                    $exam->start_status = 'Expired';
                } else {
                    $exam->can_start = true;
                    $exam->start_status = 'Available';
                }
            }
        }
        $type = 'student-exams';
        $viewTable = view('admin.partials.common-search', compact('exams', 'type'))->render();
        $viewPagination = $exams->links()->render();
        $extakens = testattempted::select(
            'testattempteds.*',
            'online_tests.name as exam_name',
            'online_tests.description as exam_description',
            'online_tests.test_duration as duration',
            'assign_tests.start_time as assigned_start_time',
            'assign_tests.end_time as assigned_end_time'
        )
            ->join('online_tests', 'online_tests.id', '=', 'testattempteds.test_id')
            ->leftJoin('assign_tests', function ($join) use ($studentId) {
                $join->on('assign_tests.test_id', '=', 'testattempteds.test_id')
                    ->where('assign_tests.student_id', '=', $studentId);
            })
            ->where('testattempteds.student_id', $studentId)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();

        foreach ($extakens as $extaken) {
            $extaken->display_start = TimezoneHelper::formatInUserTz($extaken->assigned_start_time, 'd-m-Y h:i A', 'UTC');
            $extaken->display_end = TimezoneHelper::formatInUserTz($extaken->assigned_end_time, 'd-m-Y h:i A', 'UTC');
        }


        return view('student.exam', get_defined_vars());
    }

    public function taketest($id)
    {
        $studentId = session('userid')->id;

        $assignment = AssignTest::where('test_id', $id)
            ->where('student_id', $studentId)
            ->where('status', 1)
            ->where('is_attempted', 0)
            ->first();

        if (!$assignment) {
            return back()->with('fail', 'Test is not assigned to you or is no longer available.');
        }

        $nowUtc = Carbon::now('UTC');
        $startUtc = $assignment->start_time ? Carbon::parse($assignment->start_time, 'UTC') : null;
        $endUtc = $assignment->end_time ? Carbon::parse($assignment->end_time, 'UTC') : null;

        if ($startUtc && $nowUtc->lt($startUtc)) {
            return back()->with('fail', 'Test has not started yet.');
        }
        if ($endUtc && $nowUtc->gt($endUtc)) {
            return back()->with('fail', 'Test time has ended. You can no longer start this test.');
        }

        $onlineTest = OnlineTests::where('id', $id)
            ->first();
        if (!$onlineTest) {
            return back()->with('fail', 'Test not found.');
        }

        // Decode the JSON string to an array
        $questionIds = json_decode($onlineTest->question_id);
        // Fetch the related questions using the decoded question_ids array
        $questions = Questionbank::whereIn('id', $questionIds)->get();

        return view('student.taketest', compact('onlineTest', 'questions'));
    }
    public function taketestsubjective($id)
    {
        // echo $id;
        $studentId = session('userid')->id;

        $assignment = AssignTest::where('test_id', $id)
            ->where('student_id', $studentId)
            ->where('status', 1)
            ->where('is_attempted', 0)
            ->first();

        if (!$assignment) {
            return back()->with('fail', 'Test is not assigned to you or is no longer available.');
        }

        $nowUtc = Carbon::now('UTC');
        $startUtc = $assignment->start_time ? Carbon::parse($assignment->start_time, 'UTC') : null;
        $endUtc = $assignment->end_time ? Carbon::parse($assignment->end_time, 'UTC') : null;

        if ($startUtc && $nowUtc->lt($startUtc)) {
            return back()->with('fail', 'Test has not started yet.');
        }
        if ($endUtc && $nowUtc->gt($endUtc)) {
            return back()->with('fail', 'Test time has ended. You can no longer start this test.');
        }

        $onlineTest = OnlineTests::where('id', $id)
            // ->where('class_id', session('userid')->class_id)
            ->first();
        if (!$onlineTest) {
            return back()->with('fail', 'Test not found.');
        }

        // echo session('userid')->class_id;
        // dd($onlineTest);
        // Decode the JSON string to an array
        $questionIds = json_decode($onlineTest->question_id);
        // dd($onlineTest);
        // Fetch the related questions using the decoded question_ids array
        $questions = Questionbank::whereIn('id', $questionIds)->get();


        return view('student.take-subjectivetest', get_defined_vars());
    }



    public function saveResponses(Request $request, TwilioWhatsAppService $whatsApp)
    {
        $responses = $request->input('responses'); // Assuming the responses are sent as an array

        $savedId = [];
        $test_id = "";
        $attemptNumber = "";
        foreach ($responses as $response) {
            if ($response) {
                $values = explode(',', $response);

                $copt = questionbank::select('*')->where('id', $values[0])->first();
                $correctOption = $copt['correct_option'];
                $correct_option = "";
                // Loop through the options and check if any option matches the correct option
                foreach (range(1, 4) as $optionNumber) {
                    $optionField = "option{$optionNumber}";
                    $optionValue = $copt[$optionField];

                    if ($optionValue === $correctOption) {
                        // Option $optionNumber is the correct answer
                        $correct_option = $optionNumber;
                        break; // No need to check other options
                    }
                }
                // checking attempt no.
                $totalattp = OnlineTests::select('max_attempt')->where('id', $values[2])->first();
                $alreadyattp = testattempted::select('*')->where('student_id', session('userid')->id)->where('test_id', $values[2])->count();

                $remaining = ($totalattp->max_attempt) - ($alreadyattp);
                $attemptNumber = $alreadyattp + 1;

                $data = new testresponssheet();
                $data->test_id = $values[2];
                $data->student_id = session('userid')->id;
                $data->attempt_no = $attemptNumber;
                $data->question_id = $values[0];
                $data->correct_option = $correct_option;
                $data->marked_option = $values[1];

                $data->save();
                // Access the ID of the saved record

                $savedId[] = $data->id;
                $test_id = $values[2];

            }
        }
        // calculating the total marks
        $marks_ttl = 50;
        $marks_obt = 28;
        // Saving final test data
        $data = new testattempted();
        $data->student_id = session('userid')->id;
        $data->test_id = $test_id;
        $data->attempt_no = $attemptNumber;
        $data->test_attempted_on = now();
        $data->test_time_taken = 0;
        $data->total_marks = $marks_ttl;
        $data->obtained_marks = $marks_obt;
        $data->test_type = 1;
        $data->response_id = json_encode($savedId);
        // $data->status = ;
        // $data->is_active = session('userid')->id;
        $data->save();
        // Update is_attempted in assign_tests table
        AssignTest::where('test_id', $test_id)
            ->where('student_id', session('userid')->id)
            ->update(['is_attempted' => 1]);

        $tutor_id = AssignTest::select('*')->where('test_id', $test_id)->where('student_id', session('userid')->id)->first();

        //////////////// Here I need to pass notification into db
        $notificationdata = new Notification();
        $notificationdata->alert_type = 4;
        $notificationdata->notification = 'Test Attempted By ' . session('userid')->name;
        $notificationdata->initiator_id = session('userid')->id;
        $notificationdata->initiator_role = session('userid')->role_id;
        $notificationdata->event_id = $test_id;
        // Sending to admin
        // if($request->receiver_role_id == 1){
        //     $notificationdata->show_to_admin = 1;
        //     $notificationdata->show_to_admin_id = $request->receiver_id;
        //     // $notificationdata->show_to_all_admin = 1;
        // }
        // Sending to tutor
        // if($request->receiver_role_id == 2){
        $notificationdata->show_to_tutor = 1;
        $notificationdata->show_to_tutor_id = $tutor_id->tutor_id;
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

        $notificationdata->save();

        // 2. WhatsApp Alert Logic to Tutor
        try {
            // We already have $tutor_id from your AssignTest query
            $tutor = tutorregistration::find($tutor_id->tutor_id); 
            $student = session('userid'); // The logged-in student
            $test = OnlineTests::find($test_id);

            if ($tutor && !empty($tutor->mobile)) {
                // Use the new Template ID you created for Submissions (e.g., 2263)
                $templateId = 2263; 
                $tutorNumber = $tutor->mobile;

                // Note: For tutors, we usually show the time in their local timezone
                $submittedAt = TimezoneHelper::formatInUserTz(
                    now(), 
                    'd M Y h:i A', 
                    'UTC', 
                    $tutor
                );

                // Map to variables based on: 
                $bodyVariables = [
                    $student->name,    // {{1}}
                    $test->name,       // {{2}}
                    $submittedAt,      // {{3}}
                ];

                // Ensure $whatsApp is initialized (e.g., $whatsApp = new VeevoTechService())
                $sent = $whatsApp->sendMessage(
                    $tutorNumber,
                    $bodyVariables,
                    $templateId
                );

                if ($sent) {
                    Log::info("WhatsApp Submission Alert sent to Tutor", [
                        'tutor_id' => $tutor->id,
                        'student_id' => $student->id
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // We log it so the student doesn't see a 500 error if WhatsApp fails
            Log::error("WhatsApp Tutor Submission Notification Error: " . $e->getMessage());
        }

        broadcast(new RealTimeMessage('$notification'));

        return response()->json(['message' => 'Test Submitted Successfully']);
    }

    public function saveSubjectiveResponses(Request $request)
    {
        $testId = $request->testId;
        $questionIds = $request->questionIds;
        $savedId = [];
        $tutor_id = AssignTest::select('*')->where('test_id', $testId)->where('student_id', session('userid')->id)->first();

        if (count($questionIds) > 0) {
            foreach ($questionIds as $questionId) {
                $temprec = TemporarySubjective::where('std_id', session('userid')->id)->where('test_id', $testId)->where('question_id', $questionId)->first();

                // dd($temprec);
                if ($temprec) {
                    $data = new SubjectiveResponse;
                    $data->test_id = $testId;
                    $data->student_id = session('userid')->id;
                    $data->question_id = $questionId;
                    $data->response = $temprec->answer;
                    $data->total_marks = null;
                    $data->obtained_marks = null;
                    $data->remarks = null;
                    $data->save();
                    $savedId[] = $data->id;
                }
            }
            $totalattp = OnlineTests::select('max_attempt')->where('id', $testId)->first();
            $alreadyattp = testattempted::select('*')->where('student_id', session('userid')->id)->where('test_id', $testId)->count();

            $remaining = ($totalattp->max_attempt) - ($alreadyattp);
            $attemptNumber = $alreadyattp + 1;

            $data = new testattempted();
            $data->student_id = session('userid')->id;
            $data->test_id = $testId;
            $data->attempt_no = $attemptNumber;
            $data->test_attempted_on = now();
            $data->test_time_taken = 0;
            $data->total_marks = 0;
            $data->obtained_marks = 0;
            $data->response_id = 0;
            $data->answer = json_encode($savedId);
            $data->test_type = 2;
            $data->save();
            $temprec = TemporarySubjective::where('std_id', session('userid')->id)->where('test_id', $testId)->delete();

            $notificationdata = new Notification();
            $notificationdata->alert_type = 4;
            $notificationdata->notification = 'Test Submitted By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $testId;
            $notificationdata->show_to_tutor = 1;
            $notificationdata->show_to_tutor_id = $tutor_id->tutor_id;

            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();
            broadcast(new RealTimeMessage('$notification'));

            // Update is_attempted in assign_tests table
            AssignTest::where('test_id', $testId)
                ->where('student_id', session('userid')->id)
                ->update(['is_attempted' => 1]);
            return response()->json(['message' => 'Test Submitted Successfully']);
        }
    }
    public function testreport($id)
    {
        // dd($id);
        // $assigntdata = AssignTest::find($id);
        // $testid = testattempted::where('test_id', $assigntdata->test_id)->first();
        $testid = testattempted::find($id);
        // $onlineTest = OnlineTests::where('id', $testid->test_id)->orderBy('id','desc')->first();
        $onlineTest = OnlineTests::where('id', $testid->test_id)->orderBy('created_at', 'asc')->first();

        if ($onlineTest->test_type == 1) {

            $questionIds = json_decode($onlineTest->question_id);
            $responseIds = json_decode($testid->response_id);
            $questionsCount = Questionbank::whereIn('id', $questionIds)->count();
            $questions = Questionbank::whereIn('id', $questionIds)->get();

            // Initialize mergedData array
            $mergedData = [];

            foreach ($questions as $question) {
                $questionData = [
                    'question' => $question->question,
                    'option1' => $question->option1,
                    'option2' => $question->option2,
                    'option3' => $question->option3,
                    'option4' => $question->option4,
                    'correct_answer' => $this->getOptionNumber($question, $question->correct_option),
                ];

                // Find the corresponding response for the question
                $response = $responseIds > 0 ? testresponssheet::whereIn('id', $responseIds)->where('question_id', $question->id)->first() : null;

                if ($response) {
                    $questionData['marked_answer'] = intval($response->marked_option);
                } else {
                    $questionData['marked_answer'] = '';
                }


                $mergedData[] = $questionData;
            }
            // dd($mergedData);
            // Continue with the rest of your code...

            if ($responseIds > 0) {
                $responsesCount = testresponssheet::whereIn('id', $responseIds)->count();
                $responsesCn = testresponssheet::whereIn('id', $responseIds)->get();
                $correctResponsesCount = testresponssheet::whereIn('id', $responseIds)->whereColumn('correct_option', 'marked_option')->count();
            } else {
                $responsesCount = 0;
                $correctResponsesCount = 0;
            }
            // dd($mergedData);
            return view('student.testreport', compact('onlineTest', 'questionsCount', 'responsesCount', 'correctResponsesCount', 'mergedData'));
        } else {
            $response = testattempted::find($id);

            if ($response) {
                $responseIds = json_decode($response->answer);
                $finalResponses = SubjectiveResponse::select('subjective_responses.*', 'questionbanks.question')
                    ->join('questionbanks', 'questionbanks.id', 'subjective_responses.question_id')
                    ->whereIn('subjective_responses.id', $responseIds)
                    ->get();

                // Check if any of the responses is not checked
                $uncheckedResponses = $finalResponses->first(function ($response) {
                    return $response->checked == 0;
                });

                if ($uncheckedResponses) {
                    return back()->with('fail', 'Test not yet checked. Please wait or contact tutor');
                } else {
                    $test = OnlineTests::find($response->test_id);
                    $questionIds = json_decode($test->question_id);
                    $questions = questionbank::whereIn('id', $questionIds)->get();
                    $student = studentregistration::find(session('userid')->id);

                    return view('student.onlinetestresponses-student', get_defined_vars());
                }
            }
        }
    }

    public function onlinetestresponseslist()
    {
        $subs = tutorsubjectmapping::pluck('subject_id')->toArray();
        if ($subs) {
            $onlineTests = OnlineTests::select('online_tests.*', 'subjects.name as sub_name', 'classes.name as class_name', 'online_tests.topic_name as topic_name')
                ->join('classes', 'classes.id', 'online_tests.class_id')
                ->join('subjects', 'subjects.id', 'online_tests.subject_id')
                ->join('testattempteds', 'testattempteds.test_id', 'online_tests.id')
                ->join('paymentstudents', 'paymentstudents.student_id', 'testattempteds.student_id')
                ->where('online_tests.subject_id', 'paymentstudents.subject_id')
                ->where('online_tests.test_type', 2)
                ->where('paymentstudents.tutor_id', session('userid')->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            $classes = classes::where('is_active', 1)->get();
            $subjects = subjects::where('is_active', 1)->get();
            $topics = topics::where('is_active', 1)->get();
            // dd();
            return view('admin.onlinetestresponselist', get_defined_vars());
        } else {
            return back()->with('fail', 'No tests Found');
        }
    }
    public function onlinetestresponse($id)
    {
        return view('admin.onlinetestresponses');
    }

    // tutor subjectve responses
    public function onlinetestresponseslistTutor()
    {
        $subs = tutorsubjectmapping::where('tutor_id', session('userid')->id)->pluck('subject_id')->toArray();
        if ($subs) {
            $onlineTests = OnlineTests::select(
                'online_tests.id',
                'online_tests.class_id',
                'online_tests.subject_id',
                'online_tests.test_type',
                'online_tests.created_at',
                'online_tests.updated_at',
                'subjects.name as sub_name',
                'classes.name as class_name',
                'online_tests.topic_name as topic_name'
            )
                ->join('classes', 'classes.id', '=', 'online_tests.class_id')
                ->join('subjects', 'subjects.id', '=', 'online_tests.subject_id')
                ->join('testattempteds', 'testattempteds.test_id', '=', 'online_tests.id')
                ->join('paymentstudents', 'paymentstudents.student_id', '=', 'testattempteds.student_id')
                ->where('online_tests.test_type', 2)
                ->where('paymentstudents.tutor_id', session('userid')->id)
                // ->groupBy('online_tests.id') // Group by the test ID to ensure uniqueness
                ->distinct()
                ->orderBy('online_tests.created_at', 'desc')
                ->paginate(10);

            $classes = Classes::where('is_active', 1)->get();
            $subjects = Subjects::where('is_active', 1)->get();
            $topics = Topics::where('is_active', 1)->get();

            return view('tutor.onlinetestresponselist-tutor', get_defined_vars());
        } else {
            return back()->with('fail', 'No tests Found');
        }
    }

    public function subjTestsSearch(Request $request)
    {
        $subs = tutorsubjectmapping::where('tutor_id', session('userid')->id)->pluck('subject_id')->toArray();
        if ($subs) {
            $query = OnlineTests::select('online_tests.*', 'subjects.name as sub_name', 'classes.name as class_name', 'topics.name as topic_name')->join('classes', 'classes.id', 'online_tests.class_id')->join('subjects', 'subjects.id', 'online_tests.subject_id')->join('topics', 'topics.id', 'online_tests.topic_id')->whereIn('online_tests.subject_id', $subs)->where('online_tests.test_type', 2);

            if ($request->test_name) {
                $query->where('online_tests.name', 'like', '%' . $request->test_name . '%');
            }
            if ($request->class_name) {
                $query->where('online_tests.class_id', $request->class_name);
            }
            if ($request->subject_name) {
                $query->where('online_tests.subject_id', $request->subject_name);
            }
            if ($request->topic_name) {
                $query->where('online_tests.topic_id', $request->topic_name);
            }
            if ($request->start_date) {
                $query->whereDate(DB::raw('DATE(online_tests.test_start_date)'), '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate(DB::raw('DATE(online_tests.test_end_date)'), '<=', $request->end_date);
            }
            $onlineTests = $query->paginate(10);
            $type = 'tutor_subjective_tests';
            $viewTable = view('admin.partials.students-tutor-search', compact('onlineTests', 'type'))->render();
            $viewPagination = $onlineTests->links()->render();
            return response()->json([
                'table' => $viewTable,
                'pagination' => $viewPagination
            ]);
        }
    }

    public function onlinetestresponseTutor(Request $request, $test_id)
    {
        $responses = testattempted::select('testattempteds.*', 'studentregistrations.name as std_name')->join('studentregistrations', 'studentregistrations.id', 'testattempteds.student_id')->where('testattempteds.test_id', $test_id)->where('testattempteds.test_type', 2)->paginate(10);
        $test_name = OnlineTests::find($test_id);
        return view('tutor.onlinetestresponses-tutor', get_defined_vars());
    }
    public function studentwiseSubjSearch(Request $request, $test_id)
    {

        $query = testattempted::select('testattempteds.*', 'studentregistrations.name as std_name')->join('studentregistrations', 'studentregistrations.id', 'testattempteds.student_id')->where('testattempteds.test_id', $test_id)->where('testattempteds.test_type', 2);
        if ($request->student_name) {
            $query->where('studentregistrations.name', 'like', '%' . $request->student_name . '%');
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(testattempteds.test_attempted_on)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(testattempteds.test_attempted_on)'), '<=', $request->end_date);
        }
        $responses = $query->paginate(10);
        $type = 'tutor_subjective_responses';
        $viewTable = view('admin.partials.students-tutor-search', compact('responses', 'type'))->render();
        $viewPagination = $responses->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }

    public function onlinetestresponsestudentTutor($response_id)
    {
        $response = testattempted::find($response_id);
        if ($response) {
            $responseIds = json_decode($response->answer);
            $finalResponses = SubjectiveResponse::select('subjective_responses.*', 'questionbanks.question')->join('questionbanks', 'questionbanks.id', 'subjective_responses.question_id')->whereIn('subjective_responses.id', $responseIds)->get();
            $test = OnlineTests::find($response->test_id);
            $questionIds = json_decode($test->question_id);
            $questions = questionbank::whereIn('id', $questionIds)->get();
            $student = studentregistration::find($response->student_id);
            return view('tutor.onlinetestresponsesstudent-tutor', get_defined_vars());
        }
    }

    public function testCorrection(Request $request, $response_id)
    {
        $response = testattempted::find($response_id);
        if ($response) {
            $responseIds = json_decode($response->answer);
            $finalResponses = SubjectiveResponse::whereIn('subjective_responses.id', $responseIds)->get();
            $test = OnlineTests::find($response->test_id);
            $questionIds = json_decode($test->question_id);
            $questions = questionbank::whereIn('id', $questionIds)->get();

            $rules = [];
            $messages = [];

            foreach ($questions as $question) {
                $fieldPrefix = "{$question->id}";

                // Define rules for max_marks and marks_obtained
                $rules["max_marks.{$fieldPrefix}"] = 'required|numeric';
                $rules["marks_obtained.{$fieldPrefix}"] = "required|numeric|min:0|max:{$request->input("max_marks.{$fieldPrefix}")}";

                // Define custom error messages
                $messages["max_marks.{$fieldPrefix}.required"] = "Max Marks for Question  is required.";
                $messages["max_marks.{$fieldPrefix}.numeric"] = "Max Marks for Question  must be a numeric value.";
                $messages["marks_obtained.{$fieldPrefix}.required"] = "Marks Obtained for Question  is required.";
                $messages["marks_obtained.{$fieldPrefix}.numeric"] = "Marks Obtained for Question  must be a numeric value.";
                $messages["marks_obtained.{$fieldPrefix}.min"] = "Marks Obtained for Question  must be at least 0.";
                $messages["marks_obtained.{$fieldPrefix}.max"] = "Marks Obtained for Question  cannot be greater than Max Marks.";

                // // Define custom validation attribute names
                // $attributes["max_marks.{$fieldPrefix}"] = "Max Marks for Question {$question->id}";
                // $attributes["marks_obtained.{$fieldPrefix}"] = "Marks Obtained for Question {$question->id}";
                // $attributes["remarks.{$fieldPrefix}"] = "Remarks for Question {$question->id}";
            }

            // Create the validator instance
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }




            $totalTotalMarks = 0;
            $totalObtainedMarks = 0;


            foreach ($questions as  $question) {
                foreach ($finalResponses as  $SubjectiveResponse) {
                    if ($SubjectiveResponse->question_id === $question->id) {
                        $SubjectiveResponse->total_marks = $request->max_marks[$question->id];
                        $SubjectiveResponse->obtained_marks = $request->marks_obtained[$question->id];
                        $SubjectiveResponse->remarks = $request->remarks[$question->id];
                        $SubjectiveResponse->checked_by = session('userid')->id;
                        $SubjectiveResponse->checked = session('userid')->id;
                        $SubjectiveResponse->save();
                        $totalTotalMarks += $SubjectiveResponse->total_marks;
                        $totalObtainedMarks += $SubjectiveResponse->obtained_marks;
                    }
                }
            }
            $response->total_marks = $totalTotalMarks;
            $response->obtained_marks = $totalObtainedMarks;
            $response->status = 1;
            $response->save();

            $student_id = AssignTest::select('*')->where('test_id', $response->test_id)->first();

            $notificationdata = new Notification();
            $notificationdata->alert_type = 4;
            $notificationdata->notification = 'Report Submited By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $response->test_id;
            $notificationdata->show_to_student = 1;
            $notificationdata->show_to_student_id = $student_id->student_id;
            $notificationdata->read_status = 0;

            $notified = $notificationdata->save();
            broadcast(new RealTimeMessage('$notification'));

            return redirect(url('tutor/onlinetestresponseslist'))->with('success', 'Marks Submitted');
        }
    }



    //  Tutor online tests

    public function tutorindex()
    {
        $tutorId = session('userid')->id;
        if (!$tutorId) {
            return redirect()->route('home')->with('fail', 'Please login to access the tutor dashboard.');
        }
        $testlists = OnlineTests::select('*', 'online_tests.id as test_id', 'online_tests.name as test_name', 'online_tests.description as test_description', 'online_tests.is_active as test_status', 'classes.name as class_name', 'subjects.name as subject_name', 'online_tests.topic_name as topic_name')
            ->join('classes', 'classes.id', 'online_tests.class_id')
            ->join('subjects', 'subjects.id', 'online_tests.subject_id')
            ->where('online_tests.tutor_id', $tutorId)
            ->orderby('online_tests.created_at', 'desc')
            ->paginate(10);
        $classes = classes::where('is_active', 1)->get();
        $subjects = subjects::where('is_active', 1)->get();
        $topics = topics::where('is_active', 1)->get();
        $students = studentregistration::select('studentregistrations.*')
            ->leftJoin('paymentstudents', function ($join) {
                $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
                    ->where('paymentstudents.tutor_id', '=', session('userid')->id);
            })
            ->whereNotNull('paymentstudents.student_id')
            ->distinct()
            ->where('studentregistrations.is_active', 1)
            ->get();
        return view('tutor.tutor-onlinetestlist', get_defined_vars());
    }

    function assigntest($id)
    {
        $testdata = OnlineTests::select('*')->where('id', $id)->where('is_active', 1)->first();

        if (!$testdata) {
            return back()->with('fail', 'Test not found or inactive.');
        }
        $students = studentregistration::select('studentregistrations.*')
            ->leftJoin('paymentstudents', function ($join) {
                $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
                    ->where('paymentstudents.tutor_id', '=', session('userid')->id);
            })
            ->whereNotNull('paymentstudents.student_id')
            ->distinct()
            ->where('studentregistrations.is_active', 1)
            ->get();

        $test_id = $id;

        $studentdata = AssignTest::select('assign_tests.*', 'online_tests.name as test_name', 'studentregistrations.name as student_name')
            ->join('online_tests', 'online_tests.id', 'assign_tests.test_id')
            ->join('studentregistrations', 'studentregistrations.id', 'assign_tests.student_id')
            ->where('assign_tests.test_id', $id)
            ->where('assign_tests.tutor_id', session('userid')
                ->id)->where('assign_tests.is_active', 1)
            ->get();
        // dd($studentdata);
        return view('tutor.assigntest', get_defined_vars());
    }

    public function assigntestdata(Request $request, TwilioWhatsAppService $whatsApp)
    {
        $request->validate([
            'testid' => 'required',
            'student' => 'required',
            'starttime' => 'required',
            'endtime' => 'required',
        ]);

        $datachk = AssignTest::select('*')
            ->where('test_id', $request->testid)
            ->where('student_id', $request->student)
            ->where('tutor_id', session('userid')->id)
            ->first();
        // dd($datachk);

        if ($datachk) {
            return back()->with('fail', 'Test already assigned to this student');
        }
        // datetime-local is wall clock in the tutor's browser locale; interpret it in the tutor's TZ, then persist UTC.
        $tutorTz = TimezoneHelper::userTimezone(session('userid'));
        $utcStart = Carbon::parse($request->starttime, $tutorTz)->utc();
        $utcEnd = Carbon::parse($request->endtime, $tutorTz)->utc();

        $assigntest = new AssignTest();
        $assigntest->test_id = $request->testid;
        $assigntest->student_id = $request->student;
        $assigntest->tutor_id = session('userid')->id;
        $assigntest->start_time = $utcStart;
        $assigntest->end_time = $utcEnd;
        $assigntest->status = 1;
        $res = $assigntest->save();

        if ($res) {
            //////////////// Here I need to pass notification into db
            $notificationdata = new Notification();
            $notificationdata->alert_type = 4;
            $notificationdata->notification = 'Test Assigned By ' . session('userid')->name;
            $notificationdata->initiator_id = session('userid')->id;
            $notificationdata->initiator_role = session('userid')->role_id;
            $notificationdata->event_id = $request->testid;
            // Sending to admin
            // if($request->receiver_role_id == 1){
            //     $notificationdata->show_to_admin = 1;
            //     $notificationdata->show_to_admin_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_admin = 1;
            // }
            // Sending to tutor
            // if($request->receiver_role_id == 2){
            // $notificationdata->show_to_tutor = 1;
            // $notificationdata->show_to_tutor_id = $tutor_id->tutor_id;
            // $notificationdata->show_to_all_tutor = 0;
            // }
            // Sending to student
            // if($request->receiver_role_id == 3){
            $notificationdata->show_to_student = 1;
            $notificationdata->show_to_student_id = $request->student;
            //     // $notificationdata->show_to_all_student = 0;
            // }
            // // Sending to parent
            // if($request->receiver_role_id == 3){
            //     $notificationdata->show_to_parent = 1;
            //     $notificationdata->show_to_parent_id = $request->receiver_id;
            //     // $notificationdata->show_to_all_parent = 0;
            // }
            $notificationdata->read_status = 0;

            $notificationdata->save();


            // 2. WhatsApp Alert Logic
            try {
                $student = studentregistration::find($request->student);
                $test = OnlineTests::find($request->testid);
                $tutor = session('userid');


                if ($student && !empty($student->mobile)) {
                    $templateId = 2262;
                    $studentNumber = $student->mobile;

                    // Format the start time for the student's timezone
                    // 1. Format both times
                    $formattedStart = TimezoneHelper::formatInUserTz(
                        $utcStart,
                        'd M Y h:i A',
                        'UTC',
                        $student
                    );

                    $formattedEnd = TimezoneHelper::formatInUserTz(
                        $utcEnd,
                        'd M Y h:i A',
                        'UTC',
                        $student
                    );

                    // 2. Map to variables (Ensure your Template ID 2071 has 5 variables now)
                    $bodyVariables = [
                        $student->name,    // {{2}}
                        $tutor->name,      // {{1}}
                        $test->name,       // {{3}}
                        $formattedStart,   // {{4}}
                        $formattedEnd      // {{5}}
                    ];

                    $sent = $whatsApp->sendMessage(
                        $studentNumber,
                        $bodyVariables,
                        $templateId
                    );

                    if ($sent) {
                        Log::info("WhatsApp Test Alert sent to Student", [
                            'student_id' => $student->id,
                            'test_id' => $test->id
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error("WhatsApp Student Notification Error: " . $e->getMessage());
            }
            broadcast(new RealTimeMessage('$notification'));
            return back()->with('success', 'Student assigned to test successfully!');
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }

    function assigntestdelete(Request $request)
    {
        $request->validate([
            'assigntestdeleteid' => 'required',
        ]);

        // dd($request->all());
        // Assuming SlotBooking is the model associated with the slot_bookings table
        $slotbooking = AssignTest::where('id', $request->assigntestdeleteid)
            ->first();

        if ($slotbooking) {
            // Found the slot, now delete it
            $slotbooking->delete();

            return back()->with('success', 'Record deleted successfully!');
        } else {
            // Slot not found
            return back()->with('fail', 'Record not found or you do not have permission to delete it.');
        }
    }

    public function tutorcreate()
    {
        $classes = (new CommonController)->classes();
        return view('tutor.tutor-onlinetestnew', compact('classes'));
    }
    public function tutorstore(Request $request)
    {
        $request->validate([
            'testname' => 'required',
            'testdescription' => 'required',
            'classname' => 'required',
            'subject' => 'required',
            'topic' => 'required',
            // 'maxattempt' => 'required',
            'duration' => 'required',
            // 'tstartdate' => 'required',
            // 'testenddate' => 'required',
            'questiondata' => 'required',
        ]);

        // Handle new JSON format question data
        $questionData = $request->questiondata;
        if (is_string($questionData)) {
            $questionData = json_decode($questionData, true);
        }
        if (empty($questionData) || !is_array($questionData)) {
            return back()->with('fail', 'Please select at least one question for your quiz.');
        }

        // Validate that all questions are of the correct type
        $testType = $request->test_type;
        $invalidQuestions = questionbank::whereIn('id', $questionData)
            ->where('type', '!=', $testType)
            ->where('is_active', 1)
            ->get();

        if ($invalidQuestions->count() > 0) {
            return back()->with('fail', 'All questions must be of the same type as the test (' .
                ($testType == 1 ? 'Objective' : 'Subjective') . '). Please check your question selection.');
        }

        if ($request->id) {
            $data = OnlineTests::find($request->id);
            $msg = 'Test updated successfully';
        } else {
            $data = new OnlineTests();
            $msg = 'Test added successfully';
        }
        $tutorid = session('userid')->id;
        // dd($tutorid);
        $data->name = $request->testname;
        $data->test_type = $request->test_type;
        $data->description = $request->testdescription;
        $data->tutor_id = $tutorid;
        $data->class_id = $request->classname;
        $data->subject_id = $request->subject;
        $data->topic_name = $request->topic;
        $data->max_attempt = 1;
        $data->test_duration = $request->duration;
        $data->test_start_date = Carbon::now();
        $data->test_end_date = Carbon::now();
        $data->question_id = json_encode($questionData);
        $res = $data->save();

        $notificationdata = new Notification();
        $notificationdata->alert_type = 6;
        $notificationdata->notification = session('userid')->name . ' New Test Added';
        $notificationdata->initiator_id = session('userid')->id;
        $notificationdata->initiator_role = session('userid')->role_id;
        $notificationdata->event_id = $request->tutorenrollid;

        if ($res) {
            return back()->with('success', $msg);
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }
    public function tutorfetchquestions(Request $request)
    {
        // echo $request->topic_id;

        $questions = questionbank::select('*')
            ->where('subject_id', $request->subject_id)
            ->where('type', $request->type)
            ->where('is_active', 1)
            ->get();

        return $questions;
    }
    public function tutorviewquestions($id)
    {
        // Fetch question details based on testid -> Using jQuerry
        $data['questions'] = OnlineTests::where("id", $id)->first();
        return response()->json($data);
    }
    public function tutoredit($id)
    {
        $tdata = OnlineTests::select('*')->where('id', $id)->first();
        $classes = (new CommonController)->classes();
        $subjects = subjects::select('*')->where('class_id', $tdata->class_id)->where('is_active', 1)->get();
        $topics = topics::select('*')->where('subject_id', $tdata->subject_id)->where('is_active', 1)->get();
        $questions = questionbank::select('*')->where('subject_id', $tdata->subject_id)->where('type', $tdata->test_type)->where('is_active', 1)->get();
        $questiondatas = OnlineTests::select('question_id')->where('id', $tdata->id)->first();

        // $questiondata = explode(',', $tdata->question_id);
        // $data = ModelName::find($id);
        $qstn = explode('"', $tdata->question_id);
        // return view('package.edit', ['data' => $data,'months' => $SelectedMonths]);
        // foreach($prodmulti as $test)
        // echo $months ;
        // echo "<pre>";
        // dd($months);
        // endforeach
        // $keywords = preg_split('/[\s,-,"]+/', $tdata->question_id);
        // dd($months);
        return view('tutor.tutor-onlinetestnew', compact(['tdata', 'classes', 'subjects', 'topics', 'questions', 'questiondatas', 'qstn']));
    }

    public function tutorstatus(Request $request)
    {
        $data = OnlineTests::find($request->id);
        if ($request->status == 1) {
            $status = 0;
        }
        if ($request->status == 0) {
            $status = 1;
        }
        $data->is_active = $status;

        $res = $data->save();
        return json_encode(array('statusCode' => 200));
    }
    public function assignteststatus(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'id' => 'required|exists:assign_tests,id',
            'status' => 'required|in:0,1',
        ]);

        // Find the record by test_id
        $data = AssignTest::where('id', $request->id)->first();

        // Check if the record exists
        if (!$data) {
            return json_encode(array('statusCode' => 404, 'error' => 'Record not found'));
        }

        // Update the status
        $tstatus = ($request->status == 1) ? 0 : 1;
        $data->status = $tstatus;

        // Save the changes
        $res = $data->save();

        return json_encode(array('statusCode' => 200));
    }


    public function tutoronlinetestSearch(Request $request)
    {
        // return $request->all();
        $query = OnlineTests::select('*', 'online_tests.id as test_id', 'online_tests.name as test_name', 'online_tests.description as test_description', 'online_tests.is_active as test_status', 'classes.name as class_name', 'subjects.name as subject_name', 'online_tests.topic_name as topic_name')
            ->join('classes', 'classes.id', 'online_tests.class_id')
            ->join('subjects', 'subjects.id', 'online_tests.subject_id');
        // ->get();
        if ($request->test_name) {
            $query->where('online_tests.name', 'like', '%' . $request->test_name . '%');
        }
        if ($request->class_name) {
            $query->where('online_tests.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('online_tests.subject_id', $request->subject_name);
        }
        if ($request->topic_name) {
            $query->where('online_tests.topic_name', $request->topic_name);
        }
        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(online_tests.test_start_date)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(online_tests.test_end_date)'), '<=', $request->end_date);
        }
        if ($request->status_field) {
            if ($request->status_field == '2') {
                $request->status_field = '0';
            }
            $query->where('online_tests.is_active', $request->status_field);
        }
        $testlists = $query->paginate(10);
        $type = 'tutor-testlists';
        $viewTable = view('admin.partials.students-tutor-search', compact('testlists', 'type'))->render();
        $viewPagination = $testlists->links()->render();
        $classes = classes::where('is_active', 1)->get();
        $subjects = subjects::where('is_active', 1)->get();
        $topics = topics::where('is_active', 1)->get();

        return view('tutor.tutor-onlinetestlist', get_defined_vars());
    }

    public function onlinetestresponsestudent($id)
    {
        return view('admin.onlinetestresponsesstudent');
    }

    // operations with subjective data
    public function  storeSubjectiveDataInTemporaryTable(Request $request)
    {
        // dd($request->all());
        $testId = $request->input('testId');
        $questionId = $request->input('questionId');
        $answer = $request->input('answer'); // Assuming 'answer' is the name of the input field
        $data = TemporarySubjective::where('std_id', session('userid')->id)->where('test_id', $testId)->where('question_id', $questionId)->first();
        if ($data) {
            $answerSave = $data;
        } else {
            $answerSave = new TemporarySubjective;
        }
        $answerSave->std_id = session('userid')->id;
        $answerSave->test_id = $testId;
        $answerSave->question_id = $questionId;
        $answerSave->answer = $answer;
        $answerSave->save();
        if ($request->nextQuestionId != null) {
            $nextanswer = TemporarySubjective::where('std_id', session('userid')->id)->where('test_id', $testId)->where('question_id', $request->nextQuestionId)->value('answer');
        } else {
            $nextanswer = null;
        }
        return response()->json(['message' => 'Data stored successfully', 'nextAnswer' => $nextanswer]);
    }

    public function  getAnswerFromSubjectiveTempTable(Request $request)
    {

        $testId = $request->input('testId');
        $questionId = $request->input('questionId');
        $answer = TemporarySubjective::where('std_id', session('userid')->id)->where('test_id', $testId)->where('question_id', $questionId)->value('answer');
        return response()->json(['answer' => $answer]);
    }
    function tutortestreport($id)
    {
        $assigntdata = AssignTest::find($id);
        $testid = testattempted::where('test_id', $assigntdata->test_id)->first();
        $onlineTest = OnlineTests::where('id', $testid->test_id)->first();

        if ($onlineTest->test_type == 1) {
            $questionIds = json_decode($onlineTest->question_id);
            $responseIds = json_decode($testid->response_id);
            $questionsCount = Questionbank::whereIn('id', $questionIds)->count();
            $questions = Questionbank::whereIn('id', $questionIds)->get();

            $mergedData = [];

            foreach ($questions as $question) {
                $questionData = [
                    'question' => $question->question,
                    'option1' => $question->option1,
                    'option2' => $question->option2,
                    'option3' => $question->option3,
                    'option4' => $question->option4,
                    'correct_answer' => $this->getOptionNumber($question, $question->correct_option),
                ];
                $response = $responseIds > 0 ? testresponssheet::whereIn('id', $responseIds)->where('question_id', $question->id)->first() : null;

                if ($response) {
                    $questionData['marked_answer'] = intval($response->marked_option);
                } else {
                    $questionData['marked_answer'] = '';
                }


                $mergedData[] = $questionData;
            }


            if ($responseIds > 0) {
                $responsesCount = testresponssheet::whereIn('id', $responseIds)->count();
                $responsesCn = testresponssheet::whereIn('id', $responseIds)->get();
                $correctResponsesCount = testresponssheet::whereIn('id', $responseIds)->whereColumn('correct_option', 'marked_option')->count();
            } else {
                $responsesCount = 0;
                $correctResponsesCount = 0;
            }

            return view('tutor.testreport', compact('onlineTest', 'questionsCount', 'responsesCount', 'correctResponsesCount', 'mergedData'));
        } else {

            $response = testattempted::where('test_id', $assigntdata->test_id)->first();

            if ($response) {

                $firstResponse = $response->first();

                $responseIds = json_decode($firstResponse->response_id);

                $finalResponses = SubjectiveResponse::select('subjective_responses.*', 'questionbanks.question')
                    ->join('questionbanks', 'questionbanks.id', 'subjective_responses.question_id')
                    ->whereIn('subjective_responses.id', $responseIds)
                    ->get();

                $uncheckedResponses = $finalResponses->first(function ($response) {
                    return $response->checked == 0;
                });

                if ($uncheckedResponses) {
                    return back()->with('fail', 'Test not yet checked. Please wait or contact tutor');
                } else {
                    $test = OnlineTests::find($firstResponse->test_id);

                    $questionIds = json_decode($test->question_id);
                    $questions = questionbank::whereIn('id', $questionIds)->get();
                    $student = studentregistration::find($assigntdata->student_id);

                    return view('tutor.onlinetestresponsesreport-tutor', get_defined_vars());
                }
                // iuiuiuuii //



            }
        }
    }

    // Helper function to get the option number from the correct option value
    private function getOptionNumber($question, $correctOption)
    {
        foreach (range(1, 4) as $optionNumber) {
            $optionField = "option{$optionNumber}";
            $optionValue = $question->$optionField;

            if ($optionValue === $correctOption) {
                return $optionNumber;
            }
        }

        return 0; // Return 0 if the correct option is not found (shouldn't happen)
    }

    // New API endpoints for improved UX

    /**
     * Get questions for visual selector (with pagination and search)
     */
    public function getQuestionsForSelector(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $type = $request->get('type');
        $search = $request->get('search');
        $typeFilter = $request->get('type_filter');
        $page = $request->get('page', 1);
        $perPage = 12;

        if (!$subjectId || !$type) {
            return response()->json([
                'questions' => [],
                'pagination' => null
            ]);
        }

        $query = questionbank::where('subject_id', $subjectId)
            ->where('type', $type)
            ->where('is_active', 1);

        // Apply type filter if specified
        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', '%' . $search . '%')
                    ->orWhere('topic_name', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }

        $questions = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'questions' => $questions->items(),
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
                'total' => $questions->total(),
                'links' => (string) $questions->links()
            ]
        ]);
    }

    /**
     * Get question details by IDs
     */
    public function getQuestionDetails(Request $request)
    {
        $questionIds = $request->get('question_ids', []);

        if (empty($questionIds) || !is_array($questionIds)) {
            return response()->json(['questions' => []]);
        }

        $questions = questionbank::whereIn('id', $questionIds)
            ->where('is_active', 1)
            ->get();

        return response()->json(['questions' => $questions]);
    }

    /**
     * Quick create question from quiz form
     */
    public function quickCreateQuestion(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'class_id' => 'required',
            'topic' => 'required',
            'question' => 'required',
            'type' => 'required|in:1,2',
        ]);

        $questionType = (int)$request->type;

        if ($questionType == 1) {
            $request->validate([
                'optiona' => 'required',
                'optionb' => 'required',
                'optionc' => 'required',
                'optiond' => 'required',
                'correctanswer' => 'required|in:A,B,C,D',
            ]);
        }

        $data = new questionbank();
        $data->class_id = $request->class_id;
        $data->subject_id = $request->subject_id;
        $data->topic_name = $request->topic;
        $data->question = $request->question;
        $data->type = $questionType;
        $data->remarks = $request->remarks ?? '';

        if ($questionType == 1) {
            $data->option1 = $request->optiona;
            $data->option2 = $request->optionb;
            $data->option3 = $request->optionc;
            $data->option4 = $request->optiond;

            // Set correct option
            if ($request->correctanswer == 'A') {
                $data->correct_option = $request->optiona;
            } elseif ($request->correctanswer == 'B') {
                $data->correct_option = $request->optionb;
            } elseif ($request->correctanswer == 'C') {
                $data->correct_option = $request->optionc;
            } elseif ($request->correctanswer == 'D') {
                $data->correct_option = $request->optiond;
            }
        }
        $data->tutor_id = session('userid')->id;
        $res = $data->save();

        if ($res) {
            // Question is saved to question bank - tutors can see it in question bank
            return response()->json([
                'success' => true,
                'message' => 'Question created and saved to question bank successfully!',
                'question' => [
                    'id' => $data->id,
                    'question' => $data->question,
                    'type' => $data->type,
                    'option1' => $data->option1 ?? null,
                    'option2' => $data->option2 ?? null,
                    'option3' => $data->option3 ?? null,
                    'option4' => $data->option4 ?? null,
                    'correct_option' => $data->correct_option ?? null,
                    'topic_name' => $data->topic_name,
                    'subject_id' => $data->subject_id,
                    'class_id' => $data->class_id
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create question'
            ], 500);
        }
    }
}
