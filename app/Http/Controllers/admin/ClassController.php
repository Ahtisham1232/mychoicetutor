<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\batches;
use App\Models\classes;
use App\Models\tutorreviews;
use App\Models\status;
use App\Models\SlotBooking;
use App\Models\students\studentattendance;
use App\Models\subjects;
use App\Models\zoom_classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;

class ClassController extends Controller
{
    public function index()
    {
        $classes = classes::select('*')->paginate(10);

        return view('admin.class', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classname' => 'required',
        ]);
        if ($request->id) {
            $data = classes::find($request->id);
            $msg = 'Class updated successfully';
        } else {
            $data = new classes();
            $msg = 'Class added successfully';
        }
        $data->name = $request->classname;
        $res = $data->save();

        if ($res) {
            return back()->with('success', $msg);
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }
    public function status(Request $request)
    {
        // echo 'Test';
        // echo $request->id;
        // echo $request->status;
        // dd();
        $data = classes::find($request->id);
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

    public function tutorclasses()
{
    $tutorId = session('userid')->id; // Get the current tutor's ID
    
    $liveclasses = zoom_classes::select(
            'zoom_classes.*',
            'classes.id as class_id',
            'subjects.id as subject_id',
            'subjects.name as subjects',
            'studentregistrations.name as student',
            'classes.name as class',
            'slot_bookings.date as slotdate',
            'slot_bookings.slot as slottime',
            // 'tutorreviews.id as review_id', // to check if a review exists
            // 'tutorreviews.ratings as tutor_rating',
            // 'tutorreviews.name as tutor_review'
        )
        ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
        ->join('studentregistrations', 'studentregistrations.id', 'slot_bookings.student_id')
        ->join('subjects', 'subjects.id', 'slot_bookings.subject_id')
        ->join('classes', 'subjects.class_id', 'classes.id')
        // ->leftJoin('tutorreviews', function($join) {
        //     $join->on('tutorreviews.subject_id', '=', 'subjects.id')
        //          ->on('tutorreviews.tutor_id', '=', 'zoom_classes.tutor_id')
        //          ->on('tutorreviews.student_id', '=', 'studentregistrations.id');
        // })
        ->where('zoom_classes.is_completed', 1)
        ->where('zoom_classes.is_active', 1)
        ->where('zoom_classes.tutor_id', $tutorId) // Ensure only this tutor's recordings
        ->whereNotNull('zoom_classes.recording_link') // Only show classes with recordings
        ->where('zoom_classes.recording_link', '!=', '') // Exclude empty recording links
        ->get();

    $classes = (new CommonController)->classes();

    return view('tutor.classes', compact('liveclasses', 'classes'));
}


    public function studentclass()
    {

        $targetValue = session('userid')->id; // The value we want to check in the JSON array    
        $classes = SlotBooking::select(
            'zoom_classes.*',
            'zoom_classes.id as class_id',
            'tutorregistrations.name as tutor_name',
            'zoom_classes.topic_name as topics',
            'zoom_classes.tutor_id as tutor_id',
            'subjects.id as subject_id',
            'subjects.name as subjects',
            'slot_bookings.date as slotdate',
            'slot_bookings.slot as slottime'
        )
        ->leftJoin('zoom_classes', 'zoom_classes.id', 'slot_bookings.meeting_id')
        ->leftJoin('subjects', 'subjects.id', 'slot_bookings.subject_id')
        ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'slot_bookings.tutor_id')
        ->where('slot_bookings.student_id', session('userid')->id)
        ->where(function($query) {
            $query->where('zoom_classes.is_completed', '!=', 1)
                  ->orWhereNull('zoom_classes.is_completed');
        }) // Exclude completed classes only if they exist
        ->orderBy('zoom_classes.id','desc')->get();

        // Convert slot dates and times from UTC to viewer's timezone for display
        $classes->each(function ($class) {
            if (isset($class->slotdate) && $class->slotdate) {
                $class->slotdate = TimezoneHelper::dateToUserTz($class->slotdate);
            }
            if (isset($class->slottime) && $class->slottime) {
                $class->slottime = TimezoneHelper::timeToUserTz($class->slottime);
            }
        });

            // dd($classes);
        $subjects = subjects::where('is_active', 1)->get();
        $batches = batches::where('is_active', 1)->get();

        return view('student.classes', get_defined_vars());

    }
    public function studentclassParent()
    {

        $targetValue = session('userid')->id; // The value we want to check in the JSON array

        $classes = zoom_classes::select('*', 'zoom_classes.id as class_id', 'zoom_classes.tutor_id as tutor_id', 'subjects.id as subject_id', 'subjects.name as subjects', 'batches.name as batch', 'topics.name as topics')
            ->join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('batches', 'batches.id', 'zoom_classes.batch_id')
            ->join('subjects', 'subjects.id', 'batches.subject_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
        // ->where('zoom_classes.status','like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->paginate(10);
        $subjects = subjects::where('is_active', 1)->where('class_id', session('userid')->class_id)->get();
        $batches = batches::where('is_active', 1)->get();

        return view('parent.classes', get_defined_vars());

    }
    public function studentCompletedclass()
{
    $targetValue = session('userid')->id; // Get the current student's ID

    $classes = zoom_classes::select(
            'zoom_classes.*',
            'zoom_classes.id as class_id',
            'zoom_classes.topic_name as topics',
            'subjects.id as subject_id',
            'subjects.name as subject_name',
            'zoom_classes.tutor_id',
            'tutorregistrations.name as tutor_name',
            'tutorreviews.ratings as tutor_review', // Adding tutor review
            'tutorreviews.name as tutor_review_text' // Adding tutor review
        )
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
        ->join('slot_bookings', 'slot_bookings.meeting_id', '=', 'zoom_classes.id')
        ->join('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
        ->leftJoin('tutorreviews', function($join) use ($targetValue) {
            $join->on('tutorreviews.tutor_id', '=', 'zoom_classes.tutor_id')
                 ->on('tutorreviews.subject_id', '=', 'subjects.id')
                 ->where('tutorreviews.student_id', '=', $targetValue); // Match tutor_id, subject_id, and student_id
        })
        ->where('zoom_classes.is_active', 1)
        ->where('zoom_classes.student_id', $targetValue) // Ensure only this student's recordings
        ->where('slot_bookings.student_id', $targetValue) // Double-check with slot_bookings
        ->where('zoom_classes.is_completed', 1)
        ->whereNotNull('zoom_classes.recording_link') // Only show classes with recordings
        ->where('zoom_classes.recording_link', '!=', '') // Exclude empty recording links
        ->paginate(10000);

    // Fetch active subjects and batches
    $subjects = subjects::where('is_active', 1)->get();
    $batches = batches::where('is_active', 1)->get();

    return view('student.completed-classes', get_defined_vars());
}


    public function studentCompletedclasssearch(Request $request)
    {
        $targetValue = session('userid')->id; // The current student's ID

        // Start building the query
        $query = zoom_classes::select(
            'zoom_classes.id as class_id',
            'zoom_classes.topic_name as topic_name',
            'subjects.name as subject_name',
            'zoom_classes.tutor_id as tutor_id',
            'tutorregistrations.name as tutor_name',
            'slot_bookings.date as class_date',
            'zoom_classes.started_at as started_at',
            'zoom_classes.completed_at as completed_at',
            'zoom_classes.is_completed as is_completed',
            'zoom_classes.recording_link as recording_link',
        )
        ->join('tutorregistrations', 'tutorregistrations.id', 'zoom_classes.tutor_id')
        ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
        ->join('subjects', 'subjects.id', 'slot_bookings.subject_id')
        ->where('zoom_classes.is_active', 1)
        ->where('zoom_classes.is_completed', 1)
        ->where('zoom_classes.student_id', $targetValue) // Filter by student_id in zoom_classes table
        ->where('slot_bookings.student_id', $targetValue) // Also filter by student_id in slot_bookings for extra security
        ->whereNotNull('zoom_classes.recording_link') // Only show classes with recordings
        ->where('zoom_classes.recording_link', '!=', ''); // Exclude empty recording links

        // Apply subject filter if present
        if ($request->subject_name) {
            $query->where('subjects.id', $request->subject_name);
        }

        // Apply date range filters
        if ($request->start_date && !$request->end_date) {
            // Only start date provided: show all classes from that start date onward
            $query->whereDate('slot_bookings.date', '>=', $request->start_date);
        } elseif (!$request->start_date && $request->end_date) {
            // Only end date provided: show all classes up to that end date + 1 day
            $endDate = Carbon::parse($request->end_date)->addDay(); // Add one day to the end date
            $query->whereDate('slot_bookings.date', '<=', $endDate);
        } elseif ($request->start_date && $request->end_date) {
            // Both start and end dates provided: show classes between those dates + 1 day for end date
            $endDate = Carbon::parse($request->end_date)->addDay(); // Add one day to the end date
            $query->whereDate('slot_bookings.date', '>=', $request->start_date)
                  ->whereDate('slot_bookings.date', '<=', $endDate);
        }

        // Paginate the results
        $classes = $query->paginate(10000);

        // Fetch subjects and batches
        $subjects = subjects::where('is_active', 1)->get();
        $batches = batches::where('is_active', 1)->get();

        // Return the view
        return view('student.completed-classes', get_defined_vars());
    }


    public function studentCompletedclassParent()
    {

        $targetValue = session('userid')->id; // The value we want to check in the JSON array

        $classes = zoom_classes::select('*', 'zoom_classes.id as class_id', 'zoom_classes.tutor_id as tutor_id', 'subjects.id as subject_id', 'subjects.name as subjects', 'batches.name as batch', 'topics.name as topics')
            ->join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('batches', 'batches.id', 'zoom_classes.batch_id')
            ->join('subjects', 'subjects.id', 'batches.subject_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
            ->where('zoom_classes.status', 'like', '%completed%')
            ->where('zoom_classes.is_completed', 1)
            ->paginate(10);
        $subjects = subjects::where('is_active', 1)->where('class_id', session('userid')->class_id)->get();
        $batches = batches::where('is_active', 1)->get();

        return view('parent.completed-classes', get_defined_vars());

    }

    // search functionality
    public function studentclassSearch(Request $request)
    {
        $targetValue = session('userid')->id; // The current student's ID

        // Build the base query for zoom classes
        $query = zoom_classes::select(
            'zoom_classes.id as class_id',
            'zoom_classes.tutor_id as tutor_id',
            'tutorregistrations.name as tutor_name',
            'subjects.id as subject_id',
            'subjects.name as subjects',
            'zoom_classes.topic_name as topics',
            'zoom_classes.status as status',
            'slot_bookings.date as slotdate',
            'slot_bookings.slot as slottime'
        )
            ->join('slot_bookings', 'slot_bookings.meeting_id', 'zoom_classes.id')
            ->join('subjects', 'subjects.id', 'slot_bookings.subject_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'zoom_classes.tutor_id')
            ->where('zoom_classes.is_active', 1)
        // Ensure that the student's ID matches
            ->where('slot_bookings.student_id', $targetValue);

        // Apply subject filter if present
        if ($request->subject_name) {
            $query->where('subjects.id', $request->subject_name);
        }

        // Apply batch filter if present
        if ($request->batch) {
            $query->where('zoom_classes.batch_id', $request->batch);
        }

        // Handle start date, end date, and both date filters using slot_bookings.date
        if ($request->start_date && !$request->end_date) {
            // Only start date provided: show all classes from that start date onward
            $query->whereDate('slot_bookings.date', '>=', $request->start_date);
        } elseif (!$request->start_date && $request->end_date) {
            // Only end date provided: show all classes up to that end date
            $query->whereDate('slot_bookings.date', '<=', $request->end_date);
        } elseif ($request->start_date && $request->end_date) {
            // Both start and end date provided: show all classes between these dates
            $query->whereDate('slot_bookings.date', '>=', $request->start_date)
                ->whereDate('slot_bookings.date', '<=', $request->end_date);
        }

        // Apply status filter if present
        if ($request->status) {
            $query->where('zoom_classes.status', 'like', '%' . $request->status . '%');
        }

        // Paginate the results
        $classes = $query->paginate();

        // View variables
        $type = "student-classes";
        $viewTable = view('admin.partials.common-search', compact('classes', 'type'))->render();
        $subjects = subjects::where('is_active', 1)->get();
        $batches = batches::where('is_active', 1)->get();

        // Return the view
        return view('student.classes', get_defined_vars());
    }

    public function student_attendance_report()
    {

        $attend = studentattendance::select('studentattendances.*', 'tutorregistrations.name as tutor_name', 'subjects.name as subject_name')
        // ->join('classes','classes.id','studentattendances.class_id')
        // ->join('subjects','subjects.id','studentattendances.subject_id')
        // ->join('tutorprofiles','tutorprofiles.tutor_id','studentattendances.tutor_id')
            ->join('tutorregistrations', 'tutorregistrations.id', 'studentattendances.tutor_id')
            ->join('subjects', 'subjects.id', 'studentattendances.subject_id')
            ->where('studentattendances.student_id', session('userid')->id)

            ->get();
        // dd($attend);

        return view('student.attendance-report', compact('attend'));
    }
    public function student_class_report()
    {
        $targetValue = session('userid')->id; // The value we want to check in the JSON array

        $classes = zoom_classes::select('*', 'zoom_classes.id as class_id', 'zoom_classes.tutor_id as tutor_id', 'subjects.id as subject_id', 'subjects.name as subjects', 'batches.name as batch', 'topics.name as topics', 'tutorprofiles.name as tutor')
            ->join('batchstudentmappings', 'batchstudentmappings.batch_id', 'zoom_classes.batch_id')
            ->join('batches', 'batches.id', 'zoom_classes.batch_id')
            ->join('subjects', 'subjects.id', 'batches.subject_id')
            ->join('topics', 'topics.id', 'zoom_classes.topic_id')
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', 'zoom_classes.tutor_id')
            ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
            ->where('zoom_classes.is_active', 1)
        // ->where('zoom_classes.status','like', '%waiting%')
            ->where('zoom_classes.is_completed', 0)
            ->paginate(10);
        $subjects = subjects::where('is_active', 1)->where('class_id', session('userid')->class_id)->get();
        $batches = batches::where('is_active', 1)->get();
        return view('student.class-report', get_defined_vars());
    }
    public function student_class_reportParent()
    {
        return view('parent.class-report');
    }

    public function student_attendance_reportParent()
    {
        return view('parent.attendance-report');
    }

    public function tutorattendance()
    {
        $studentAttendances = studentattendance::select("studentattendances.class_starts_at", "studentattendances.status", 'studentregistrations.name as student_name', 'subjects.name as subject_name', 'classes.name as class_name', 'batches.name as batch_name')
            ->leftjoin('studentregistrations', 'studentregistrations.id', 'studentattendances.student_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'studentattendances.subject_id')
            ->leftjoin('batches', 'batches.id', '=', 'studentattendances.batch_id')
            ->leftjoin('classes', 'classes.id', '=', 'studentattendances.class_id')->where('studentattendances.tutor_id', session('userid')->id)->paginate(10);
        return view('tutor.attendance', get_defined_vars());
    }

    public function tutorattendanceSearch(Request $request)
    {
        // dd($request->all());
        $query = studentattendance::select("studentattendances.class_starts_at", "studentattendances.status", 'studentregistrations.name as student_name', 'subjects.name as subject_name', 'classes.name as class_name', 'batches.name as batch_name')
            ->leftjoin('studentregistrations', 'studentregistrations.id', 'studentattendances.student_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'studentattendances.subject_id')
            ->leftjoin('batches', 'batches.id', '=', 'studentattendances.batch_id')
            ->leftjoin('classes', 'classes.id', '=', 'studentattendances.class_id')
            ->where('studentattendances.tutor_id', session('userid')->id);
        // ->paginate(10);
        if ($request->student_name) {
            $query->where('studentregistrations.name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->start_date) {
            $query->whereDate(DB::raw('DATE(studentattendances.class_starts_at)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(studentattendances.class_starts_at)'), '<=', $request->end_date);
        }
        if ($request->status) {
            if ($request->status == 2) {
                $status = 0;
            } elseif ($request->status == 1) {
                $status = 1;
            }
            $query->where('studentattendances.status', $status);
        }
        $studentAttendances = $query->paginate(10);
        $type = "tutor-attendances";
        $viewTable = view('admin.partials.common-search', compact('studentAttendances', 'type'))->render();
        $viewPagination = $studentAttendances->links()->render();
        return view('tutor.attendance', get_defined_vars());

    }
    // Scheduled Classes in Admin Panel
    public function scheduledclasses()
    {
        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        $scheduledclasses = zoom_classes::select('zoom_classes.id as class_id','zoom_classes.started_at', 'zoom_classes.completed_at', 'zoom_classes.meeting_id as meeting_id', 'zoom_classes.topic_name as topic_name', 'zoom_classes.status as meeting_status', 'zoom_classes.start_time as meeting_start_time', 'zoom_classes.is_completed as is_completed', 'zoom_classes.recording_link as recording_link', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name', 'tutorregistrations.mobile as tutor_mobile', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile', 'topics.name as topic_name', 'subjects.name as subject_name', 'classes.name as class_name')
            ->leftjoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftjoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftjoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->leftjoin('classes', 'classes.id', '=', 'subjects.class_id')
            ->orderby('zoom_classes.created_at', 'desc')
            ->paginate(1000000);

        // dd($scheduledclasses);
        return view('admin.scheduledclasses', get_defined_vars());
    }
    // Scheduled Class Search
    public function scheduledsearch(Request $request)
    {

        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        $query = zoom_classes::select('zoom_classes.id as class_id', 'zoom_classes.meeting_id as meeting_id', 'zoom_classes.topic_name as topic_name', 'zoom_classes.status as meeting_status', 'zoom_classes.start_time as meeting_start_time', 'zoom_classes.is_completed as is_completed', 'zoom_classes.recording_link as recording_link', 'tutorregistrations.id as tutor_id', 'tutorregistrations.name as tutor_name', 'tutorregistrations.mobile as tutor_mobile', 'studentregistrations.id as student_id', 'studentregistrations.name as student_name', 'studentregistrations.mobile as student_mobile', 'topics.name as topic_name', 'subjects.name as subject_name', 'classes.name as class_name')
            ->leftjoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftjoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftjoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->leftjoin('classes', 'classes.id', '=', 'subjects.class_id');
        // ->where('democlasses.student_id','=', session('userid')->id)
        // ->get();

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
            $query->whereDate(DB::raw('DATE(zoom_classes.created_at)'), '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate(DB::raw('DATE(zoom_classes.created_at)'), '<=', $request->end_date);
        }
        // if ($request->status) {
        //     $query->where('zoom_classes.status',$request->status );
        // }
        $scheduledclasses = $query->get();
        return view('admin.scheduledclasses', get_defined_vars());
    }
    // Completed Classes in Admin Panel
    public function completedclasses()
    {
        $subjects = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $statuses = status::select('*')->get();
        return view('admin.completedclasses', get_defined_vars());
    }
}
