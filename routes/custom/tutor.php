<?php
use App\Http\Controllers\tutor\TutorDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\tutor\TutorProfileController;
use App\Http\Controllers\admin\DemoController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\admin\BatchesController;
use App\Http\Controllers\admin\ClassController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\tutor\LearningContentsController;
use App\Http\Controllers\ZoomClassesController;
use App\Http\Controllers\SlotBookingController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\admin\QuestionBankController;
use App\Http\Controllers\admin\OnlineTestController;


//************************************************ Tutor Routes ************************************************
Route::group(['prefix' => 'tutor', 'middleware' => ['TutorAuthenticate']], function () {

    // Tutor Dashboard
    Route::get('dashboard', [TutorDashboardController::class, 'index'])->name('tutor.dashboard');
    Route::get('notifications', [TutorDashboardController::class, 'notificationslist'])->name('tutor.notifications');
    Route::get('dashboard/oauth2callback', [TutorDashboardController::class, 'index'])->name('tutor.dashboard.oauth2callback');
    // Tutor Profile
    Route::get('profile', [TutorProfileController::class, 'tutorprofile'])->name('tutor.profile');
    Route::get('profileupdate', [TutorProfileController::class, 'edit'])->name('tutor.profileupdate');
    Route::post('updateprofiledata', [TutorProfileController::class, 'updateprofiledata'])->name('tutor.updateprofiledata');
    Route::post('update-skills', [TutorProfileController::class, 'updateSkills'])->name('tutor.update-skills');
    // Tutor Achievement Mapping
    Route::post('tutoracadd', [TutorProfileController::class, 'tutoracadd'])->name('tutor.tutoracadd');
    Route::get('tutoracdel/{id}', [TutorProfileController::class, 'tutoracdel'])->name('tutor.tutoracdel');
    // Tutor Class Mapping
    Route::post('classmapping', [TutorProfileController::class, 'classmapping'])->name('tutor.classmapping');
    Route::get('classmappingdelete/{id}', [TutorProfileController::class, 'classmappingdelete'])->name('tutor.classmappingdelete');

    // Demo List
    Route::get('demolist', [DemoController::class, 'tutordemolist'])->name('tutor.demolist');
    // Route::post('demolist', [DemoController::class, 'tutordemoupdate'])->name('tutor.demo.update');
    Route::get('demodetails/{id}', [DemoController::class, 'demodetails'])->name('tutor.demodetails');
    Route::post('demolist-search', [DemoController::class, 'tutorDemolistsearch'])->name('tutor.demolist-search');
    Route::post('demo/confirm', [GoogleCalendarController::class, 'democonfirm'])->name('tutor.demo.confirm');
    Route::post('demo/end', [GoogleCalendarController::class, 'demoend'])->name('tutor.demo.end');
    Route::post('demo/update', [DemoController::class, 'demoupdate'])->name('tutor.demo.update');
    Route::get('demo/status/update', [DemoController::class, 'demostatusupdate'])->name('tutor.demo.status.update');
    // Tutor Batches
    Route::get('batches', [BatchesController::class, 'tutorbatches'])->name('tutor.batches');
    Route::get('batches/students/{id}', [BatchesController::class, 'tutorbatchesstudents'])->name('tutor.batches.students');
    Route::any('batches/attendance/{id}', [BatchesController::class, 'tutorbatchesattendance'])->name('tutor.batches.attendance');
    Route::post('batches/update-attendance', [BatchesController::class, 'tutorBatcheUpdateattendance'])->name('tutor.batches.update-attendance');
    Route::post('batches/update-recording', [BatchesController::class, 'updaterecording'])->name('tutor.batches.update-recording');
    // Student Lists(Enrolled)
    Route::get('students', [BatchesController::class, 'tstudentlists'])->name('tutor.studentslist');


    // Tutor Classes
    Route::get('classes', [ClassController::class, 'tutorclasses'])->name('tutor.classes');
    // Tutor attendances
    Route::get('attendance', [ClassController::class, 'tutorattendance'])->name('tutor.attendance');
    Route::post('attendance-search', [ClassController::class, 'tutorattendanceSearch'])->name('tutor.attendance-search');
    // Tutor Assignments
    Route::get('assignments', [AssignmentsController::class, 'tutorassignments'])->name('tutor.assignments');
    Route::post('assignments', [AssignmentsController::class, 'tutorassignmentscreate'])->name('tutor.assignments.create');
    Route::get('assignments/{id}', [AssignmentsController::class, 'tutorview'])->name('tutor.assignments.view');
    Route::post('assignments/update-marks-remarks', [AssignmentsController::class, 'updateMarksRemarks'])->name('tutor.assignments.updateMarksRemarks');
    // Tutor Learning Contents
    Route::get('learningcontents', [LearningContentsController::class, 'index'])->name('tutor.learningcontents');
    Route::get('learningcontents/add', [LearningContentsController::class, 'add'])->name('tutor.learningcontents.add');
    Route::post('learningcontents/create', [LearningContentsController::class, 'store'])->name('tutor.learningcontents.create');
    Route::get('learningcontents/status', [LearningContentsController::class, 'status'])->name('tutor.learningcontents.status');
    Route::get('learningcontents/{id}', [LearningContentsController::class, 'edit'])->name('tutor.learningcontents.edit');
    Route::post('learningcontents-search', [LearningContentsController::class, 'search'])->name('tutor.learningcontents.search');
    // Live Classes(GMeet Meeting)
    Route::get('liveclass', [ZoomClassesController::class, 'index'])->name('tutor.liveclass');
    Route::any('liveclass/completed/{id}', [ZoomClassesController::class, 'completed'])->name('tutor.liveclass.completed');
    Route::get('liveclass/create', [ZoomClassesController::class, 'create'])->name('tutor.liveclass.create');
    Route::post('liveclass/store', [ZoomClassesController::class, 'store'])->name('tutor.liveclass.store');
    Route::get('getuser', [ZoomClassesController::class, 'getzoomuser'])->name('tutor.liveclass.getuser');
    Route::get('getclasslist', [GoogleCalendarController::class, 'classlist'])->name('tutor.liveclass.classlist');
    Route::get('liveclass/status/update',[ZoomClassesController::class,'liveclassstatusupdate'])->name('tutor.liveclass.status.update');
    // tutor Slot Creation
    Route::get('tutorslots', [SlotBookingController::class, 'tutorslots'])->name('tutor.tutorslots');
    Route::post('tutorslots', [SlotBookingController::class, 'slotscreate'])->name('tutor.slots.create');
    Route::post('reschedule', [SlotBookingController::class, 'reschedule'])->name('tutor.slots.reschedule');
    Route::post('tutorslotsdelete', [SlotBookingController::class, 'slotsdelete'])->name('tutor.slots.delete');
    Route::get('tutor/tutorslotsearch', [SlotBookingController::class, 'tutorslotsearch'])->name('tutor.slots.search');
    // Route::get('getclass-bkp', [ZoomClassesController::class, 'classlist'])->name('tutor.liveclass.classlist');
    Route::get('getclass', [GoogleCalendarController::class, 'classlist'])->name('tutor.meet.classlist');
    // Route::post('classschedule-bkp', [ZoomClassesController::class, 'scheduleclass'])->name('tutor.liveclass.scheduleclass-bkp');
    Route::any('classschedule', [GoogleCalendarController::class, 'scheduleclass'])->name('tutor.liveclass.scheduleclass');
    // Feedback by tutor
    Route::get('feedback', [FeedbackController::class, 'index'])->name('tutor.feedback.list');
    Route::post('tutorfeedback-student', [FeedbackController::class, 'tutorsubmitstudentreview'])->name('tutor.feedback.student');
    // Message By Tutor
    Route::get('messages', [MessagesController::class, 'messagesbytutor'])->name('tutor.messages');
    Route::get('adminmessages', [MessagesController::class, 'messagesbytutoradmins'])->name('tutor.messages.admins');
    Route::get('adminmessages/{id}', [MessagesController::class, 'messagesbytutoradminmessages'])->name('tutor.messages.adminmessages');
    Route::get('adminmessagesload/{id}', [MessagesController::class, 'messagesbytutoradminmessagesload'])->name('tutor.messages.adminmessagesload');
    Route::get('studentmessages', [MessagesController::class, 'messagesbytutorstudents'])->name('tutor.messages.students');
    Route::get('studentmessages/{id}', [MessagesController::class, 'messagesbytutorstudentmessages'])->name('tutor.messages.studentmessages');
    Route::get('studentmessagesload/{id}', [MessagesController::class, 'messagesbytutorstudentmessagesload'])->name('tutor.messages.studentmessagesload');
    Route::post('sendmessage', [MessagesController::class, 'messagesentbytutor'])->name('tutor.messages.send');
    Route::get('chat-presence-status', [MessagesController::class, 'chatPresenceStatusTutor'])->name('tutor.chat.presence.status');
    Route::post('chat-presence-auth', [MessagesController::class, 'chatPresenceAuth'])->name('tutor.chat.presence.auth');
   //payments
   Route::get('payments', [PaymentsController::class, 'tutorStudentPayments'])->name('tutor.payments');
   Route::any('paymentsearch', [PaymentsController::class, 'paymentSearchTutor'])->name('tutor.paymentsearch');
   Route::post('payment-update', [PaymentsController::class, 'update'])->name('tutor.payments.update');
   // Payouts
   Route::get('payouts',[PaymentsController::class,'tutorpayouts'])->name('tutor.payouts');
   Route::post('payouts-search',[PaymentsController::class,'tutorpayoutsSearch'])->name('tutor.payouts-search');

    // learning Contents
   Route::get('questionbank', [QuestionBankController::class, 'tutorQuestionbank'])->name('tutor.questionbank');
   Route::get('questionbank/create', [QuestionBankController::class, 'tutorcreate'])->name('tutor.questionbank.create');
   Route::post('questionbank/store', [QuestionBankController::class, 'tutorstore'])->name('tutor.questionbank.store');
   Route::get('questionbank/subjective/create', [QuestionBankController::class, 'tutor_subjective_create'])->name('tutor.questionbank.subjective.create');
   Route::get('question/status', [QuestionBankController::class, 'status'])->name('tutor.question.status');
   Route::get('questionupdate/{id}', [QuestionBankController::class, 'tutorview'])->name('tutor.questionupdate.view');
   Route::post('questionbank/subjective-store', [QuestionBankController::class, 'storeSubjective'])->name('tutor.questionbank.subjective.store');

    // Online tests
    Route::get('onlinetestlist', [OnlineTestController::class, 'tutorindex'])->name('tutor.onlinetests');
    Route::get('onlinetests', [OnlineTestController::class, 'tutorcreate'])->name('tutor.onlinetests.create');
    Route::post('onlinetests', [OnlineTestController::class, 'tutorstore'])->name('tutor.onlinetests.store');
    Route::get('onlinetests/{id}', [OnlineTestController::class, 'tutoredit'])->name('tutor.onlinetests.edit');
    Route::get('onlinetestquestions/{id}', [OnlineTestController::class, 'tutorviewquestions'])->name('tutor.onlinetestquestions.viewquestions');
    Route::post('onlinetestlist-search', [OnlineTestController::class, 'tutoronlinetestSearch'])->name('tutor.onlinetests-search');
    Route::post('fetchquestions', [OnlineTestController::class, 'tutorfetchquestions'])->name('tutor.fetchquestions');
    Route::get('questions/selector', [OnlineTestController::class, 'getQuestionsForSelector'])->name('tutor.questions.selector');
    Route::post('questions/details', [OnlineTestController::class, 'getQuestionDetails'])->name('tutor.questions.details');
    Route::post('questions/quick-create', [OnlineTestController::class, 'quickCreateQuestion'])->name('tutor.questions.quick-create');
    Route::get('onlinetest/status', [OnlineTestController::class, 'tutorstatus'])->name('tutor.onlinetest.status');
    Route::get('onlinetest/assign/status', [OnlineTestController::class, 'assignteststatus'])->name('tutor.onlinetestassign.status');
    Route::get('assigntest/{id}', [OnlineTestController::class, 'assigntest'])->name('tutor.assigntest');
    Route::post('assigntestdata', [OnlineTestController::class, 'assigntestdata'])->name('tutor.assigntestdata');
    Route::post('assigntestdelete', [OnlineTestController::class, 'assigntestdelete'])->name('tutor.assigntest.delete');
    // tutor subjective responses
    Route::get('onlinetestresponseslist', [OnlineTestController::class, 'onlinetestresponseslistTutor'])->name('tutor.onlinetests.responses.list');
    Route::get('onlinetests/responses/{id}', [OnlineTestController::class, 'onlinetestresponseTutor'])->name('tutor.onlinetests.responses');
    Route::get('onlinetests/responses/student/{id}', [OnlineTestController::class, 'onlinetestresponsestudentTutor'])->name('tutor.onlinetests.responses.student');
    Route::post('onlinetests/responses/correction/{response_id}', [OnlineTestController::class, 'testCorrection'])->name('tutor.onlinetests.responses.correction');
    Route::post('subjectiveTests/responses/search', [OnlineTestController::class, 'subjTestsSearch'])->name('tutor.subjectiveTests-search');
    Route::post('studentwise/subjectiveResponses/{id}', [OnlineTestController::class, 'studentwiseSubjSearch'])->name('tutor.studentwise.subjectiveResponses');
    // Student Test Wise Report
    Route::get('exam/report/{id}', [OnlineTestController::class, 'tutortestreport'])->name('tutor.test.report');

});