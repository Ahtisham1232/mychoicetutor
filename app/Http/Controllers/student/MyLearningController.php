<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\learningcontents;
use App\Models\payments\paymentstudents;
use Illuminate\Http\Request;

class MyLearningController extends Controller
{
    public function index(Request $request)
    {
        $studentId = session('userid')->id;
        $query = learningcontents::select('learningcontents.*')
            ->leftJoin('subjects', 'subjects.id', 'learningcontents.subject_id')
            ->where('learningcontents.is_active', 1)
            ->where(function ($q) use ($studentId) {
                $q->whereNull('learningcontents.student_ids')
                    ->orWhereJsonContains('learningcontents.student_ids', $studentId);
            });
            
        if ($request->input('topic')) {
            $query->where('learningcontents.topic_name', 'like', '%' . $request->topic . '%');
        }

        $learnings =  $query->paginate(10);
        return view('student.mylearnings', get_defined_vars());
    }

    public function parent_index(Request $request)
    {
        $pdetails = paymentstudents::select('*')->where('class_id', session('userid')->id)->where('student_id', session('userid')->id)->first();
        $query = learningcontents::select('learningcontents.*')
            ->join('classes', 'classes.id', 'learningcontents.class_id')
            ->join('subjects', 'subjects.id', 'learningcontents.subject_id')
            ->where('classes.id', session('userid')->class_id)
            ->where('subjects.id', $pdetails->subject_id);
        if ($request->input('topic')) {
            $query->where('topics.name', 'like', '%' . $request->topic . '%');
            $requests = $request->all();
        }
        $learnings =  $query->paginate(10);
        return view('parent.mylearnings', get_defined_vars());
    }
}
