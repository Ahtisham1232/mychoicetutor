<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\admin\admin;
use App\Models\tutorregistration;
use App\Models\tutorprofile;
use App\Models\studentregistration;
use App\Models\studentprofile;
use App\Models\ChMessage;
use App\Models\Notification;
use App\Events\NewMessage;
use App\Events\MessageNotification;

class MessagesController extends Controller
{

    public function chatPresenceAuth(Request $request)
    {
        $user = session('userid');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $channelName = $request->input('channel_name');
        $socketId = $request->input('socket_id');
        if ($channelName !== 'presence-chat' || !$socketId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $key = config('chatify.pusher.key');
        $secret = config('chatify.pusher.secret');
        if (!$key || !$secret) {
            return response()->json(['error' => 'Server config error'], 500);
        }
        $userId = $user->role_id . '_' . $user->id;
        $channelData = json_encode([
            'user_id' => $userId,
            'user_info' => [
                'name' => $user->name ?? '',
                'role_id' => $user->role_id,
            ],
        ]);
        $stringToSign = $socketId . ':' . $channelName . ':' . $channelData;
        $signature = hash_hmac('sha256', $stringToSign, $secret, false);
        return response()->json([
            'auth' => $key . ':' . $signature,
            'channel_data' => $channelData,
        ]);
    }

    /**
     * Add is_online flag to each user in the list based on cache presence.
     *
     * @param \Illuminate\Support\Collection $userlists
     * @return \Illuminate\Support\Collection
     */
    protected function addOnlineStatusToUserList($userlists)
    {
        if ($userlists === null) {
            return $userlists;
        }
        $userlists->each(function ($user) {
            $user->is_online = Cache::has('chat_online:' . $user->role_id . ':' . $user->id);
        });
        return $userlists;
    }

    /**
     * Build a status map (role_id + '_' + id => is_online) from a user list.
     *
     * @param \Illuminate\Support\Collection $userlists
     * @return array<string, bool>
     */
    protected function userListToStatusMap($userlists)
    {
        $status = [];
        if ($userlists === null) {
            return $status;
        }
        foreach ($userlists as $user) {
            $key = $user->role_id . '_' . $user->id;
            $status[$key] = (bool) ($user->is_online ?? false);
        }
        return $status;
    }

    /**
     * JSON endpoint: online status for student's chat list (admins + assigned tutors).
     */
    public function chatPresenceStatusStudent(Request $request)
    {
        $user = session('userid');
        if (!$user || $user->role_id != 3) {
            return response()->json(['status' => []], 401);
        }
        $admins = admin::select('id', 'name', 'role_id')
            ->where('id', '!=', $user->id)
            ->where('role_id', 1);
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.role_id')
            ->join('paymentstudents', 'paymentstudents.tutor_id', '=', 'tutorregistrations.id')
            ->where('paymentstudents.student_id', $user->id)
            ->where('tutorregistrations.id', '!=', $user->id)
            ->where('tutorregistrations.role_id', 2)
            ->distinct();
        $userlists = $admins->union($tutors)->get();
        $this->addOnlineStatusToUserList($userlists);
        return response()->json(['status' => $this->userListToStatusMap($userlists)]);
    }

    /**
     * JSON endpoint: online status for admin's chat list (admins + tutors + students).
     */
    public function chatPresenceStatusAdmin(Request $request)
    {
        $user = session('userid');
        if (!$user || $user->role_id != 1) {
            return response()->json(['status' => []], 401);
        }
        $admins = admin::select('id', 'name', 'role_id')
            ->where('id', '!=', $user->id)
            ->where('role_id', 1)
            ->get();
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.role_id')
            ->where('tutorregistrations.id', '!=', $user->id)
            ->where('tutorregistrations.role_id', 2)
            ->get();
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.role_id')
            ->where('studentregistrations.id', '!=', $user->id)
            ->where('studentregistrations.role_id', 3)
            ->get();
        $userlists = $admins->merge($tutors)->merge($students);
        $this->addOnlineStatusToUserList($userlists);
        return response()->json(['status' => $this->userListToStatusMap($userlists)]);
    }

    /**
     * JSON endpoint: online status for tutor's chat list (admins + assigned students).
     */
    public function chatPresenceStatusTutor(Request $request)
    {
        $user = session('userid');
        if (!$user || $user->role_id != 2) {
            return response()->json(['status' => []], 401);
        }
        $admins = admin::select('id', 'name', 'role_id')
            ->where('id', '!=', $user->id)
            ->where('role_id', 1);
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.role_id')
            ->join('paymentstudents', 'paymentstudents.student_id', '=', 'studentregistrations.id')
            ->where('paymentstudents.tutor_id', $user->id)
            ->where('studentregistrations.id', '!=', $user->id)
            ->where('studentregistrations.role_id', 3)
            ->distinct();
        $userlists = $admins->union($students)->get();
        $this->addOnlineStatusToUserList($userlists);
        return response()->json(['status' => $this->userListToStatusMap($userlists)]);
    }
    /**
     * Student messages - placeholder method
     */
    public function messagesbystudent()
    {
        // Get admins (role_id = 1) - admins don't have profile pics, so we'll handle this in the view
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1);

        // Get tutors assigned to this student via paymentstudents table and democlasses with profile pics
        $allTutors = tutorregistration::select(
                'tutorregistrations.id',
                'tutorregistrations.name',
                'tutorregistrations.email',
                'tutorregistrations.mobile',
                'tutorregistrations.role_id',
                'tutorprofiles.profile_pic'
            )
            ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('paymentstudents', function ($join) {
                $join->on('paymentstudents.tutor_id', '=', 'tutorregistrations.id')
                    ->where('paymentstudents.student_id', session('userid')->id);
            })
            ->leftJoin('democlasses', function ($join) {
                $join->on('democlasses.tutor_id', '=', 'tutorregistrations.id')
                    ->where('democlasses.student_id', session('userid')->id);
            })
            ->where(function ($query) {
                $query->whereNotNull('paymentstudents.id')
                    ->orWhereNotNull('democlasses.id');
            })
            ->where('tutorregistrations.role_id', 2)
            ->distinct();

        // $userlists = $admins->union($tutors)->get();
        $userlists = $admins->union($allTutors)->get();

        $this->addOnlineStatusToUserList($userlists);
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
        $this->addOnlineStatusToUserList($userlists);

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
        $this->addOnlineStatusToUserList($userlists);

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
        $this->addOnlineStatusToUserList($userlists);

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
        $this->addOnlineStatusToUserList($userlists);

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
        $header = tutorregistration::select('tutorregistrations.*', 'tutorprofiles.profile_pic')
                ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                ->where('tutorregistrations.id', $id)
                ->first();

        // Return HTML partial instead of JSON
        return view('student.partials.chat-messages', compact('messages', 'studentProfile','header'))->render();
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

        $this->createMessageNotification($message, $senderName);

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Admin messages - main chat page (list of admins, tutors, students)
     */
    public function messagesbyadmin()
    {
        // Other admins (role_id = 1)
        $admins = admin::select('id', 'name', 'email', 'mobile', 'role_id', \DB::raw('NULL as profile_pic'))
                      ->where('id', '!=', session('userid')->id)
                      ->where('role_id', 1)
                      ->get();

        // All tutors (role_id = 2) with profile pic
        $tutors = tutorregistration::select('tutorregistrations.id', 'tutorregistrations.name', 'tutorregistrations.email', 'tutorregistrations.mobile', 'tutorregistrations.role_id', 'tutorprofiles.profile_pic')
                                    ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
                                    ->where('tutorregistrations.id', '!=', session('userid')->id)
                                    ->where('tutorregistrations.role_id', 2)
                                    ->get();

        // All students (role_id = 3) with profile pic
        $students = studentregistration::select('studentregistrations.id', 'studentregistrations.name', 'studentregistrations.email', 'studentregistrations.mobile', 'studentregistrations.role_id', 'studentprofiles.profile_pic')
                                        ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
                                        ->where('studentregistrations.id', '!=', session('userid')->id)
                                        ->where('studentregistrations.role_id', 3)
                                        ->get();

        $userlists = $admins->merge($tutors)->merge($students);
        $this->addOnlineStatusToUserList($userlists);
        $messages = [];
        $header = null;
        $activeTab = 'all';
        // $hasStudents = $userlists->contains('role_id', 3);


        return view('admin.messages', compact('userlists', 'messages', 'header','activeTab'));
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
        $this->addOnlineStatusToUserList($userlists);

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];
        $header = null;
        $activeTab = 'student';

        return view('admin.messages', compact('userlists', 'messages', 'header','activeTab'))->with('info', 'Student messages feature is temporarily unavailable.');
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
        $this->addOnlineStatusToUserList($userlists);

        // Empty messages array for initial view (no conversation selected yet)
        $messages = [];
        $header = null;
        $activeTab = 'tutor';
        return view('admin.messages', compact('userlists', 'messages', 'header','activeTab'))->with('info', 'Tutor messages feature is temporarily unavailable.');
    }

