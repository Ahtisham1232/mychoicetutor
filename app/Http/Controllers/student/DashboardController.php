<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\questionbank;
use App\Models\studentprofile;
use App\Models\testattempted;
use App\Models\testresponssheet;
use App\Models\Notification;
use DB;
use App\Models\classes;
use Illuminate\Http\Request;
use App\Models\tutorprofile;
use App\Models\democlasses;
use App\Models\subjects;
use App\Models\zoom_classes;
use App\Models\OnlineTests;
use App\Helpers\TimezoneHelper;
use Illuminate\Support\Carbon;
use App\Models\payments\paymentdetails;
use App\Models\StudentAssignmentList;
use App\Models\StudentAssignments;

class DashboardController extends Controller
{
    public function index()
    {

        $targetValue = session('userid')->id;
        $studentpro = studentprofile::select('*')->where('student_id', '=', session('userid')->id)->first();
        $subjects_enrolled = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)
            ->groupBy('paymentstudents.subject_id')
            ->selectRaw('paymentstudents.subject_id, COUNT(*) as subject_count')
            ->get();
        $atendedclasses = zoom_classes::
        // join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            // ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            // ->where('zoom_classes.is_active', 1)
            // ->where('zoom_classes.status', 'like', '%completed%')
            where('is_completed', 1)
            ->where('student_id', session('userid')->id)
            ->count();
        // dd($atendedclasses);
        $non_atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->count();
        $classes_purchased = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)->sum('classes_purchased');
        $upcomingClasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            // ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'desc') // Order by start_time in ascending order
            ->take(5) // Limit to the latest 5
            ->get();
            
        // ============================
        // $upclasses = zoom_classes::select('zoom_classes.*','zoom_classes.id as class_id','slot_bookings.date as slot_date','slot_bookings.slot as slot_time','zoom_classes.topic_name as topics','zoom_classes.tutor_id as tutor_id','tutorregistrations.name as tutor_name','subjects.id as subject_id','subjects.name as subjects',)
        // ->join('slot_bookings','slot_bookings.meeting_id','zoom_classes.id')
        // // ->join('batchstudentmappings','batchstudentmappings.batch_id','zoom_classes.batch_id')
        // // ->join('batches','batches.id','zoom_classes.batch_id')
        // ->join('subjects','subjects.id','slot_bookings.subject_id')
        // ->join('tutorregistrations','tutorregistrations.id','zoom_classes.tutor_id')
        // // ->join('topics','topics.id','zoom_classes.topic_id')
        // // ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
        // ->where('zoom_classes.is_active',1)
        // ->where('slot_bookings.student_id',session('userid')->id)
        // // ->where('zoom_classes.status','like', '%waiting%')
        // ->where('zoom_classes.is_completed',0)
        // ->get();

        $upclasses = zoom_classes::select('zoom_classes.*', 'zoom_classes.id as liveclass_id', 'studentregistrations.name as studentname','tutorregistrations.name as tutor_name', 'subjects.name as subjects', 'classes.name as classname', 'slot_bookings.date as slotdate', 'slot_bookings.slot as slottime')
            ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
            ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'slot_bookings.tutor_id')
            ->join('paymentstudents', 'paymentstudents.id', 'slot_bookings.class_schedule_id')
            ->join('subjects', 'subjects.id', 'paymentstudents.subject_id')
            ->join('classes', 'classes.id', 'paymentstudents.class_id')
            ->where('zoom_classes.is_completed', 0)
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.student_id', session('userid')->id)
            // ->where('zoom_classes.start_time','>=',Carbon::now())
            ->orderby('slot_bookings.date', 'desc')
            ->get();

        $upclasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time);
            $class->slotdate = TimezoneHelper::dateToUserTz($class->slotdate);
            $class->slottime = TimezoneHelper::timeToUserTz($class->slottime);
            return $class;
        });

        // dd($upclasses);

        $demos = democlasses::select('*', 'democlasses.id as demo_id', 'classes.name as class_name', 'tutorregistrations.name as tutor', 'subjects.name as subject', 'subjects.id as subjectid', 'statuses.name as currentstatus', 'tutorprofiles.name as tutor_name')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'democlasses.tutor_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'democlasses.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'democlasses.subject_id')
            ->join('statuses', 'statuses.id', '=', 'democlasses.status')
            ->join('classes', 'classes.id', '=', 'subjects.class_id')
            ->where('democlasses.student_id', '=', session('userid')->id)
            ->where(function ($query) {
                $query->where('democlasses.slot_confirmed', '>', Carbon::now())
                    ->orWhere('statuses.name', '=', 'started');
            })
            ->orderBy('democlasses.slot_confirmed', 'desc')
            ->take(5)
            ->get()
            ->each(function ($item) {
                $item->slot_confirmed_local = TimezoneHelper::toUserTz($item->slot_confirmed, 'UTC');
            });


        $upcomingClasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time); // Convert start_time to Carbon
            return $class;
        });
        // dd($demos);
        // Upcoming Quiz/Tests
        $upcomingQuizes = OnlineTests::select('online_tests.*', 'subjects.name as subject_name', 'subjects.image as subject_image', 'online_tests.topic_name as topic_name')
            ->where('online_tests.class_id', session('userid')->class_id)
            // ->where('online_tests.test_start_date', '>', Carbon::now())
            ->join('subjects', 'subjects.id', 'online_tests.subject_id')
            ->orderBy('online_tests.test_start_date', 'asc')
            ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });

        $pastQuizes = testattempted::select('testattempteds.*', 'online_tests.name as exam_name', 'online_tests.description as exam_description', 'online_tests.test_duration as duration', 'online_tests.test_start_date as test_start_date', 'online_tests.test_end_date as test_end_date')
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();


        // Past Tests
        $pastQuizes->transform(function ($quiz) {
            $testid = testattempted::find($quiz->id);
            $onlineTest = OnlineTests::where('id', $testid->test_id)
                            // ->where('class_id', session('userid')->class_id)
                            ->first();
         

                if ($onlineTest && $testid) {
                    // Decode the JSON strings to arrays
                    $questionIds = json_decode($onlineTest->question_id, true);
                    $responseIds = json_decode($testid->response_id, true);

                    // Ensure both are valid arrays before proceeding
                    $questionsCount = is_array($questionIds) ? count($questionIds) : 0;
                    $responsesCount = is_array($responseIds) ? count($responseIds) : 0;

                    // Calculate correct responses
                    $correctResponsesCount = is_array($responseIds) ? count(array_filter($responseIds, function ($responseId) {
                        // Ensure proper filtering logic
                        return testresponssheet::where('id', $responseId)
                            ->whereColumn('correct_option', 'marked_option')
                            ->exists();
                    })) : 0;

                    // Proceed with your logic or return the counts
                    return [
                        'questionsCount' => $questionsCount,
                        'responsesCount' => $responsesCount,
                        'correctResponsesCount' => $correctResponsesCount,
                    ];
                } else {
                    return back()->with('fail', 'Test or response data not found.');
                }



            // Assign counts to the quiz object
            $quiz->questionsCount = $questionsCount;
            $quiz->responsesCount = $responsesCount;
            $quiz->correctResponsesCount = $correctResponsesCount;

            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });


        // Test Reports - Merged By Tests
        $pastQuizzes = testattempted::select('testattempteds.*', 'online_tests.subject_id', 'online_tests.name as exam_name', 'online_tests.description as exam_description', 'online_tests.test_duration as duration', 'online_tests.test_start_date as test_start_date', 'online_tests.test_end_date as test_end_date')
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();


        $pastQuizzes = testattempted::select(
            'testattempteds.*',
            'online_tests.subject_id',
            'online_tests.question_id', // Include the question_id from online_tests
            'online_tests.name as exam_name',
            'online_tests.description as exam_description',
            'online_tests.test_duration as duration',
            'online_tests.test_start_date as test_start_date',
            'online_tests.test_end_date as test_end_date'
        )
            ->join('online_tests', 'online_tests.id', 'testattempteds.test_id')
            ->where('testattempteds.student_id', session('userid')->id)
            ->where('testattempteds.is_active', 1)
            ->orderBy('testattempteds.created_at', 'desc')
            ->get();

        $pastQuizzes = $pastQuizzes->groupBy('subject_id')->map(function ($quizzes) {
            // Initialize aggregated counts
            $totalQuestions = 0;
            $totalAttempted = 0;

            // Iterate over quizzes to sum counts
            foreach ($quizzes as $quiz) {
                $questionIds = json_decode($quiz->question_id, true);
                $responseIds = json_decode($quiz->response_id, true);

                $questionsCount = is_array($questionIds) ? count($questionIds) : 0;
                $responsesCount = is_array($responseIds) ? count($responseIds) : 0;

                // Aggregate counts
                $totalQuestions += $questionsCount;
                $totalAttempted += $responsesCount;
            }

            // Pick the first quiz to get subject information
            $firstQuiz = $quizzes->first();
            $subjectName = subjects::find($firstQuiz->subject_id)->name;

            return [
                'subjectName' => $subjectName,
                'totalTests' => $quizzes->count(),
                'totalQuestions' => $totalQuestions,
                'totalAttempted' => $totalAttempted,
            ];
        });

        // Tutors List
        $tutorlists = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            'tutorprofiles.profile_pic',
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", ") as subject'),
            DB::raw('SUM(tutorreviews.ratings) / COUNT(tutorreviews.id) AS starrating'),
            DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
            DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
        )
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id') // Adding join for zoom_classes
            ->where('tutorregistrations.is_active', 1)
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.profile_pic'
            )
            ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
            ->get(10);

        // Subject lists with category
        $subjectlistsdata = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        $formattedSubjects = [];

        foreach ($subjectlists as $subject) {
            $categoryName = $subject->category_name;
            $subjectName = $subject->subject_name;

            if (!isset($formattedSubjects[$categoryName])) {
                $formattedSubjects[$categoryName] = [];
            }

            $formattedSubjects[$categoryName][] = $subjectName;
        }


        // dd($subjectlists);
        // Upcoming Assignments
        $upcomingAssignments = StudentAssignmentList::select('student_assignment_lists.name as assignment_name', 'subjects.name as subject_name', 'topics.name as topic_name', 'student_assignment_lists.assignment_start_date as assignment_start_date', 'student_assignment_lists.assignment_link as assignment_link', 'tutorprofiles.profile_pic as tutor_pic')
            ->where('student_assignment_lists.class_id', session('userid')->class_id)
            ->join('subjects', 'subjects.id', 'student_assignment_lists.subject_id')
            ->join('topics', 'topics.id', 'student_assignment_lists.topic_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'student_assignment_lists.assigned_by')
            // ->where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
            ->take(5)->get();



        $latest_payments = paymentdetails::leftjoin('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->leftjoin('subjects', 'subjects.id', 'paymentstudents.subject_id')->where('paymentstudents.student_id', '=', session('userid')->id)->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        $classes = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->join('subjects', 'subjects.id', 'topics.subject_id')
            // ->where('zoom_classes.is_completed',0)
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->groupBy('subjects.id')
            ->selectRaw('subjects.id, COUNT(*) as subject_count')
            ->get();



        $totalclassesTaken =  zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")->count();

        $subjectData = [];
        foreach ($classes as $demo) {
            $subjectName = subjects::find($demo->id)->name;
            $subjectCount = $demo->subject_count;
            $percentage = ($subjectCount / $totalclassesTaken) * 100;
            $subjectData[] = [
                'subject_id' => $demo->id,
                'subject_name' => $subjectName,
                'percentage' => $percentage,
            ];
        }

        $tutors_enrolled = tutorprofile::select(
            'tutorprofiles.id',
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.profile_pic',
            DB::raw('IFNULL(SUM(tutorreviews.ratings) / COUNT(tutorreviews.ratings), 0) AS starrating'),
            DB::raw('IFNULL(ps.total_classes_purchased, 0) as total_classes_purchased'),
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) + tutorprofiles.rateperhour as rate'),
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name ASC SEPARATOR ", ") as subject')
        )
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join(DB::raw('(SELECT tutor_id, SUM(classes_purchased) as total_classes_purchased
                            FROM paymentstudents
                            WHERE student_id = ' . session('userid')->id . '
                            GROUP BY tutor_id) as ps'), 'ps.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->groupBy(
                'tutorprofiles.id',
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.profile_pic',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'ps.total_classes_purchased'
            )
            ->get();

            // dd($tutors_enrolled);

        return view('student.dashboard', get_defined_vars());
    }

    public function parent_dashboard()
    {

        $targetValue = session('userid')->id;
        $studentpro = studentprofile::select('*')->where('student_id', '=', session('userid')->id)->first();
        $subjects_enrolled = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)
            ->groupBy('paymentstudents.subject_id')
            ->selectRaw('paymentstudents.subject_id, COUNT(*) as subject_count')
            ->get();
        $atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%completed%')
            ->where('zoom_classes.is_completed', 1)
            ->count();
        $non_atendedclasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->count();
        $classes_purchased = paymentdetails::join('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->where('paymentstudents.student_id', session('userid')->id)->sum('classes_purchased');
        $upcomingClasses = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('start_time', '>', Carbon::now())
            ->orderBy('slotdate', 'desc') // Order by start_time in ascending order
            ->take(5) // Limit to the latest 5
            ->get();
        $upcomingClasses->transform(function ($class) {
            $class->start_time = Carbon::parse($class->start_time); // Convert start_time to Carbon
            return $class;
        });

        $upcomingQuizes = OnlineTests::
            // where('class_id',session('userid')->class_id)
            where('test_start_date', '>', Carbon::now())->orderBy('test_start_date', 'asc')
            ->take(5)->get();
        $upcomingQuizes->transform(function ($quiz) {
            $quiz->test_start_date = Carbon::parse($quiz->test_start_date);
            $quiz->test_end_date = Carbon::parse($quiz->test_end_date);
            return $quiz;
        });
        $latest_payments = paymentdetails::leftjoin('paymentstudents', 'paymentstudents.transaction_id', 'paymentdetails.transaction_id')
            ->leftjoin('subjects', 'subjects.id', 'paymentstudents.subject_id')->where('paymentstudents.student_id', '=', session('userid')->id)->orderBy('payment_date', 'desc')->take(5)->get();
        $latest_payments->transform(function ($payment) {
            $payment->payment_date = Carbon::parse($payment->payment_date);
            return $payment;
        });

        $classes = zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->join('subjects', 'subjects.id', 'topics.subject_id')
            // ->where('zoom_classes.is_completed',0)
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->groupBy('subjects.id')
            ->selectRaw('subjects.id, COUNT(*) as subject_count')
            ->get();



        $totalclassesTaken =  zoom_classes::join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")->count();

        $subjectData = [];
        foreach ($classes as $demo) {
            $subjectName = subjects::find($demo->id)->name;
            $subjectCount = $demo->subject_count;
            $percentage = ($subjectCount / $totalclassesTaken) * 100;
            $subjectData[] = [
                'subject_id' => $demo->id,
                'subject_name' => $subjectName,
                'percentage' => $percentage,
            ];
        }

        return view('parent.dashboard', get_defined_vars());
    }

    public function notificationslist(){
        // Get the current date minus 30 days
        $dateLimit = now()->subDays(30);

        // Start building the base query for notifications
        $notifications = Notification::select('notifications.*')
            ->where('notifications.show_to_student', 1)
            ->where('notifications.show_to_student_id', session('userid')->id)
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

        // Paginate and return to the view
        $notifications = $notifications->paginate(20);

        return view('student.notificationslist', compact('notifications'));
    }


}
