<?php

namespace App\Http\Controllers;
use App\Models\SlotBooking;
use App\Models\subjects;
use App\Models\status;
use App\Models\studentregistration;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;
use App\Events\RealTimeMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\classes;

class SlotBookingController extends Controller
{
    public function tutorslots(){

    $slots = SlotBooking::select('slot_bookings.*', 'studentprofiles.name as student_name', 'subjects.name as subject')
    ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'slot_bookings.student_id')
    ->leftJoin('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
    ->where('slot_bookings.tutor_id', session('userid')->id)
    ->where(function($query) {
        // Convert today's date to UTC for comparison
        $query->whereDate('slot_bookings.date', '>=', Carbon::now('UTC')->toDateString())
              ->orWhere(function($query) {
                  $query->whereDate('slot_bookings.date', '=', Carbon::now('UTC')->toDateString())
                        ->whereTime('slot_bookings.date', '>=', Carbon::now('UTC')->toTimeString());
              });
    })
    ->orderby('slot_bookings.date','asc')
    ->get();

        $subjects = subjects::select('subjects.*')
        ->join('tutorsubjectmappings','tutorsubjectmappings.subject_id','subjects.id')
        ->where('tutorsubjectmappings.tutor_id',session('userid')->id)
        ->where('subjects.is_active',1)
        ->get();

        $students = studentregistration::select('studentregistrations.*')
    ->leftJoin('paymentstudents', function($join) {
        $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
            ->where('paymentstudents.tutor_id', '=', session('userid')->id);
    })
    ->whereNotNull('paymentstudents.student_id')
    ->distinct()
    ->where('studentregistrations.is_active',1)
    ->get();

        // Convert slot dates and times from UTC to user timezone for display
        $slots->each(function ($slot) {
            $slot->date = TimezoneHelper::dateToUserTz($slot->date);
            $slot->slot = TimezoneHelper::timeToUserTz($slot->slot);
            if ($slot->booked_at) {
                $slot->booked_at = TimezoneHelper::toUserTz($slot->booked_at, 'UTC');
            }
        });

        // dd($students);
        return view('tutor.slotcreate',compact('slots','subjects','students'));
    }