       /**
     * Admin tutor messages by ID - placeholder method
     * When admin message to the specific tutor
     */
    public function messagesbyadmintutormessages($id)
    {
        // Get all tutors for the user list
        $userlists = tutorregistration::select('id', 'name', 'email', 'mobile', 'role_id')
                                      ->where('id', '!=', session('userid')->id)
                                      ->where('role_id', 2) // Get only tutors
                                      ->get();
        $this->addOnlineStatusToUserList($userlists);

        // Get the specific tutor for the header
        $header = tutorregistration::select(
            'tutorregistrations.*',
            'tutorprofiles.profile_pic'
        )
        ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
        ->where('tutorregistrations.id', $id)
        ->first();


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
        $activeTab = null;

        return view('admin.messages', compact('userlists', 'header', 'messages','activeTab'))->with('info', 'Tutor messages feature is temporarily unavailable.');
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
        $this->addOnlineStatusToUserList($userlists);

        // Get the specific student for the header
        $header = studentregistration::select(
            'studentregistrations.*',
            'studentprofiles.profile_pic'
        )
        ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
        ->where('studentregistrations.id', $id)
        ->first();


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
        // dd($messages->toArray());
        $activeTab = null;

        return view('admin.messages', compact('userlists', 'header', 'messages','activeTab'))->with('info', 'Student messages feature is temporarily unavailable.');
    }

