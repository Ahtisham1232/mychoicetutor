<?php
namespace App\Http\Controllers;
use App\Models\ChMessage;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    // public function admin_class_report(){
    //     return view('admin.class-report');
    // }
    public function  admin_chat_report(){
        // User role : 1 = Admin | 2 = Tutor | 3 = Student | 4 = Parent |
        $chatlist = ChMessage::select('ch_messages.*', 'tutors.name as tutor_name', 'students.name as student_name')
        ->leftJoin('tutorregistrations as tutors', function($join) {
            $join->on('ch_messages.from_id', '=', 'tutors.id')
                ->where('ch_messages.from_role_id', '=', 2);
            $join->orOn('ch_messages.to_id', '=', 'tutors.id')
                ->where('ch_messages.to_role_id', '=', 2);
        })
        ->leftJoin('studentregistrations as students', function($join) {
            $join->on('ch_messages.from_id', '=', 'students.id')
                ->where('ch_messages.from_role_id', '=', 3);
            $join->orOn('ch_messages.to_id', '=', 'students.id')
                ->where('ch_messages.to_role_id', '=', 3);
        })
        ->where(function($query) {
            $query->where('ch_messages.from_role_id', 2)
                  ->orWhere('ch_messages.from_role_id', 3);
        })
        ->where(function($query) {
            $query->where('ch_messages.to_role_id', 2)
                  ->orWhere('ch_messages.to_role_id', 3);
        })
        ->get()
        ->filter(function($message) {
            return ($message->from_role_id == 2 && $message->to_role_id == 3) || ($message->from_role_id == 3 && $message->to_role_id == 2);
        })
        ->groupBy(function($message) {
            return $message->from_role_id == 2 ? $message->from_id : $message->to_id;
        });

dd($chatlist);
        // return view('admin.chat-report');
    }
}