    public function slotscreate(Request $request)
{
    $request->validate([
        'classdate' => 'required',
        'classtime' => 'required',
    ]);

    // Parse the time from the request in the tutor's timezone
    $requestedDateTime = Carbon::parse($request->classdate . ' ' . $request->classtime, TimezoneHelper::userTimezone());
    
    // Convert to UTC for storage
    $utcDateTime = $requestedDateTime->setTimezone('UTC');

    // Check for existing slots within the 1-hour gap (check in UTC)
    $existingSlots = SlotBooking::where('tutor_id', session('userid')->id)
        ->where('date', $utcDateTime->format('Y-m-d'))
        ->whereBetween('slot', [
            $utcDateTime->copy()->subHour()->format('H:i:s'),
            $utcDateTime->copy()->addHour()->format('H:i:s'),
        ])
        ->get();

    if ($existingSlots->isEmpty()) {
        // No conflicting slots, proceed to create the new slot
        $sucsmsg = ($request->slotid) ? 'Slot updated successfully!' : 'Slot created successfully!';
        $ermsg = ($request->slotid) ? 'Slot updation failed!' : 'Slot creation failed!';

        // Create or update the slot for the requested day
        $slotbooking = ($request->slotid) ? SlotBooking::find($request->slotid) : new SlotBooking();
        // Store date and slot in UTC
        $slotbooking->date = $utcDateTime->format('Y-m-d');
        $slotbooking->slot = $utcDateTime->format('H:i:s');
        $slotbooking->status = 0;
        $slotbooking->tutor_id = session('userid')->id;
        $res = $slotbooking->save();

        if ($res) {
            // Replicate the same slots based on the radio button selection
            $repeatOption = $request->input('repeatOption');

            switch ($repeatOption) {
                case 'forday':
                    // Do nothing, as it's already created for one day
                    break;

                case 'forweek':
                case 'formonth':
                    $conflictMessage = $this->replicateSlotsForPeriod($requestedDateTime, ($repeatOption == 'forweek') ? 7 : 30);
                    if ($conflictMessage) {
                        return back()->with('success', $sucsmsg)->with('conflict', $conflictMessage);
                    }
                    break;
            }

            return back()->with('success', $sucsmsg);
        } else {
            return back()->with('fail', $ermsg);
        }
    } else {
        // Conflicting slots found
        $conflictingTimes = $existingSlots->pluck('slot')->map(function ($time) {
            return TimezoneHelper::formatInUserTz($time, 'h:i A', 'UTC');
        })->implode(', ');

        return back()->with('fail', 'Slot creation failed. There is a conflicting slot within the 1-hour gap. Conflicting times: ' . $conflictingTimes);
    }
}
public function reschedule(Request $request){
    $getdata = SlotBooking::where('id',$request->currentslotid)->first();

    $updatedata = SlotBooking::find($request->changedslottime);
    $updatedata->status = $getdata->status;
    $updatedata->student_id = $getdata->student_id;
    $updatedata->booked_at = Carbon::now('UTC');
    $updatedata->transaction_id = $getdata->transaction_id;
    $updatedata->class_schedule_id = $getdata->class_schedule_id;
    $updatedata->contact_admin = $getdata->contact_admin;
    $updatedata->remarks = $getdata->remarks;
    $updatedata->save();

    $cleardata = SlotBooking::find($request->currentslotid);
    $cleardata->status = 0;
    $cleardata->student_id = NULL;
    $cleardata->booked_at = NULL;
    $cleardata->transaction_id = NULL;
    $cleardata->class_schedule_id = NULL;
    $cleardata->contact_admin = 0;
    $cleardata->remarks = NULL;
    $cleardata->save();

    //////////////// Here I need to pass notification into db
    $notificationdata = new Notification();
    $notificationdata->alert_type = 7;
    $notificationdata->notification = "You slots has been modified";
    $notificationdata->initiator_id = session('userid')->id;
    $notificationdata->initiator_role = session('userid')->role_id;
    $notificationdata->event_id = $request->currentslotid;
    // Sending to admin
    // if($request->receiver_role_id == 1){
    //     $notificationdata->show_to_admin = 1;
    //     $notificationdata->show_to_admin_id = $request->receiver_id;
    //     // $notificationdata->show_to_all_admin = 1;
    // }
    // // Sending to tutor
    // if($request->receiver_role_id == 2){
    //     $notificationdata->show_to_tutor = 1;
    //     $notificationdata->show_to_tutor_id = $request->receiver_id;
    //     // $notificationdata->show_to_all_tutor = 0;
    // }
    // Sending to student
    // if($request->receiver_role_id == 3){
        $notificationdata->show_to_student = 1;
        $notificationdata->show_to_student_id = $getdata->student_id;
        // $notificationdata->show_to_all_student = 0;
    // }
    // Sending to parent
    // if($request->receiver_role_id == 3){
    //     $notificationdata->show_to_parent = 1;
    //     $notificationdata->show_to_parent_id = $request->receiver_id;
    //     // $notificationdata->show_to_all_parent = 0;
    // }
    $notificationdata->read_status = 0;

    $notified = $notificationdata->save();
    broadcast(new RealTimeMessage('$notification'));

    return back()->with('success', 'Slot changed successfully!');
}

private function replicateSlotsForPeriod($sourceDateTime, $days)
{
    $conflictMessage = null;

    for ($dayOffset = 1; $dayOffset <= $days; $dayOffset++) {
        // Get current date in UTC (since sourceDateTime is already in UTC)
        $currentDate = Carbon::now('UTC')->addDays($dayOffset)->toDateString();

        // Check for existing slots within the 1-hour gap for the target day
        $existingSlots = SlotBooking::where('tutor_id', session('userid')->id)
            ->where('date', $currentDate)
            ->whereBetween('slot', [
                $sourceDateTime->copy()->subHour()->format('H:i:s'),
                $sourceDateTime->copy()->addHour()->format('H:i:s'),
            ])
            ->get();

        if ($existingSlots->isNotEmpty()) {
            // Conflicting slots found for the current day
            $conflictingTimes = $existingSlots->pluck('slot')->map(function ($time) {
                return TimezoneHelper::formatInUserTz($time, 'h:i A', 'UTC');
            })->implode(', ');

            $conflictMessage = ($conflictMessage) ?
                $conflictMessage . "<br>Conflict on $currentDate: $conflictingTimes" :
                "Conflict on $currentDate: $conflictingTimes";
        } else {
            // No conflicting slots, proceed to replicate the slot
            $slotbooking = new SlotBooking();
            // Store in UTC
            $slotbooking->date = $currentDate;
            $slotbooking->slot = $sourceDateTime->format('H:i:s');
            $slotbooking->status = 0;
            $slotbooking->tutor_id = session('userid')->id;
            $slotbooking->save();
        }
    }

    return $conflictMessage;
}


