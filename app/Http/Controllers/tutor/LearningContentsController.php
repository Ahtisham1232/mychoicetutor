<?php

namespace App\Http\Controllers\tutor;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\learningcontents;
use App\Models\classes;
use App\Models\subjects;
use App\Models\topics;
use App\Models\studentregistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningContentsController extends Controller
{
    public function index()
    {
        $contents = learningcontents::select(
            '*',
            'learningcontents.id as contentid',
            'learningcontents.is_active as contentstatus',
            'classes.name as classname',
            'subjects.name as subjectname',
            'learningcontents.topic_name as topicname' // use topic_name directly
        )
            ->join('classes', 'classes.id', 'learningcontents.class_id')
            ->join('subjects', 'subjects.id', 'learningcontents.subject_id')
            ->where('learningcontents.tutor_id', session('userid')->id)
            ->paginate(10);
        //    dd($contents->items());


        $classes = classes::where('is_active', 1)->get();
        $subjects = subjects::where('is_active', 1)->get();

        // You can remove $topics if not needed anymore
        // $topics = topics::where('is_active',1)->get();

        return view('tutor.learningcontentslist', get_defined_vars());
    }


    public function search(Request $request)
    {
        $query = learningcontents::select('*', 'learningcontents.id as contentid', 'learningcontents.is_active as contentstatus', 'classes.name as classname', 'subjects.name as subjectname', 'topics.name as topicname')
            ->join('classes', 'classes.id', 'learningcontents.class_id')
            ->join('subjects', 'subjects.id', 'learningcontents.subject_id')
            ->join('topics', 'topics.id', 'learningcontents.topic_id')
            ->where('learningcontents.tutor_id', session('userid')->id);

        if ($request->class_name) {
            $query->where('learningcontents.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('learningcontents.subject_id', $request->subject_name);
        }
        if ($request->topic_name) {
            $query->where('learningcontents.topic_id', $request->topic_name);
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

        // Get students for this tutor
        $students = studentregistration::select(
            'studentregistrations.id',
            'studentregistrations.name'
        )
            ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
            ->where('paymentstudents.tutor_id', session('userid')->id)
            ->where('studentregistrations.is_active', 1)
            ->distinct()
            ->orderBy('studentregistrations.name')
            ->get();

        return view('tutor.addlearningcontents', compact('classes', 'pagename', 'students'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'classid' => 'required',
            'subjectid' => 'required',
            'topicid' => 'required'
        ]);

        if ($request->contentid) {
            $data = learningcontents::find($request->contentid);
        } else {
            $data = new learningcontents();
        }

        $data->class_id = $request->classid;
        $data->subject_id = $request->subjectid;
        $data->topic_name = $request->topicid;
        $data->tutor_id = session('userid')->id;

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
        // Verify tutor owns this content
        if ($data->tutor_id != session('userid')->id) {
            return json_encode(array('statusCode' => 403, 'message' => 'Unauthorized'));
        }

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

        // Verify tutor owns this content
        if ($ucontents->tutor_id != session('userid')->id) {
            return redirect()->route('tutor.learningcontents')->with('fail', 'Unauthorized access');
        }

        // Get students for this tutor
        $students = studentregistration::select(
            'studentregistrations.id',
            'studentregistrations.name'
        )
            ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
            ->where('paymentstudents.tutor_id', session('userid')->id)
            ->where('studentregistrations.is_active', 1)
            ->distinct()
            ->orderBy('studentregistrations.name')
            ->get();

        $subjects = subjects::select('*')->where('class_id', $ucontents->class_id)->where('is_active', 1)->get();

        return view('tutor.addlearningcontents', compact('ucontents', 'pagename', 'classes', 'students', 'subjects'));
    }

    public function deleteAll()
    {
        // Delete all learning contents
        $deleted = learningcontents::truncate();
        return response()->json([
            'success' => true,
            'message' => 'All learning contents deleted successfully'
        ]);
    }
}
