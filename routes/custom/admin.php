<?php
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\ClassController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlotBookingController;
use App\Http\Controllers\admin\RecordingsController;
use App\Http\Controllers\admin\SubjectController;
use App\Http\Controllers\admin\TopicController;
use App\Http\Controllers\admin\BatchesController;
use App\Http\Controllers\admin\DemoController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\student\StudentProfileController;
use App\Http\Controllers\student\TutorSearchController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\admin\LearningsContentsController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\admin\QuestionBankController;
use App\Http\Controllers\admin\OnlineTestController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\admin\BlogsController;
use App\Http\Controllers\admin\FaqsController;
use App\Http\Controllers\admin\ContactMessageController;


//************************************************ Admin Authenticate Routes ************************************************
Route::get('admin/signin', [LoginController::class, 'index'])->name('signin');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login');
Route::post('admin/forget-password', [LoginController::class, 'admin_forget_password'])->name('admin.forget-password');
Route::get('admin/reset-password/{token}', [LoginController::class, 'admin_reset_password_form'])->name('admin.reset.password.get');
Route::post('admin/reset-password/reset_password_submit', [LoginController::class, 'admin_reset_password_submit'])->name('admin.reset.password.submit');



//************************************************ Admin Routes ************************************************
Route::group(['prefix' => 'admin', 'middleware' => ['AdminAuthenticate']], function () {
    
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('notifications', [AdminDashboardController::class, 'notificationslist'])->name('admin.notifications');
    Route::get('notificationdelete/{id}', [AdminDashboardController::class, 'notificationdelete'])->name('admin.notificationdelete');
    // Classes
    Route::get('class', [ClassController::class, 'index'])->name('admin.class');
    Route::post('class', [ClassController::class, 'store'])->name('admin.class.create');
    Route::get('class/status', [ClassController::class, 'status'])->name('admin.class.status');
    Route::get('scheduledclasses', [ClassController::class, 'scheduledclasses'])->name('admin.scheduledclasses');
    Route::get('pending/scheduledclasses', [ClassController::class, 'pendingscheduledclasses'])->name('admin.pendingscheduledclasses');
    Route::post('scheduledclass-search', [ClassController::class, 'scheduledsearch'])->name('admin.scheduledclass-search');
    Route::get('tutorslots', [SlotBookingController::class, 'admintutorslots'])->name('admin.tutorslots');
    Route::post('tutorslotssearch', [SlotBookingController::class, 'admintutorslotssearch'])->name('admin.tutorslotssearch');

    // Recordings
    Route::get('recordings', [RecordingsController::class, 'index'])->name('admin.recordings');
    Route::get('recordings/view/{id}', [RecordingsController::class, 'view'])->name('admin.recordings.view');
    Route::get('recordings/analytics', [RecordingsController::class, 'analytics'])->name('admin.recordings.analytics');
    Route::get('recordings/download/{id}', [RecordingsController::class, 'download'])->name('admin.recordings.download');
    Route::delete('recordings/{id}', [RecordingsController::class, 'destroy'])->name('admin.recordings.delete');
    Route::post('recordings/{id}/update-link', [RecordingsController::class, 'updateRecordingLink'])->name('admin.recordings.update-link');

    // Subjects
    Route::get('subject', [SubjectController::class, 'index'])->name('admin.subject');
    Route::post('subject', [SubjectController::class, 'store'])->name('admin.subject.create');
    Route::get('subjectcategory', [SubjectController::class, 'subjectcategory'])->name('admin.subjectcategory');
    Route::get('subject/status', [SubjectController::class, 'status'])->name('admin.subject.status');
    // Topics
    Route::get('topic', [TopicController::class, 'index'])->name('admin.topic');
    Route::post('topic', [TopicController::class, 'store'])->name('admin.topic.create');
    Route::get('topic/status', [TopicController::class, 'status'])->name('admin.topic.status');
    Route::post('topic-search', [TopicController::class, 'topicSearch'])->name('admin.topic-search');
    // Batch
    Route::get('batch', [BatchesController::class, 'index'])->name('admin.batch');
    Route::post('batch', [BatchesController::class, 'store'])->name('admin.batch.create');
    Route::get('batch/status', [BatchesController::class, 'status'])->name('admin.batch.status');
    Route::post('batchmapping/create', [BatchesController::class, 'mapping'])->name('admin.batchmapping.create');
    Route::get('viewbatchdata/{id}', [BatchesController::class, 'viewrecord'])->name('admin.viewbatchdata');
    Route::post('batches-search', [BatchesController::class, 'batchSearch'])->name('admin.batches-search');
    // Demo List
    Route::get('demolist', [DemoController::class, 'index'])->name('admin.demolist');
    Route::get('pendingtrials', [DemoController::class, 'pendingTrial'])->name('admin.pendingtrials');
    Route::post('demolist-search', [DemoController::class, 'demolistsearch'])->name('admin.demolist-search');
    Route::post('bookdemo', [DemoController::class, 'bookdemo'])->name('admin.bookdemo');
    Route::get('democancel/{id}', [DemoController::class, 'democancel'])->name('admin.democancel');
    Route::get('demodetails/{id}', [DemoController::class, 'demodetails'])->name('admin.demodetails');
    Route::post('demo/confirm', [GoogleCalendarController::class, 'democonfirm'])->name('admin.demo.confirm');
    Route::post('demo/update', [DemoController::class, 'demoupdate'])->name('admin.demo.update');
    Route::get('demo/status/update', [DemoController::class, 'demostatusupdate'])->name('admin.demo.status.update');
    // student profile from admin side
    Route::get('studentprofile/{id}', [StudentProfileController::class, 'adminstudentprofile'])->name('admin.studentprofile');
    Route::get('studentdelete/{id}', [StudentProfileController::class, 'studentdelete'])->name('admin.studentdelete');
    Route::get('students', [StudentProfileController::class, 'studentslist'])->name('admin.students');
    Route::get('students/status', [StudentProfileController::class, 'status'])->name('admin.students.status');
    Route::post('students-search', [StudentProfileController::class, 'studentslistsearch'])->name('admin.students-search');
    // tutor profile view by admin
    Route::get('tutorprofile/{id}', [TutorSearchController::class, 'admintutorprofile'])->name('admin.tutorprofile');
    Route::get('tutors', [TutorSearchController::class, 'tutorslist'])->name('admin.tutors');
    Route::get('tutordelete/{id}', [TutorSearchController::class, 'tutordelete'])->name('admin.tutordelete');

    Route::get('tutors/status', [TutorSearchController::class, 'status'])->name('admin.tutors.status');
    Route::post('tutors-search', [TutorSearchController::class, 'tutorslistsearch'])->name('admin.tutors-search');
    // Tutor slots check
    Route::get('tutorslotscheck/{id}', [TutorSearchController::class, 'tutorslotscheck'])->name('admin.tutorslotscheck');
    Route::post('adminslotsdelete', [SlotBookingController::class, 'slotsdelete'])->name('admin.slots.delete');
    Route::post('adminslotsupdate', [SlotBookingController::class, 'slotsupdate'])->name('admin.slots.update');

    // Admin Commission
    Route::get('commission/update', [TutorSearchController::class, 'commissionupdate'])->name('admin.commission.update');
    Route::get('rate/update', [TutorSearchController::class, 'rateupdate'])->name('admin.rate.update');
    // Payment details
    Route::get('payments', [PaymentsController::class, 'index'])->name('admin.payments');
    Route::get('student-payments-report', [PaymentsController::class, 'studentpaymentsreport'])->name('admin.reports.student-payments');
    Route::get('tutor-payments-report', [PaymentsController::class, 'tutorpaymentsreport'])->name('admin.reports.tutor-payments');
    Route::any('paymentsearch', [PaymentsController::class, 'paymentSearch'])->name('admin.paymentsearch');
    Route::any('tutorpaymentsearch', [PaymentsController::class, 'tutorPaymentSearch'])->name('admin.tutor-paymentsearch');
    Route::get('tutorpayments', [PaymentsController::class, 'tutorpayments'])->name('admin.tutorpayments');
    Route::get('tutorpaymentslist', [PaymentsController::class, 'tutorpaymentslist'])->name('admin.tutorpaymentslist');
    Route::post('payments', [PaymentsController::class, 'update'])->name('admin.payments.update');

    // NEW: Enrollment Request Management
    Route::get('enrollment-requests', [PaymentsController::class, 'enrollmentRequests'])->name('admin.enrollment-requests');
    Route::post('approve-enrollment', [PaymentsController::class, 'approveEnrollmentRequest'])->name('admin.approve-enrollment');
    Route::post('reject-enrollment', [PaymentsController::class, 'rejectEnrollmentRequest'])->name('admin.reject-enrollment');

    // admin tutor payment
    Route::any('tutor-payment', [PaymentsController::class, 'tutorPaymentAdmin'])->name('admin.tutor-payment');
    Route::post('fetchtutorsAmount', [PaymentsController::class, 'fetchtutorsAmount'])->name('admin.fetch-tutor-amount');

    // Learning contents
    Route::get('learningcontents', [LearningsContentsController::class, 'index'])->name('admin.learningcontents');
    Route::get('addlearningcontents', [LearningsContentsController::class, 'add'])->name('admin.addlearningcontents');
    Route::post('learningcontents/create', [LearningsContentsController::class, 'store'])->name('admin.learningcontents.create');
    Route::get('learningcontents/status', [LearningsContentsController::class, 'status'])->name('admin.learningcontents.status');
    Route::get('learningcontents/{id}', [LearningsContentsController::class, 'edit'])->name('admin.learningcontents.edit');
    Route::post('learningcontents-search', [LearningsContentsController::class, 'search'])->name('admin.learningcontents-search');
    // Assignments
    Route::get('assignments', [AssignmentsController::class, 'adminindex'])->name('admin.assignments');
    Route::get('assignments/status', [AssignmentsController::class, 'status'])->name('admin.assignments.status');
    Route::get('assignments/{id}', [AssignmentsController::class, 'view'])->name('admin.assignments.view');
    Route::post('assignments-search', [AssignmentsController::class, 'assignmentsSearch'])->name('admin.assignments-search');
    Route::post('admin-assignments-search', [AssignmentsController::class, 'adminassignmentsSearch'])->name('admin.assignmentsearch');
    // Question Bank
    Route::get('questionbank', [QuestionBankController::class, 'index'])->name('admin.questionbank');
    Route::get('questionbank/create', [QuestionBankController::class, 'create'])->name('admin.questionbank.create');
    Route::get('questionbank/subjective/create', [QuestionBankController::class, 'subjective_create'])->name('admin.questionbank.subjective.create');
    Route::post('questionbank/store', [QuestionBankController::class, 'store'])->name('admin.questionbank.store');
    Route::get('question/status', [QuestionBankController::class, 'status'])->name('admin.question.status');
    Route::get('questionupdate/{id}', [QuestionBankController::class, 'view'])->name('admin.questionupdate.view');
    Route::post('questionbank-search', [QuestionBankController::class, 'questionbankSearch'])->name('admin.questionbank-search');

    Route::post('questionbank/subjective-store', [QuestionBankController::class, 'storeSubjective'])->name('admin.questionbank.subjective.store');


    // Online tests
    Route::get('onlinetestlist', [OnlineTestController::class, 'index'])->name('admin.onlinetests');
    Route::get('onlinetestresponseslist', [OnlineTestController::class, 'onlinetestresponseslist'])->name('admin.onlinetests.responses.list');
    Route::get('onlinetests/responses/{id}', [OnlineTestController::class, 'onlinetestresponse'])->name('admin.onlinetests.responses');
    Route::get('onlinetests/responses/student/{id}', [OnlineTestController::class, 'onlinetestresponsestudent'])->name('admin.onlinetests.responses.student');
    Route::get('onlinetests', [OnlineTestController::class, 'create'])->name('admin.onlinetests.create');
    Route::post('onlinetests', [OnlineTestController::class, 'store'])->name('admin.onlinetests.store');
    Route::get('onlinetests/{id}', [OnlineTestController::class, 'edit'])->name('admin.onlinetests.edit');
    Route::get('onlinetestquestions/{id}', [OnlineTestController::class, 'viewquestions'])->name('admin.onlinetestquestions.viewquestions');
    Route::post('onlinetestlist-search', [OnlineTestController::class, 'onlinetestSearch'])->name('admin.onlinetests-search');
    // Get questions by Subject
    Route::post('fetchquestions', [OnlineTestController::class, 'fetchquestions'])->name('fetchquestions');
    // Message By Student
    Route::get('messages', [MessagesController::class, 'messagesbyadmin'])->name('admin.messages');
    Route::get('studentmessages', [MessagesController::class, 'messagesbyadminstudents'])->name('admin.messages.students');
    Route::get('studentmessages/{id}', [MessagesController::class, 'messagesbyadminstudentmessages'])->name('admin.messages.studentmessages');
    Route::get('studentmessagesload/{id}', [MessagesController::class, 'messagesbyadminstudentmessagesload'])->name('admin.messages.studentmessagesload');
    Route::get('adminclearsstudentmessages/{id}', [MessagesController::class, 'chatClearAdminstudent'])->name('admin.messages.clearstudentmessages');
    Route::get('tutormessages', [MessagesController::class, 'messagesbyadmintutor'])->name('admin.messages.tutors');
    Route::get('tutormessages/{id}', [MessagesController::class, 'messagesbyadmintutormessages'])->name('admin.messages.tutormessages');
    Route::get('tutormessagesload/{id}', [MessagesController::class, 'messagesbyadmintutormessagesload'])->name('admin.messages.tutormessagesload');
    Route::get('chatClearAdmintutor/{id}', [MessagesController::class, 'chatClearAdmintutor'])->name('admin.messages.cleartutormessages');
    Route::post('sendmessage', [MessagesController::class, 'messagesentbyadmin'])->name('admin.messages.send');
    Route::get('chat-presence-status', [MessagesController::class, 'chatPresenceStatusAdmin'])->name('admin.chat.presence.status');
    Route::post('chat-presence-auth', [MessagesController::class, 'chatPresenceAuth'])->name('admin.chat.presence.auth');
    Route::post('messages/student/search', [MessagesController::class, 'chatstudentsearch'])->name('admin.chat.student.search');
    Route::post('messages/tutor/search', [MessagesController::class, 'chattutorsearch'])->name('admin.chat.tutor.search');
    // Admin Reports
    // Route::get('classes-report',[ReportController::class, 'admin_class_report'])->name('admin.reports.class-list');
    Route::get('chat-report', [ReportController::class, 'admin_chat_report'])->name('admin.reports.chat-list');
    Route::post('payouts-search', [PaymentsController::class, 'adminPayoutsSearch'])->name('admin.payouts-search');

    Route::get('blogs', [BlogsController::class, 'index'])->name('admin.blogs.list');
    Route::get('blogs/create', [BlogsController::class, 'create'])->name('admin.blogs.create');
    Route::post('blogs/create', [BlogsController::class, 'store'])->name('admin.blogs.store');
    Route::get('blogs/update/{id}', [BlogsController::class, 'edit'])->name('admin.blogs.edit');
    Route::post('blogs/status', [BlogsController::class, 'changeStatus'])->name('admin.blogs.status');

    // Admin Faqs Routes
    Route::get('faqs', [FaqsController::class, 'index'])->name('admin.faqs.list');
    Route::get('faqs/create', [FaqsController::class, 'create'])->name('admin.faqs.create');
    Route::post('faqs', [FaqsController::class, 'store'])->name('admin.faqs.store');
    Route::get('faqs/{id}/edit', [FaqsController::class, 'edit'])->name('admin.faqs.edit');
    Route::post('faqs/status', [FaqsController::class, 'changeStatus'])->name('admin.faqs.status');

    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('admin.contact.messages');
    Route::delete('/contact-messages/{id}', [ContactMessageController::class, 'destroy'])->name('admin.contact.messages.delete');
});