    public function slotsdelete(Request $request)
{
    $request->validate([
        'slotdeleteid' => 'required',
    ]);

    // Assuming SlotBooking is the model associated with the slot_bookings table
    $slotbooking = SlotBooking::where('tutor_id', session('userid')->id)
        ->where('id', $request->slotdeleteid)
        ->first();

    if ($slotbooking) {
        // Found the slot, now delete it
        $slotbooking->delete();

        return back()->with('success', 'Slot deleted successfully!');
    } else {
        // Slot not found
        return back()->with('fail', 'Slot not found or you do not have permission to delete it.');
    }

}

public function tutorslotsearch(Request $request) {
    $searchDate = $request->searchDate;
    $subject = $request->selectsubject;
    $student = $request->selectstudent;
    $bookingStatus = $request->bookingstatus;
    $classStatus = $request->classstatus;
    // dd($request->all());
    // Assuming 'date' is the column in your 'slot_bookings' table where the date is stored
    $slotsQuery = SlotBooking::select('slot_bookings.*', 'studentprofiles.name as student_name', 'subjects.name as subject','zoom_classes.meeting_id as meeting_id')
    ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'slot_bookings.student_id')
    ->leftJoin('zoom_classes', 'zoom_classes.id', '=', 'slot_bookings.meeting_id')
    ->leftJoin('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
    ->where('slot_bookings.tutor_id', session('userid')->id);

    if($student){
        $slotsQuery->where('slot_bookings.student_id',$student);
    }

    if($subject){
        $slotsQuery->where('slot_bookings.subject_id',$subject);
    }

    if ($searchDate) {
        // Convert PKT date to UTC for database query
        $utcDate = Carbon::createFromFormat('Y-m-d', $searchDate, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
        $slotsQuery->where('slot_bookings.date', $utcDate); // Filter by the selected date if available
    }
    if($bookingStatus){

        if($bookingStatus == 2){
            $bookingStatus = 0;
        }
       $slotsQuery->where('status',$bookingStatus);
    }
    if($classStatus){

        // if($bookingStatus == 2){
        //     $bookingStatus = 0;
        // }
       $slotsQuery->where('is_class_scheduled',$classStatus);
    }

    $slots = $slotsQuery->get();

    // Convert slot dates and times from UTC to user timezone for display
    $slots->each(function ($slot) {
        $slot->date = TimezoneHelper::dateToUserTz($slot->date);
        $slot->slot = TimezoneHelper::timeToUserTz($slot->slot);
        if ($slot->booked_at) {
            $slot->booked_at = TimezoneHelper::toUserTz($slot->booked_at, 'UTC');
        }
    });

    $subjects = subjects::select('subjects.*',)
    ->join('tutorsubjectmappings','tutorsubjectmappings.subject_id','subjects.id')
    ->where('tutorsubjectmappings.tutor_id',session('userid')->id)
    ->where('subjects.is_active',1)
    ->get();

    $students = studentregistration::select('studentregistrations.*')
    ->leftJoin('paymentstudents', function($join) {
        $join->on('paymentstudents.student_id', '=', 'studentregistrations.id')
            ->where('paymentstudents.tutor_id', '=', session('userid')->id);
    })
    ->whereNotNull('paymentstudents.student_id')
    ->distinct()
    ->where('studentregistrations.is_active',1)
    ->get();

    return view('tutor.slotcreate', compact('slots','subjects','students'));
}

public function indexslotsearch(Request $request) {
    // Get the selected date from the request
    $selectedDate = $request->input('date');
    $tutorid = $request->input('tutorid');

    // Convert PKT date to UTC for database query
    $utcDate = Carbon::createFromFormat('Y-m-d', $selectedDate, 'Asia/Karachi')->setTimezone('UTC')->toDateString();

    // Assuming 'date' is the column in your 'slot_bookings' table where the date is stored
    $slots = SlotBooking::select('*')
    ->where('slot_bookings.tutor_id', $tutorid)
        ->where('slot_bookings.date', $utcDate) // Assuming there is a 'status' column for the slot status
        ->get();

    // Convert slot dates and times from UTC to user timezone for display
    $slots->each(function ($slot) {
        $slot->date = TimezoneHelper::dateToUserTz($slot->date);
        $slot->slot = TimezoneHelper::timeToUserTz($slot->slot);
    });

    // Return the filtered slots as JSON
    return response()->json($slots);
}

function slotsupdate(Request $request){
    // dd($request->all());
    $request->validate([
        'markactive' => 'required',
        // 'classtime' => 'required',
    ]);

    $requestedDateTime = Carbon::parse($request->classdate . ' ' . $request->classtime);

    // // Check for existing slots within the 1-hour gap
    // $existingSlots = SlotBooking::where('tutor_id', session('userid')->id)
    //     ->where('date', $requestedDateTime->format('Y-m-d'))
    //     ->whereBetween('slot', [
    //         $requestedDateTime->subHour()->format('H:i:s'),
    //         $requestedDateTime->addHour()->format('H:i:s'),
    //     ])
    //     ->get();

    // if ($existingSlots->isEmpty()) {
    //     // No conflicting slots, proceed to create the new slot
        // if ($request->slotid) {
            $slotbooking = SlotBooking::find($request->slotid);
            $sucsmsg = 'Slot updated successfully!';
            $ermsg = 'Slot updation failed!';
        // } else {
        //     $slotbooking = new SlotBooking();
        //     $sucsmsg = 'Slot created successfully!';
        //     $ermsg = 'Slot creation failed!';
        // }

        // $slotbooking->date = $requestedDateTime->format('Y-m-d');
        // $slotbooking->slot = $requestedDateTime->format('H:i:s');
        $slotbooking->status = 0;
        // $slotbooking->tutor_id = session('userid')->id;
        $slotbooking->student_id = NULL;

        $res = $slotbooking->save();

        if ($res) {
            return back()->with('success', $sucsmsg);
        } else {
            return back()->with('fail', $sucsmsg);
        }
    // } else {
    //     // Conflicting slots found
    //     $conflictingTimes = $existingSlots->pluck('slot')->map(function ($time) {
    //         return Carbon::parse($time)->format('h:i A');
    //     })->implode(', ');

    //     return back()->with('fail', 'Slot creation failed. There is a conflicting slot within the 1-hour gap. Conflicting times: ' . $conflictingTimes);
    // }
}

public function admintutorslots(){

    $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::select('*')->get();
        $slots = SlotBooking::select('slot_bookings.*','studentprofiles.name as student_name','subjects.name as subject','tutorregistrations.name as tutor_name')
        ->leftJoin('studentprofiles','studentprofiles.student_id','=','slot_bookings.student_id')
        ->leftJoin('subjects','subjects.id','=','slot_bookings.subject_id')
        ->leftJoin('tutorregistrations','tutorregistrations.id','slot_bookings.tutor_id')
        ->orderby('slot_bookings.created_at','desc')
        ->whereDate('slot_bookings.date', '>=', Carbon::now('UTC')->toDateString())
        ->paginate(100);
    
    // Convert slot dates and times from UTC to user timezone for display (admin: default Asia/Karachi)
    $slots->getCollection()->each(function ($slot) {
        $slot->date = TimezoneHelper::dateToUserTz($slot->date);
        $slot->slot = TimezoneHelper::timeToUserTz($slot->slot);
        if ($slot->booked_at) {
            $slot->booked_at = TimezoneHelper::toUserTz($slot->booked_at, 'UTC');
        }
    });

    return view('admin.tutorslotslist', get_defined_vars());
}

public function admintutorslotssearch(Request $request){

    $subjects = subjects::where('is_active',1)->get();
        $classes = classes::where('is_active',1)->get();
        $statuses = status::select('*')->get();
        $query = SlotBooking::select('slot_bookings.*','studentprofiles.name as student_name','subjects.name as subject','tutorregistrations.name as tutor_name')
        ->leftJoin('studentprofiles','studentprofiles.student_id','=','slot_bookings.student_id')
        ->leftJoin('subjects','subjects.id','=','slot_bookings.subject_id')
        ->leftJoin('tutorregistrations','tutorregistrations.id','slot_bookings.tutor_id');
        // ->whereDate('slot_bookings.date', '>=', now()->toDateString())

        if ($request->student_name) {
            $query->where('studentprofiles.name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->student_mobile) {
            $query->where('studentprofiles.mobile', 'like', '%' . $request->student_mobile . '%');
        }

        if ($request->tutor_name) {
            $query->where('tutorregistrations.name', 'like', '%' . $request->tutor_name . '%');
        }

        if ($request->tutor_mobile) {
            $query->where('tutorregistrations.mobile', 'like', '%' . $request->tutor_mobile . '%');
        }

        if ($request->start_date && $request->end_date) {
            // Convert PKT dates to UTC for database query
            $utcStartDate = Carbon::createFromFormat('Y-m-d', $request->start_date, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
            $utcEndDate = Carbon::createFromFormat('Y-m-d', $request->end_date, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
            $query->whereBetween('slot_bookings.date', [$utcStartDate, $utcEndDate]);
        } elseif ($request->start_date) {
            $utcStartDate = Carbon::createFromFormat('Y-m-d', $request->start_date, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
            $query->where('slot_bookings.date', '>=', $utcStartDate);
        } elseif ($request->end_date) {
            $utcEndDate = Carbon::createFromFormat('Y-m-d', $request->end_date, 'Asia/Karachi')->setTimezone('UTC')->toDateString();
            $query->where('slot_bookings.date', '<=', $utcEndDate);
        }


        if ($request->status) {
            $query->where('slot_bookings.status', '=', $request->status);
        }



            $slots=  $query->get();
            
            // Convert slot dates and times from UTC to user timezone for display
            $slots->each(function ($slot) {
                $slot->date = TimezoneHelper::dateToUserTz($slot->date);
                $slot->slot = TimezoneHelper::timeToUserTz($slot->slot);
                if ($slot->booked_at) {
                    $slot->booked_at = TimezoneHelper::toUserTz($slot->booked_at, 'UTC');
                }
            });
            // ->get();
        // ->get();
    return view('admin.tutorslotslist', get_defined_vars());
}

}
