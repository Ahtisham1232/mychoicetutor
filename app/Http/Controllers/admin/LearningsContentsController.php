<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\learningcontents;
use App\Models\classes;
use App\Models\subjects;
use App\Models\topics;
use Illuminate\Http\Request;

class LearningsContentsController extends Controller
{
    public function index()
    {
        $contents = learningcontents::select('*', 'learningcontents.id as contentid', 'learningcontents.is_active as contentstatus', 'classes.name as classname', 'subjects.name as subjectname')
            ->join('classes', 'classes.id', 'learningcontents.class_id')
            ->join('subjects', 'subjects.id', 'learningcontents.subject_id')
            ->paginate(10);
        $classes = classes::where('is_active', 1)->get();
        $subjects = subjects::where('is_active', 1)->get();
        $topics = learningcontents::select('topic_name')->distinct()->get();

        return view('admin.learningcontentslist', get_defined_vars());
    }
    // search functionality
    public function search(Request $request)
    {
        $query = learningcontents::select('*', 'learningcontents.id as contentid', 'learningcontents.is_active as contentstatus', 'classes.name as classname', 'subjects.name as subjectname')
            ->join('classes', 'classes.id', 'learningcontents.class_id')
            ->join('subjects', 'subjects.id', 'learningcontents.subject_id');
        // ->get();
        if ($request->filled('class_name')) {
            $query->where('learningcontents.class_id', $request->class_name);
        }

        if ($request->filled('subject_name')) {
            $query->where('learningcontents.subject_id', $request->subject_name);
        }

        if ($request->filled('status_field')) {
            $status = $request->status_field == 2 ? 0 : 1;
            $query->where('learningcontents.is_active', $status);
        }
        if ($request->filled('topic_name')) {
            $query->where('learningcontents.topic_name', 'like', '%' . $request->topic_name . '%');
        }




        if ($request->status_field) {
            if ($request->status_field == '2') {
                $request->status_field = '0';
            }
            $query->where('learningcontents.is_active', $request->status_field);
        }


        $contents = $query->paginate(5);
        $type = 'contents';
        $viewTable = view('admin.partials.students-tutor-search', compact('contents', 'type'))->render();
        $viewPagination = $contents->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);
    }

    public function add()
    {
        $pagename = 'Add Learning Content';
        $classes = (new CommonController)->classes();
        // Get all active students for admin
        $students = \App\Models\studentregistration::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('admin.addlearningcontents', compact('classes', 'pagename', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classid' => 'required',
            'subjectid' => 'required',
            'topic_name' => 'required'
        ]);

        if ($request->contentid) {
            $data = learningcontents::find($request->contentid);
        } else {
            $data = new learningcontents();
        }

        $data->class_id = $request->classid;
        $data->subject_id = $request->subjectid;
        $data->topic_name = $request->topic_name;
        $data->tutor_id = null; // Admin created content has no tutor_id

        // Handle student_ids - if empty, set to null (means all students)
        if ($request->student_ids && count($request->student_ids) > 0) {
            $data->student_ids = json_encode($request->student_ids);
        } else {
            $data->student_ids = null; // null means visible to all students
        }

        if ($request->uploadcontent) {
            $contentlink = time() . '.' . $request->uploadcontent->extension();
            $request->uploadcontent->move(public_path('uploads/documents/learningcontents'), $contentlink);
            $data->content_link = $contentlink;
        }
        if ($request->uploadvideo) {
            $videolink = time() . '.' . $request->uploadvideo->extension();
            $request->uploadvideo->move(public_path('uploads/videos/learningcontents'), $videolink);
            $data->video_link = $videolink;
        }

        $data->content_description = $request->contentdescription;
        $data->video_description = $request->videodescription;
        $data->blog_link = $request->bloglink;
        $data->blog_description = $request->blogdescription;
        $data->is_active = 1;
        // dd('fadfddsff');


        $res = $data->save();
        if ($data) {
            return back()->with('success', 'Content added successfully');
        } else {
            return back()->with('fail', 'Something went wrong. Please try again later');
        }
    }
    public function status(Request $request)
    {
        $data = learningcontents::find($request->id);
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

    public function edit($id)
    {
        $pagename = 'Update content details';
        $classes = (new CommonController)->classes();
        $ucontents = learningcontents::find($id);
        $subjects = subjects::select('*')->where('class_id', $ucontents->class_id)->where('is_active', 1)->get();
        // Get all active students for admin
        $students = \App\Models\studentregistration::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('admin.addlearningcontents', compact('ucontents', 'pagename', 'classes', 'students','subjects'));
    }
}
