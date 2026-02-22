<?php

namespace App\Http\Controllers;

use App\Models\batches;
use App\Models\classes;
use App\Models\status;
use App\Models\studentregistration;
use App\Models\tutorsubjectmapping;
use App\Models\batchstudentmapping;
use App\Models\subjects;
use App\Models\topics;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;

class CommonController extends Controller
{
    // Status List
    public function status()
    {
        return  status::select('*')->get();
    }
    // Class List
    public function classes()
    {
        return classes::select('*')->where('is_active', '1')->get();
    }
    // Fetch Subjects By Class
    public function fetchsubjects(Request $request)
    {
        // Fetch Subjects Based On Class -> Using jQuerry
        $data['subjects'] = subjects::where("class_id", $request->class_id)
            ->where('is_active', 1)->get();
        return response()->json($data);
    }
    public function fetchtutorsubjects(Request $request){
        $data['subjects'] = tutorsubjectmapping::select('subjects.id','subjects.name')
        ->join('subjects','subjects.id','tutorsubjectmappings.subject_id')
        ->where("tutorsubjectmappings.tutor_id", $request->tutor_id)
            ->where('subjects.is_active', 1)->get();
        return response()->json($data);
    }
    // Fetch Topic By Class
    public function fetchtopics(Request $request)
    {
        // Fetch Subjects Based On Class -> Using jQuerry
        $data['topics'] = topics::where("subject_id", $request->subject_id)
            ->where('is_active', 1)->get();
        return response()->json($data);
    }

    public function fetchslottime(Request $request)
    {
        // Fetch Slot Times Based On Date -> Using jQuerry
        // Convert PKT date to UTC for database query
        $utcDate = Carbon::createFromFormat('Y-m-d', $request->date, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
        
        $slots = SlotBooking::where("date", $utcDate)
            ->where('status', 0)->get();
        
        // Convert slot times from UTC to user timezone for display
        $slots->each(function ($slot) {
            $slot->slot = TimezoneHelper::toUserTz($slot->slot, 'UTC')->toTimeString('minute');
        });
        
        $data['times'] = $slots;
        return response()->json($data);
    }

    public function studentsbyclass(Request $request)
    {
        $data['students'] = studentregistration::where('class_id', $request->class_id)
            ->where('is_active', 1)->get();
        return response()->json($data);
    }

    public function batchbysubject(Request $request)
    {
        $data['batches'] = batches::where('subject_id', $request->subject_id)->where('is_active', 1)->get();
        return response()->json($data);
    }

    public function fetchtutors(Request $request)
    {
        // Fetch Subjects Based On Class -> Using jQuerry
        $data['tutors'] = tutorsubjectmapping::where("tutorsubjectmappings.subject_id", $request->subject_id)->select('tutorsubjectmappings.tutor_id','tutorregistrations.name')
                           ->leftjoin('tutorregistrations','tutorregistrations.id','tutorsubjectmappings.tutor_id')->get();
        return response()->json($data);
    }
    public function studentsbybatch(Request $request)
    {
        $targetValue = 1;
        // dd($request->batchId);
        //     $data['students'] = studentregistration::select('*')
        //     ->join('batchstudentmappings', 'studentregistrations.student_id', '=', 'batchstudentmappings.student_id')
        // ->whereIn('batchstudentmappings.student_id', $studentIdsArray)
        // ->join('batchstudentmappings','batchstudentmappings.id',$request->batch_id)
        // ->join('batchstudentmappings','batchstudentmappings.id',$request->batch_id)
        // ->join('batchstudentmappings',"JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
        // ->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetValue\"')")
        // ->where('batchstudentmappings.batch_id', $request->batch_id)
        // ->get();
        $batchesstudents = batchstudentmapping::where('batch_id',$request->batch_id)->where('tutor_id', session('userid')->id)->first();

        $targetStudentData = json_decode($batchesstudents->student_data);
        // $targetStudentData = ["1", "3"];
// dd($targetStudentData);
        $results['students'] = StudentRegistration::
            whereIn('id',$targetStudentData)
            // join('batchstudentmappings', function ($join) use ($targetStudentData) {
            //     $join->whereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"studentregistrations.id[0]\"')"); // For the first element
            //     for ($i = 1; $i < count($targetStudentData); $i++) {
            //         $join->orWhereRaw("JSON_CONTAINS(batchstudentmappings.student_data, '\"$targetStudentData[$i]\"')");
            //     }
            // })
            ->select('studentregistrations.*')
            // 'batchstudentmappings.*'
            // ->where('batchstudentmappings','batchstudentmappings.batch_id',$request->batchid)
            ->get();
        // dd($results);
        return response()->json($results);
    }

    // $studentId = 1;
    // $targetStudentData = ["1", "3","10","11"];

    // select a.* from studentregistrations a, batchstudentmappings b where '"'+a.id+'"' in b.student_data




    //     $studentId = 1;
    // $studentIdsArray = ["1", "2"];

    // $results = Table1::where('student_id', $studentId)
    //     ->join('table2', 'table1.student_id', '=', 'table2.student_id')
    //     ->whereIn('table2.student_id', $studentIdsArray)
    //     ->get();
}
