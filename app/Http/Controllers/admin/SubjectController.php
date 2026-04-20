<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\classes;
use App\Models\subjects;
use Illuminate\Http\Request;
use App\Models\subjectcategory;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(){

        $classes = classes::select('*')
            ->where('is_active',1)->get();

        $subjects = subjects::select('classes.id as class_id','classes.name as class_name','subjects.name as subject_name','subjects.id as subject_id','subjects.is_active as subject_status','subjects.image as subject_image')
        ->join('classes','classes.id','=','subjects.class_id')
        // ->join('subjectcategories','subjectcategories.id','subjects.category')
        ->orderBy('subjects.id','desc')
        ->get();

        // $scategories = subjectcategory::select('*')->where('is_active',1)->get();
        return view('admin.subject',compact('subjects','classes'));
    }

    public function store(Request $request){
        $request->validate([
            'classname'=> 'required',
            'subject'=> 'required',
            'uploadimage'=>'required',
            // 'categoryid'=>'required'
        ]);
        if($request->id){
            $data = subjects::find($request->id);
            $msg = 'Subject updated successfully';
        }
        else{
            $data = new subjects();
            $msg = 'Subject added successfully';
        }
        if($request->uploadimage){
            
            $imageName = time().'.'.$request->uploadimage->extension();

            $request->uploadimage->move(public_path('images/subjects'), $imageName);
            $data->image = $imageName;
        }

        $data->class_id = $request->classname;
        $data->name = $request->subject;
        
        $data->category = $request->categoryid ?? 1;
        $res = $data->save();

        if($res){
            return back()->with('success',$msg);
        }
        else{
            return back()->with('fail','Something went wrong. Please try again later');
        }
    }
    public function status(Request $request){
        // echo 'Test';
        // echo $request->id;
        // echo $request->status;
        // dd();
        $data = subjects::find($request->id);
        if($request->status == 1){
            $status = 0;
        }
        if($request->status == 0){
            $status = 1;
        }
        $data->is_active = $status;

       $res = $data->save();
     return json_encode(array('statusCode'=>200));
    }

    public function subjectcategory(){

        return view('admin.subjectcategory');
    }
    public function cmsindex(){
        // $subjects = Subjects::select('*')->where('is_active',1)->get();
        $subjects = Subjects::getUniqueSubjects();;
        $subjectswc = Subjects::select('subjects.*','subjectcategories.name as category_name')
        ->join('subjectcategories','subjectcategories.id','subjects.category')
        ->where('subjects.is_active',1)
        ->get()
        ->groupBy('category_name');
        return view('front-cms.allsubjects',compact('subjects','subjectswc'));
    }
}
