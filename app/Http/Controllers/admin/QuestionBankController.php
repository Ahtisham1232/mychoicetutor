<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Controller;
use App\Models\questionbank;
use App\Models\subjects;
use App\Models\topics;
use App\Models\classes;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(){

        $questions = questionbank::select('*','questionbanks.id as question_id','questionbanks.is_active as question_status','classes.name as class','subjects.name as subject','questionbanks.topic_name as topic')
        ->join('classes','classes.id','questionbanks.class_id')
        ->join('subjects','subjects.id','questionbanks.subject_id')->orderby('questionbanks.created_at','desc')
        ->paginate(10);
        $classes = classes::where('is_active',1)->get();
        $subjects = subjects::where('is_active',1)->get();
        $topics = topics::where('is_active',1)->get();
        return view('admin.questionbanklist',get_defined_vars());

    }


    // search Functionality

    public function questionbankSearch(Request $request){

    //    return $request->all();
        $query   = questionbank::select('*','questionbanks.id as question_id','questionbanks.is_active as question_status','classes.name as class','subjects.name as subject','topics.name as topic')
        ->join('classes','classes.id','questionbanks.class_id')
        ->join('subjects','subjects.id','questionbanks.subject_id')
        ->join('topics','topics.id','questionbanks.topic_id');

        if ($request->question_name) {

            $query->where('questionbanks.question','like', '%' . $request->question_name . '%');
        }
        if ($request->class_name) {
            $query->where('questionbanks.class_id', $request->class_name);
        }
        if ($request->subject_name) {
            $query->where('questionbanks.subject_id', $request->subject_name);
        }
        if ($request->topic_name) {
            $query->where('questionbanks.topic_name', $request->topic_name);
        }
        if ($request->status_field) {
            if($request->status_field=='2'){
                $request->status_field = '0';
            }
            $query->where('questionbanks.is_active',$request->status_field);
        }
        $questions = $query->paginate(10);
        $type = 'questions';
        $viewTable = view('admin.partials.students-tutor-search', compact('questions','type'))->render();
        $viewPagination = $questions->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination
        ]);


    }


    public function create(){
        $classes = (new CommonController)->classes();
        return view('admin.questionbank',compact('classes'));
    }
    public function store(Request $request){

        $request->validate([
            'classname'=>'required',
            'subject'=>'required',
            'topic'=>'required',
            'editor1'=>'required',
            'optiona'=>'required',
            'optionb'=>'required',
            'optionc'=>'required',
            'optiond'=>'required',
            'correctanswer'=>'required',
        ]);
        if($request->id){
            $data = questionbank::find($request->id);
            $msg = 'Question updated successfully';
        }
        else{
            $data = new questionbank();
            $msg = 'Question added successfully';
        }
        $data->class_id = $request->classname;
        $data->subject_id = $request->subject;
        $data->topic_name=$request->topic;
        $data->question=$request->editor1;
        $data->option1=$request->optiona;
        $data->option2=$request->optionb;
        $data->option3=$request->optionc;
        $data->option4=$request->optiond;
        if($request->correctanswer == 'A'){
            $data->correct_option=$request->optiona;
        }
        if($request->correctanswer == 'B'){
            $data->correct_option=$request->optionb;
        }
        if($request->correctanswer == 'C'){
            $data->correct_option=$request->optionc;
        }
        if($request->correctanswer == 'D'){
            $data->correct_option=$request->optiond;
        }
        $data->remarks=$request->remarks;
        $data->type='1';
        $res = $data->save();
        if($res){
            return back()->with('success',$msg);
        }
        else{
            return back()->with('fail','Something went wrong. Please try again later');
        }
    }

    public function status(Request $request){
        $data = questionbank::find($request->id);
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

    public function view($id){
        $classes = (new CommonController)->classes();
        $qdata = questionbank::select('*')
        ->where('id', $id)->first();
        $subjects = subjects::select('*')->where('class_id',$qdata->class_id)->get();
        $label = 'Update Question';

        // dd($qdata);
        if($qdata->type == 1){
            return view('admin.questionbank',compact('qdata','classes','subjects','label'));
        }elseif($qdata->type == 2){
            return view('admin.questionbanksubjective',get_defined_vars());
        }
    }


    public function subjective_create(){
        $classes = (new CommonController)->classes();
        return view('admin.questionbanksubjective',compact('classes'));
    }


    public function storeSubjective(Request $request    ){
        $request->validate([
            'classname'=>'required',
            'subject'=>'required',
            'topic'=>'required',
            'editor1'=>'required',
        ]);
        if($request->id){
            $data = questionbank::find($request->id);
            $msg = 'Question updated successfully';
        }
        else{
            $data = new questionbank();
            $msg = 'Question added successfully';
        }
        $data->class_id = $request->classname;
        $data->subject_id = $request->subject;
        $data->topic_name=$request->topic;
        $data->question=$request->editor1;
        $data->remarks=$request->remarks;
        $data->type='2';
        $res = $data->save();
        if($res){
            return back()->with('success',$msg);
        }
        else{
            return back()->with('fail','Something went wrong. Please try again later');
        }
    }



    // tutor questio bank functions
    public function tutorQuestionbank(){

        $questions = questionbank::select('*','questionbanks.id as question_id','questionbanks.is_active as question_status','classes.name as class','subjects.name as subject','questionbanks.topic_name as topic')
        ->join('classes','classes.id','questionbanks.class_id')
        ->join('subjects','subjects.id','questionbanks.subject_id')
        ->where('questionbanks.tutor_id', session('userid')->id)
        ->orderby('questionbanks.created_at','desc')
        ->paginate(10);
        $classes = classes::where('is_active',1)->get();
        $subjects = subjects::where('is_active',1)->get();
        $topics = topics::where('is_active',1)->get();
        return view('tutor.tutor-questionbanklist',get_defined_vars());

    }
    public function tutorcreate(){
        $classes = (new CommonController)->classes();
        return view('tutor.tutor-questionbank',compact('classes'));
    }
    public function tutorstore(Request $request){

        $request->validate([
            'classname'=>'required',
            'subject'=>'required',
            'topic'=>'required',
            'editor1'=>'required',
            'optiona'=>'required',
            'optionb'=>'required',
            'optionc'=>'required',
            'optiond'=>'required',
            'correctanswer'=>'required',
        ]);
        if($request->id){
            $data = questionbank::find($request->id);
            $msg = 'Question updated successfully';
        }
        else{
            $data = new questionbank();
            $msg = 'Question added successfully';
        }
        $data->class_id = $request->classname;
        $data->subject_id = $request->subject;
        $data->topic_name=$request->topic;
        $data->tutor_id = session('userid')->id;
        $data->question=$request->editor1;
        $data->option1=$request->optiona;
        $data->option2=$request->optionb;
        $data->option3=$request->optionc;
        $data->option4=$request->optiond;
        if($request->correctanswer == 'A'){
            $data->correct_option=$request->optiona;
        }
        if($request->correctanswer == 'B'){
            $data->correct_option=$request->optionb;
        }
        if($request->correctanswer == 'C'){
            $data->correct_option=$request->optionc;
        }
        if($request->correctanswer == 'D'){
            $data->correct_option=$request->optiond;
        }

        $data->remarks=$request->remarks;
        $data->type='1';
        $res = $data->save();
        if($res){
            return back()->with('success',$msg);
        }
        else{
            return back()->with('fail','Something went wrong. Please try again later');
        }
    }
    public function tutor_subjective_create(){
        $classes = (new CommonController)->classes();
        return view('tutor.tutor-questionbanksubjective',compact('classes'));
    }
    public function tutorview($id){
        $classes = (new CommonController)->classes();
        $qdata = questionbank::select('*')
        ->where('id', $id)->first();
        $subjects = subjects::select('*')->where('class_id',$qdata->class_id)->get();
        $topics = topics::select('*')->where('subject_id',$qdata->subject_id)->get();
        $label = 'Update Question';
        if($qdata->type == 1){
            return view('tutor.tutor-questionbank',compact('qdata','classes','subjects','topics','label'));
        }elseif($qdata->type == 2){
            return view('tutor.tutor-questionbanksubjective',get_defined_vars());
        }


    }
}