    /**
     * Admin student messages load by ID
     * When the tutor click on the specific student this method hit and it load the messages
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

        $this->createMessageNotification($message, $senderName);

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
        $search = $request->searchtext;

        $userlists = studentregistration::select(
                'studentregistrations.id',
                'studentregistrations.name',
                'studentregistrations.email',
                'studentregistrations.mobile',
                'studentregistrations.role_id',
                'studentprofiles.profile_pic'
            )
            ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
            ->where('studentregistrations.id', '!=', session('userid')->id)
            ->where('studentregistrations.role_id', 3)
            ->where(function ($query) use ($search) {
                $query->where('studentregistrations.name', 'LIKE', "%{$search}%")
                    ->orWhere('studentregistrations.email', 'LIKE', "%{$search}%")
                    ->orWhere('studentregistrations.mobile', 'LIKE', "%{$search}%");
            })
            ->get();

        $this->addOnlineStatusToUserList($userlists);

        $messages = [];
        $header = null;
        $activeTab = 'student';

        return view('admin.messages', compact('userlists', 'messages', 'header', 'activeTab'))->with('searchtext', $search);
    }

    /**
     * Admin chat tutor search - placeholder method
     */
    public function chattutorsearch(Request $request)
    {
        $search = $request->searchtext;

        $userlists = tutorregistration::select(
                'tutorregistrations.id',
                'tutorregistrations.name',
                'tutorregistrations.email',
                'tutorregistrations.mobile',
                'tutorregistrations.role_id',
                'tutorprofiles.profile_pic'
            )
            ->leftJoin('tutorprofiles', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.id', '!=', session('userid')->id)
            ->where('tutorregistrations.role_id', 2)
            ->where(function ($query) use ($search) {
                $query->where('tutorregistrations.name', 'LIKE', "%{$search}%")
                    ->orWhere('tutorregistrations.email', 'LIKE', "%{$search}%")
                    ->orWhere('tutorregistrations.mobile', 'LIKE', "%{$search}%");
            })
            ->get();

        $this->addOnlineStatusToUserList($userlists);

        $messages = [];
        $header = null;
        $activeTab = 'tutor';

        return view('admin.messages', compact('userlists', 'messages', 'header', 'activeTab'))->with('searchtext', $search);
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

        // Get students assigned to this tutor via paymentstudents table and the demo class table with profile pics
        $allStudents = studentregistration::select(
            'studentregistrations.id',
            'studentregistrations.name',
            'studentregistrations.email',
            'studentregistrations.mobile',
            'studentregistrations.role_id',
            'studentprofiles.profile_pic'
        )
        ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')
        ->leftJoin('paymentstudents', function ($join) {
            $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
                ->where('paymentstudents.tutor_id', session('userid')->id);
        })
        ->leftJoin('democlasses', function ($join) {
            $join->on('democlasses.student_id', '=', 'studentregistrations.id')
                ->where('democlasses.tutor_id', session('userid')->id);
        })
        ->where(function ($query) {
            $query->whereNotNull('paymentstudents.id')
                ->orWhereNotNull('democlasses.id');
        })
        ->where('studentregistrations.role_id', 3)
        ->where('studentregistrations.id', '!=', session('userid')->id)
        ->distinct();


        $userlists = $admins->union($allStudents)->get();
        $this->addOnlineStatusToUserList($userlists);

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
        $this->addOnlineStatusToUserList($userlists);

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
        $this->addOnlineStatusToUserList($userlists);

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
        $userlists = studentregistration::select(
                'studentregistrations.id',
                'studentregistrations.name',
                'studentregistrations.email',
                'studentregistrations.mobile',
                'studentregistrations.role_id',
                'studentprofiles.profile_pic'
            )
            ->leftJoin('studentprofiles', 'studentregistrations.id', '=', 'studentprofiles.student_id')

            ->leftJoin('paymentstudents', function ($join) {
                $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
                    ->where('paymentstudents.tutor_id', session('userid')->id);
            })

            ->leftJoin('democlasses', function ($join) {
                $join->on('democlasses.student_id', '=', 'studentregistrations.id')
                    ->where('democlasses.tutor_id', session('userid')->id);
            })

            ->where(function ($query) {
                $query->whereNotNull('paymentstudents.id')
                    ->orWhereNotNull('democlasses.id');
            })

            ->where('studentregistrations.id', '!=', session('userid')->id)
            ->where('studentregistrations.role_id', 3)
            ->distinct()
            ->get();

        $this->addOnlineStatusToUserList($userlists);

        $tutorProfile = tutorprofile::where('tutor_id', session('userid')->id)->first();

        $messages = [];

        return view('tutor.messages', compact('userlists', 'tutorProfile', 'messages'))
            ->with('info', 'Student messages feature is temporarily unavailable.');
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
        $this->addOnlineStatusToUserList($userlists);

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

        $this->createMessageNotification($message, $senderName);

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Create and persist a notification for a sent message (alert_type 12).
     * Triggered when tutor, student, or admin sends a message to the recipient.
     */
    protected function createMessageNotification(ChMessage $message, string $senderName): void
    {
        try {
            $notificationdata = new Notification();
            $notificationdata->alert_type = 12;
            $notificationdata->notification = 'New message from ' . $senderName;
            $notificationdata->initiator_id = $message->from_id;
            $notificationdata->initiator_role = $message->from_role_id;
            $notificationdata->event_id = $message->id;
            $notificationdata->read_status = 0;

            $toId = $message->to_id;
            $toRoleId = (int) $message->to_role_id;

            $notificationdata->show_to_admin = 0;
            $notificationdata->show_to_admin_id = 0;
            $notificationdata->show_to_all_admin = 0;
            $notificationdata->show_to_tutor = 0;
            $notificationdata->show_to_tutor_id = 0;
            $notificationdata->show_to_student = 0;
            $notificationdata->show_to_student_id = 0;

            if ($toRoleId === 1) {
                $notificationdata->show_to_admin = 1;
                $notificationdata->show_to_admin_id = $toId;
                $notificationdata->show_to_all_admin = 0;
            } elseif ($toRoleId === 2) {
                $notificationdata->show_to_tutor = 1;
                $notificationdata->show_to_tutor_id = $toId;
            } elseif ($toRoleId === 3) {
                $notificationdata->show_to_student = 1;
                $notificationdata->show_to_student_id = $toId;
            }

            $notificationdata->save();
        } catch (\Exception $e) {
            \Log::error('Failed to create message notification: ' . $e->getMessage());
        }
    }
}
