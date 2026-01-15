<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\admin\admin;
use App\Models\tutorregistration;
use App\Models\tutorprofile;
use App\Models\studentregistration;
use App\Models\studentprofile;
use App\Models\ChMessage;
use App\Events\NewMessage;
use App\Events\MessageNotification;

class MessagesController extends Controller
{
    /**
     * Student messages - placeholder method
     */
    public function messagesbystudent()
    {
        // Get admins (role_id = 1) - admins don't have profile pics, so we'll handle this in the view
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1);

        // Get only tutors assigned to this student via paymentstudents table with profile pics
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id', 'tutorprofiles.profile_pic')
                                  ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                                  ->join('paymentstudents', 'paymentstudents.tutor_id', '=', 'tutorregistrations.id')
                                  ->where('paymentstudents.student_id', session('userid')->id)
                                  ->where('tutorregistrations.id', '!=', session('userid')->id)
                                  ->where('tutorregistrations.role_id', 2)
                                  ->distinct();

        $userlists = $admins->union($tutors)->get();

        // Get student's profile picture
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('student.messages', compact('userlists', 'studentProfile', 'messages'))->with('info', 'Messages feature is temporarily unavailable.');
    }

    /**
     * Student admin messages - placeholder method
     */
    public function messagesbystudentadmins()
    {
        $userlists = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                         ->where('id', '!=', session('userid')->id)
                         ->where('role_id', 1) // Get only admins
                         ->get();

        // Get student's profile picture
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('student.messages', compact('userlists', 'studentProfile', 'messages'))->with('info', 'Admin messages feature is temporarily unavailable.');
    }

    /**
     * Student admin messages by ID - placeholder method
     */
    public function messagesbystudentadminmessages($id)
    {
        // Get admins
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id')
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1);

        // Get only tutors assigned to this student
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id')
                                  ->join('paymentstudents', 'paymentstudents.tutor_id', '=', 'tutorregistrations.id')
                                  ->where('paymentstudents.student_id', session('userid')->id)
                                  ->where('tutorregistrations.id', '!=', session('userid')->id)
                                  ->where('tutorregistrations.role_id', 2)
                                  ->distinct();

        $userlists = $admins->union($tutors)->get();

        // Get the specific admin for the header
        $header = admin::find($id);

        // Get student's profile picture
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Get messages between student and this admin
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 1); // Admin role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 1) // Admin role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('student.messages', compact('userlists', 'header', 'messages', 'studentProfile'))->with('info', 'Admin messages feature is temporarily unavailable.');
    }

    /**
     * Student admin messages load by ID
     */
    public function messagesbystudentadminmessagesload($id)
    {
        // Get messages between student and admin
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 1); // Admin role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 1) // Admin role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        // Get student profile for avatar
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Return HTML partial instead of JSON
        return view('student.partials.chat-messages', compact('messages', 'studentProfile'))->render();
    }

    /**
     * Student tutor messages - placeholder method
     */
    public function messagesbystudenttutor()
    {
        $userlists = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id', 'tutorprofiles.profile_pic')
                                    ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                                    ->join('paymentstudents', 'paymentstudents.tutor_id', '=', 'tutorregistrations.id')
                                    ->where('paymentstudents.student_id', session('userid')->id)
                                    ->where('tutorregistrations.id', '!=', session('userid')->id)
                                    ->where('tutorregistrations.role_id', 2) // Get only tutors
                                    ->distinct()
                                    ->get();

        // Get student's profile picture
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('student.messages', compact('userlists', 'studentProfile', 'messages'))->with('info', 'Tutor messages feature is temporarily unavailable.');
    }

    /**
     * Student tutor messages by ID - placeholder method
     */
    public function messagesbystudenttutormessages($id)
    {
        // Get admins
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id')
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1);

        // Get only tutors assigned to this student
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id')
                                  ->join('paymentstudents', 'paymentstudents.tutor_id', '=', 'tutorregistrations.id')
                                  ->where('paymentstudents.student_id', session('userid')->id)
                                  ->where('tutorregistrations.id', '!=', session('userid')->id)
                                  ->where('tutorregistrations.role_id', 2)
                                  ->distinct();

        $userlists = $admins->union($tutors)->get();

        // Get the specific tutor for the header with profile picture
        $header = tutorregistration::select('tutorregistrations.*', 'tutorprofiles.profile_pic')
                                  ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                                  ->where('tutorregistrations.id', $id)
                                  ->first();

        // Get student's profile picture
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Get messages between student and this tutor
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 2); // Tutor role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 2) // Tutor role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('student.messages', compact('userlists', 'header', 'messages', 'studentProfile'))->with('info', 'Tutor messages feature is temporarily unavailable.');
    }

    /**
     * Student tutor messages load by ID
     */
    public function messagesbystudenttutormessagesload($id)
    {
        // Get messages between student and tutor
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 2); // Tutor role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 2) // Tutor role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        // Get student profile for avatar
        $studentProfile = studentprofile::where('student_id', session('userid')->id)->first();

        // Return HTML partial instead of JSON
        return view('student.partials.chat-messages', compact('messages', 'studentProfile'))->render();
    }

    /**
     * Student send message
     */
    public function messagesentbystudent(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'receiver_id' => 'required|integer',
            'receiver_role_id' => 'required|integer',
        ]);

        // Create new message
        $message = new ChMessage();
        $message->from_id = session('userid')->id;
        $message->from_role_id = session('userid')->role_id;
        $message->to_id = $request->receiver_id;
        $message->to_role_id = $request->receiver_role_id;
        $message->body = $request->message;
        $message->seen = 0;
        $message->save();

        // Broadcast the message
        broadcast(new NewMessage($message));

        // Send notification to receiver
        $senderName = session('userid')->name;
        broadcast(new MessageNotification($message, $senderName, session('userid')->role_id));

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Admin messages - placeholder method
     */
    public function messagesbyadmin()
    {
        return view('admin.messages')->with('info', 'Messages feature is temporarily unavailable.');
    }

    /**
     * Admin student messages - placeholder method
     */
    public function messagesbyadminstudents()
    {
        $userlists = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id', 'studentprofiles.profile_pic')
                                      ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
                                      ->where('studentregistrations.id', '!=', session('userid')->id)
                                      ->where('studentregistrations.role_id', 3) // Get only students
                                      ->get();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('admin.messages', compact('userlists', 'messages'))->with('info', 'Student messages feature is temporarily unavailable.');
    }

    /**
     * Admin student messages by ID - placeholder method
     */
    public function messagesbyadminstudentmessages($id)
    {
        // Get all students for the user list
        $userlists = studentregistration::select('id', 'name', 'email', 'mobile', 'role_id')
                                      ->where('id', '!=', session('userid')->id)
                                      ->where('role_id', 3) // Get only students
                                      ->get();

        // Get the specific student for the header
        $header = studentregistration::find($id);

        // Get messages between admin and this student
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 3); // Student role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 3) // Student role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('admin.messages', compact('userlists', 'header', 'messages'))->with('info', 'Student messages feature is temporarily unavailable.');
    }

    /**
     * Admin student messages load by ID
     */
    public function messagesbyadminstudentmessagesload($id)
    {
        // Get messages between admin and student
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 3); // Student role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 3) // Student role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Admin clear student messages - placeholder method
     */
    public function chatClearAdminstudent($id)
    {
        return redirect()->back()->with('info', 'Message clearing is temporarily unavailable.');
    }

    /**
     * Admin tutor messages - placeholder method
     */
    public function messagesbyadmintutor()
    {
        $userlists = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id', 'tutorprofiles.profile_pic')
                                    ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                                    ->where('tutorregistrations.id', '!=', session('userid')->id)
                                    ->where('tutorregistrations.role_id', 2) // Get only tutors
                                    ->get();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('admin.messages', compact('userlists', 'messages'))->with('info', 'Tutor messages feature is temporarily unavailable.');
    }

    /**
     * Admin tutor messages by ID - placeholder method
     */
    public function messagesbyadmintutormessages($id)
    {
        // Get all tutors for the user list
        $userlists = tutorregistration::select('id', 'name', 'email', 'mobile', 'role_id')
                                    ->where('id', '!=', session('userid')->id)
                                    ->where('role_id', 2) // Get only tutors
                                    ->get();

        // Get the specific tutor for the header
        $header = tutorregistration::find($id);

        // Get messages between admin and this tutor
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 2); // Tutor role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 2) // Tutor role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('admin.messages', compact('userlists', 'header', 'messages'))->with('info', 'Tutor messages feature is temporarily unavailable.');
    }

    /**
     * Admin tutor messages load by ID
     */
    public function messagesbyadmintutormessagesload($id)
    {
        // Get messages between admin and tutor
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 2); // Tutor role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 2) // Tutor role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Admin clear tutor messages - placeholder method
     */
    public function chatClearAdmintutor($id)
    {
        return redirect()->back()->with('info', 'Message clearing is temporarily unavailable.');
    }

    /**
     * Admin send message
     */
    public function messagesentbyadmin(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'receiver_id' => 'required|integer',
            'receiver_role_id' => 'required|integer',
        ]);

        // Create new message
        $message = new ChMessage();
        $message->from_id = session('userid')->id;
        $message->from_role_id = session('userid')->role_id;
        $message->to_id = $request->receiver_id;
        $message->to_role_id = $request->receiver_role_id;
        $message->body = $request->message;
        $message->seen = 0;
        $message->save();

        // Broadcast the message
        broadcast(new NewMessage($message));

        // Send notification to receiver
        $senderName = session('userid')->name;
        broadcast(new MessageNotification($message, $senderName, session('userid')->role_id));

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Admin chat student search - placeholder method
     */
    public function chatstudentsearch(Request $request)
    {
        return response()->json(['results' => []]);
    }

    /**
     * Admin chat tutor search - placeholder method
     */
    public function chattutorsearch(Request $request)
    {
        return response()->json(['results' => []]);
    }

    /**
     * Tutor messages - placeholder method
     */
    public function messagesbytutor()
    {
        // Get admins (role_id = 1)
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1);

        // Get only students assigned to this tutor via paymentstudents table with profile pics
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id', 'studentprofiles.profile_pic')
                                      ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
                                      ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
                                      ->where('paymentstudents.tutor_id', session('userid')->id)
                                      ->where('studentregistrations.id', '!=', session('userid')->id)
                                      ->where('studentregistrations.role_id', 3)
                                      ->distinct();

        $userlists = $admins->union($students)->get();

        // Get tutor's profile picture
        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('tutor.messages', compact('userlists', 'tutorProfile', 'messages'))->with('info', 'Messages feature is temporarily unavailable.');
    }

    /**
     * Tutor admin messages - placeholder method
     */
    public function messagesbytutoradmins()
    {
        $userlists = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                         ->where('id', '!=', session('userid')->id)
                         ->where('role_id', 1) // Get only admins
                         ->get();

        // Get tutor's profile picture
        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('tutor.messages', compact('userlists', 'tutorProfile', 'messages'))->with('info', 'Admin messages feature is temporarily unavailable.');
    }

    /**
     * Tutor admin messages by ID - placeholder method
     */
    public function messagesbytutoradminmessages($id)
    {
        // Get admins
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id')->where('id', '!=', session('userid')->id)->where('role_id', 1);

        // Get only students assigned to this tutor
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id')
                                      ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
                                      ->where('paymentstudents.tutor_id', session('userid')->id)
                                      ->where('studentregistrations.id', '!=', session('userid')->id)
                                      ->where('studentregistrations.role_id', 3)
                                      ->distinct();

        $userlists = $admins->union($students)->get();

        // Get the specific admin for the header
        $header = admin::find($id);

        // Get tutor's profile picture
        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        // Get messages between tutor and this admin
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 1); // Admin role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 1) // Admin role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('tutor.messages', compact('userlists', 'header', 'messages', 'tutorProfile'))->with('info', 'Admin messages feature is temporarily unavailable.');
    }

    /**
     * Tutor admin messages load by ID
     */
    public function messagesbytutoradminmessagesload($id)
    {
        // Get messages between tutor and admin
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 1); // Admin role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 1) // Admin role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Tutor student messages - placeholder method
     */
    public function messagesbytutorstudents()
    {
        $userlists = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id', 'studentprofiles.profile_pic')
                                      ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
                                      ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
                                      ->where('paymentstudents.tutor_id', session('userid')->id)
                                      ->where('studentregistrations.id', '!=', session('userid')->id)
                                      ->where('studentregistrations.role_id', 3) // Get only students
                                      ->distinct()
                                      ->get();

        // Get tutor's profile picture
        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];

        return view('tutor.messages', compact('userlists', 'tutorProfile', 'messages'))->with('info', 'Student messages feature is temporarily unavailable.');
    }

    /**
     * Tutor student messages by ID - placeholder method
     */
    public function messagesbytutorstudentmessages($id)
    {
        // Get admins
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id')->where('id', '!=', session('userid')->id)->where('role_id', 1);

        // Get only students assigned to this tutor
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id')
                                      ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
                                      ->where('paymentstudents.tutor_id', session('userid')->id)
                                      ->where('studentregistrations.id', '!=', session('userid')->id)
                                      ->where('studentregistrations.role_id', 3)
                                      ->distinct();

        $userlists = $admins->union($students)->get();

        // Get the specific student for the header with profile picture
        $header = studentregistration::select('studentregistrations.*', 'studentprofiles.profile_pic')
                                    ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
                                    ->where('studentregistrations.id', $id)
                                    ->first();

        // Get tutor's profile picture
        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        // Get messages between tutor and this student
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 3); // Student role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 3) // Student role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'desc')->get();

        return view('tutor.messages', compact('userlists', 'header', 'messages', 'tutorProfile'))->with('info', 'Student messages feature is temporarily unavailable.');
    }

    /**
     * Tutor student messages load by ID
     */
    public function messagesbytutorstudentmessagesload($id)
    {
        // Get messages between tutor and student
        $messages = ChMessage::where(function($query) use ($id) {
            $query->where('from_id', session('userid')->id)
                  ->where('from_role_id', session('userid')->role_id)
                  ->where('to_id', $id)
                  ->where('to_role_id', 3); // Student role
        })->orWhere(function($query) use ($id) {
            $query->where('from_id', $id)
                  ->where('from_role_id', 3) // Student role
                  ->where('to_id', session('userid')->id)
                  ->where('to_role_id', session('userid')->role_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * Tutor send message
     */
    public function messagesentbytutor(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'receiver_id' => 'required|integer',
            'receiver_role_id' => 'required|integer',
        ]);

        // Create new message
        $message = new ChMessage();
        $message->from_id = session('userid')->id;
        $message->from_role_id = session('userid')->role_id;
        $message->to_id = $request->receiver_id;
        $message->to_role_id = $request->receiver_role_id;
        $message->body = $request->message;
        $message->seen = 0;
        $message->save();

        // Broadcast the message
        broadcast(new NewMessage($message));

        // Send notification to receiver
        $senderName = session('userid')->name;
        broadcast(new MessageNotification($message, $senderName, session('userid')->role_id));

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }
}
