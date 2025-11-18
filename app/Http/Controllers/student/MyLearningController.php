<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\learningcontents;
use App\Models\payments\paymentstudents;
use Illuminate\Http\Request;

class MyLearningController extends Controller
{
    public function index(Request $request){
        $studentId = session('userid')->id;
        $query = learningcontents::select('learningcontents.*','topics.name as topic_name')
            ->join('topics','topics.id','learningcontents.topic_id')
            ->leftJoin('subjects','subjects.id','learningcontents.subject_id')
            ->where('learningcontents.is_active', 1)
            ->where(function($q) use ($studentId) {
                // Show content if:
                // 1. student_ids is null (visible to all students) OR
                // 2. student_ids contains this student's ID
                $q->whereNull('learningcontents.student_ids')
                  ->orWhereJsonContains('learningcontents.student_ids', $studentId);
            });
            
        if($request->input('topic')){
            $query->where('topics.name','like', '%' . $request->topic . '%');
            $requests = $request->all();
        }
        
       $learnings =  $query->paginate(10);
        return view('student.mylearnings',get_defined_vars());
    }

    public function parent_index(Request $request){
        $pdetails = paymentstudents::select('*')->where('class_id',session('userid')->id)->where('student_id',session('userid')->id)->first();
        $query = learningcontents::select('learningcontents.*','topics.name as topic_name')
        // ->join('paymentstudents','paymentstudents.subject_id','learningcontents.subject_id')
        // ->join('paymentdetails','paymentdetails.transaction_id','paymentstudents.transaction_id')
        ->join('topics','topics.id','learningcontents.topic_id')
        ->join('classes','classes.id','learningcontents.class_id')
        ->join('subjects','subjects.id','learningcontents.subject_id')
        // ->where('paymentstudents.student_id',session('userid')->id)
        ->where('classes.id',session('userid')->class_id)
        ->where('subjects.id',$pdetails->subject_id);
        if($request->input('topic')){
            $query->where('topics.name','like', '%' . $request->topic . '%');
            $requests = $request->all();
        }
        // ->where('paymentstudents.class_id',session('userid')->class_id)
       $learnings =  $query->paginate(10);
        return view('parent.mylearnings',get_defined_vars());
    }
}
