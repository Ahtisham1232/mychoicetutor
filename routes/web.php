<?php

use App\Events\RealTimeMessage;
use App\Http\Controllers\admin\BatchesController;
use App\Http\Controllers\admin\ClassController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\DemoController;
use App\Http\Controllers\admin\LearningsContentsController;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\OnlineTestController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\admin\QuestionBankController;
use App\Http\Controllers\admin\SubjectController;
use App\Http\Controllers\admin\TopicController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JitsiController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MyFavouriteController;
use App\Http\Controllers\student\DashboardController;
use App\Http\Controllers\tutor\TutorDashboardController;
use App\Http\Controllers\tutor\TutorProfileController;
use App\Http\Controllers\student\DemoListController;
use App\Http\Controllers\student\MyLearningController;
use App\Http\Controllers\student\StudentProfileController;
use App\Http\Controllers\student\SubjectsController;
use App\Http\Controllers\student\TutorSearchController;
use App\Http\Controllers\student\PaymentController;
use App\Http\Controllers\tutor\ClassScheduleController;

use App\Http\Controllers\TutorreviewsController;
use App\Http\Controllers\ZoomClassesController;
use App\Http\Controllers\SlotBookingController;
// use App\Http\Controllers\WorldPayController;
use App\Models\classes;
use App\Models\SlotBooking;
use App\Models\tutorreviews;
// use App\Http\Controllers\StudentregistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});
// Common Activity
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/deepesh', function(){
//     event(new RealTimeMessage('uyuyyuy this is a sample broadcast'));
// });
Route::get('/listen', function(){
    return view('listen');
});
Route::get('notifications',[HomeController::class,'notifications'])->name('notifications');
Route::get('markAsRead/{id}',[HomeController::class,'markAsRead'])->name('markAsRead');
Route::get('checkNotificationDetails/{id}',[HomeController::class,'checkNotificationDetails'])->name('checkNotificationDetails');
Route::get('findatutor',[HomeController::class,'findatutor'])->name('findatutor');
Route::get('tutor-details/{id}',[HomeController::class,'tutordetails'])->name('tutordetails');
Route::get('index/slots/search', [SlotBookingController::class, 'indexslotsearch'])->name('index.slots.search');
Route::get('resources', [HomeController::class, 'indexresources'])->name('index.resources');
Route::get('resources/{id}', [HomeController::class, 'indexresourcesdetails'])->name('index.resources.details');
Route::post('toptutorsearch',[HomeController::class, 'toptutorsearch'])->name('toptutorsearch');
Route::post('advancesearch',[HomeController::class, 'advancesearch'])->name('advancesearch');
Route::get('reviews',[HomeController::class,'reviewslist'])->name('revieweslist');

// Route::get('findatutor', function(){
//     return view('front-cms/findatutor');
// });
// Changed to new UI for Students
Route::get('/student/register', [HomeController::class, 'std_registration'])->name('std_registration');
Route::post('/student/register',[HomeController::class,'student_registration_form'])->name('student_registration_form');
Route::get('/student/login', [HomeController::class, 'std_login'])->name('studentlogin');
Route::get('/student/mobile-verify',[HomeController::class,'student_mobile_verify'])->name('student_mobile_verify');
Route::post('/student/mobile-verify',[HomeController::class,'verify_student_mobile'])->name('verify_student_mobile');
Route::get('/student-login',[HomeController::class, 'student_login'])->name('student_login');
Route::post('/forget-password',[HomeController::class, 'forget_password'])->name('forget_password');
Route::get('reset-password/{token}', [HomeController::class, 'reset_password_form'])->name('reset.password.get');
// Handle reset
Route::post('/reset-password/reset_password_submit', [HomeController::class, 'reset_password_submit'])->name('reset.password.post');

Route::get('/free-trial-class/student-login/{id}',[HomeController::class, 'free_trial_class_student_login_form'])->name('student_login_trial');
Route::get('/free-trial-class-student-login',[HomeController::class, 'free_trial_class_student_login'])->name('student_login_trial_post');

Route::get('/enroll-class/student-login/{id}',[HomeController::class, 'enroll_class_student_login_form'])->name('student_login_enroll');
Route::get('/enroll-class-student-login',[HomeController::class, 'enroll_class_student_login'])->name('student_login_enroll_post');

Route::post('tutor-search-guest', [TutorSearchController::class, 'tutorsindexsearch'])->name('guest.tutorsindexsearch');
Route::post('tutor-dashboard-search',[TutorSearchController::class, 'tutorsdashboardsearch'])->name('student.tutorsdashboardsearch');

// Changed to new UI for Tutors
Route::get('/tutor/register', [HomeController::class, 'ttr_registration'])->name('ttr_registration');
Route::post('/tutor/register',[HomeController::class,'tutor_registration_form'])->name('tutor_registration_form');
Route::get('/tutor/login', [HomeController::class, 'ttr_login'])->name('tutorlogin');
Route::get('/tutor/mobile-verify',[HomeController::class,'tutor_mobile_verify'])->name('tutor_mobile_verify');
Route::post('/tutor/mobile-verify',[HomeController::class,'verify_tutor_mobile'])->name('verify_tutor_mobile');
Route::get('/tutor-login',[HomeController::class, 'tutor_login'])->name('tutor_login');

//parent authentications
Route::get('/parent/login', [HomeController::class, 'parent_login'])->name('parentlogin');
Route::post('/parent/login', [HomeController::class, 'parent_login_attempt'])->name('parent.login');

// Tutor search in index page

Route::post('tutorsearch', [TutorSearchController::class, 'tutorsearchindex'])->name('tutorsearchindex');


Route::get("logout", [HomeController::class, "logout"])->name("logout");

// Route::get('/tutor/login', [HomeController::class, 'ttr_login'])->name('tutorlogin');

Route::post('fetchsubjects', [CommonController::class, 'fetchsubjects'])->name('fetchsubjects');
Route::post('fetchtutorsubjects', [CommonController::class, 'fetchtutorsubjects'])->name('fetchtutorsubjects');
Route::post('fetchtopics', [CommonController::class, 'fetchtopics'])->name('fetchtopics');
Route::post('fetchslottime', [CommonController::class, 'fetchslottime'])->name('fetchslottime');
Route::post('studentsbyclass', [CommonController::class, 'studentsbyclass'])->name('studentsbyclass');
Route::post('batchbysubject', [CommonController::class, 'batchbysubject'])->name('batchbysubject');
Route::post('studentsbybatch', [CommonController::class, 'studentsbybatch'])->name('studentsbybatch');
Route::post('fetchtutors', [CommonController::class, 'fetchtutors'])->name('fetchtutors');
Route::get('subjects',[SubjectController::class,'cmsindex'])->name('cmsindex');
// Student Activity
Route::group(['prefix' => 'student', 'middleware' => ['StudentAuthenticate']], function () {
    // student dashboard

    // Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::get('/payment/{order_id}', [TutorSearchController::class, 'paymentPage'])->name('payment.checkout');
    Route::post('/stripe/webhook', [TutorSearchController::class, 'handleStripeWebhook']);
    Route::get('/payment/success/{order_id}', [TutorSearchController::class, 'paymentSuccess'])->name('payment.success');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('student.dashboard');
    Route::get('notifications', [DashboardController::class, 'notificationslist'])->name('student.notifications');
    // student profile
    Route::get('profile', [StudentProfileController::class, 'index'])->name('student.profile');
    Route::get('profileupdate/{id}', [StudentProfileController::class, 'edit'])->name('student.profileupdate');
    Route::post('updateprofiledata', [StudentProfileController::class, 'updateprofiledata'])->name('student.updateprofiledata');
    Route::post('updateprofilepic', [StudentProfileController::class, 'profilepicupdate'])->name('student.profilepicupdate');
    Route::post('studentacadd', [StudentProfileController::class, 'studentacadd'])->name('student.studentacadd');
    Route::get('studentacdel/{id}', [StudentProfileController::class, 'studentacdel'])->name('student.studentacdel');
    // tutor search
    Route::get('yourtutor', [TutorSearchController::class, 'yourtutor'])->name('student.yourtutor');
    Route::get('tutorprofile/{id}', [TutorSearchController::class, 'tutorprofile'])->name('student.tutorprofile');
    Route::get('searchtutor', [TutorSearchController::class, 'index'])->name('student.searchtutor');
    Route::get('sorttutor/{value}/{type}', [TutorSearchController::class, 'sorttutor'])->name('student.sorttutor');
    Route::post('tutoradvs', [TutorSearchController::class, 'tutoradvs'])->name('student.tutoradvs');
   
    // student demo
    Route::get('demolist', [DemoListController::class, 'index'])->name('student.demolist');
    Route::post('bookdemo', [DemoListController::class, 'bookdemo'])->name('student.bookdemo');
    Route::get('democancel/{id}', [DemoListController::class, 'democancel'])->name('student.democancel');
    Route::post('demolist-search', [DemoListController::class, 'demolistSearch'])->name('student.demolist-search');
    // Purchase Class
    Route::post('purchaseclass', [TutorSearchController::class, 'purchaseclass'])->name('student.purchaseclass');
    Route::get('/stripe/payment/success', [TutorSearchController::class, 'stripePaymentSuccess'])->name('stripe.payment.success');

    // Enroll Now with slot selection
    Route::get('enrollnow/{id}', [TutorSearchController::class, 'enrollnow'])->name('student.admission');
    // Enroll Update with Slot Selections
    Route::get('enrollupdate/{id}', [TutorSearchController::class, 'enrollupdate'])->name('student.enrollupdate');
    Route::post('updateslots', [TutorSearchController::class, 'updateslots'])->name('student.updateslots');
    // Subjects
    Route::get('subjects', [SubjectsController::class, 'index'])->name('student.subjects');
    Route::get('subjectlist', [SubjectsController::class, 'subjectlist'])->name('student.subjectlist');
    // Syllabus
    Route::get('subjects/syllabus/{id}', [SubjectsController::class, 'getsyllabus'])->name('student.subjects.syllabus');
    // My Learning
    Route::any('mylearnings', [MyLearningController::class, 'index'])->name('student.mylearnings');
    // Route::post('mylearnings-search', [MyLearningController::class, 'learningSearch'])->name('student.mylearnings-search');
    // Classes
    Route::get('classes', [ClassController::class, 'studentclass'])->name('student.classes');
    Route::post('classes-search', [ClassController::class, 'studentclassSearch'])->name('student.classes-search');
    Route::get('liveclass/join/update',[ZoomClassesController::class,'liveclassjoinupdate'])->name('tutor.liveclass.join.update');

    // completed classes
    Route::get('completed-classes', [ClassController::class, 'studentCompletedclass'])->name('student.completed-classes');
    Route::post('completed-classes-search', [ClassController::class, 'studentCompletedclasssearch'])->name('student.completed-classes-search');

    // Feedback by Student
    Route::post('feedback/submit',[TutorreviewsController::class,'feedbacksubmitstudent'])->name('student.feedback.submit');
    // Feedback by tutor
    Route::get('myfeedback', [TutorreviewsController::class, 'studentfeedbacklist'])->name('student.myfeedback');
    Route::get('trialsuccess', [DemoListController::class, 'trialsuccess'])->name('student.trialsuccess');
    Route::get('enrollsuccess', [TutorSearchController::class, 'enrollsuccess'])->name('student.enrollsuccess');
    // Route::get('trialsuccess', [TutorreviewsController::class, 'trialsuccess'])->name('student.trialsuccess');
    // Message By Student
    Route::get('messages', [MessagesController::class, 'messagesbystudent'])->name('student.messages');
    Route::get('adminmessages', [MessagesController::class, 'messagesbystudentadmins'])->name('student.messages.admins');
    Route::get('adminmessages/{id}', [MessagesController::class, 'messagesbystudentadminmessages'])->name('student.messages.adminmessages');
    Route::get('adminmessagesload/{id}', [MessagesController::class, 'messagesbystudentadminmessagesload'])->name('student.messages.adminmessagesload');
    Route::get('tutormessages', [MessagesController::class, 'messagesbystudenttutor'])->name('student.messages.tutor');
    Route::get('tutormessages/{id}', [MessagesController::class, 'messagesbystudenttutormessages'])->name('student.messages.tutormessages');
    Route::get('tutormessagesload/{id}', [MessagesController::class, 'messagesbystudenttutormessagesload'])->name('student.messages.tutormessagesload');
    Route::post('sendmessage', [MessagesController::class, 'messagesentbystudent'])->name('student.messages.send');
    // Assignments
    Route::get('assignments',[AssignmentsController::class,'studentassignmentslist'])->name('student.assignments.list');
    Route::post('assignments/upload',[AssignmentsController::class,'studentassignmentsupload'])->name('student.assignments.upload');
    Route::post('assignments-search',[AssignmentsController::class,'studentassignmentsSearch'])->name('student.assignments.search');
    // Student Fees/Payments
    Route::get('studentpayments', [PaymentsController::class, 'studentpayments'])->name('student.studentpayments');
    Route::post('studentpayments-search', [PaymentsController::class, 'studentpaymentsSearch'])->name('student.payments-search');
    // Online tests/exams
    Route::get('exams', [OnlineTestController::class, 'studentexams'])->name('student.exams');
    Route::post('exams-search', [OnlineTestController::class, 'studentexamsSearch'])->name('student.exams-search');
    Route::get('taketest/{id}', [OnlineTestController::class, 'taketest'])->name('student.taketest');
    Route::get('taketest-subjective/{id}', [OnlineTestController::class, 'taketestsubjective'])->name('student.taketest.subjective');
    Route::get('exam/report/{id}', [OnlineTestController::class, 'testreport'])->name('student.test.report');
    Route::post('/save-responses', [OnlineTestController::class, 'saveResponses'])->name('student.save.responses');
    Route::post('/save-subjective-responses', [OnlineTestController::class, 'saveSubjectiveResponses'])->name('student.save.subjective-responses');
    // Route::post('/save-responses', 'OnlineTestController@saveResponses')->name('student.save.responses');
    Route::post('storeSubjectiveDataInTemporaryTable', [OnlineTestController::class, 'storeSubjectiveDataInTemporaryTable'])->name('student.storeSubjectiveDataInTemporaryTable');
    Route::post('getAnswerFromSubjectiveTempTable', [OnlineTestController::class, 'getAnswerFromSubjectiveTempTable'])->name('student.getAnswerFromSubjectiveTempTable');
    // Reports
    Route::get('attendance-reports',[ClassController::class,'student_attendance_report'])->name('student.attendance.report');
    Route::get('class-reports',[ClassController::class,'student_class_report'])->name('student.class.report');
    // My Favourites
    Route::get('addfav/{id}',[MyFavouriteController::class,'addfav'])->name('student.addfav');
});

// Admin Routes
Route::get('admin/signin', [LoginController::class, 'index'])->name('signin');
Route::get('admin/login', [LoginController::class, 'login'])->name('admin.login');

// admin activity
Route::group(['prefix' => 'admin', 'middleware' => ['AdminAuthenticate']], function () {
    // Admin dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('notifications', [AdminDashboardController::class, 'notificationslist'])->name('admin.notifications');
    Route::get('notificationdelete/{id}', [AdminDashboardController::class, 'notificationdelete'])->name('admin.notificationdelete');
    // Classes
    Route::get('class', [ClassController::class, 'index'])->name('admin.class');
    Route::post('class', [ClassController::class, 'store'])->name('admin.class.create');
    Route::get('class/status', [ClassController::class, 'status'])->name('admin.class.status');
    Route::get('scheduledclasses', [ClassController::class, 'scheduledclasses'])->name('admin.scheduledclasses');
    Route::get('tutorslots', [SlotBookingController::class, 'admintutorslots'])->name('admin.tutorslots');
    Route::post('tutorslotssearch', [SlotBookingController::class, 'admintutorslotssearch'])->name('admin.tutorslotssearch');
    
    // Recordings
    Route::get('recordings', [\App\Http\Controllers\admin\RecordingsController::class, 'index'])->name('admin.recordings');
    Route::get('recordings/view/{id}', [\App\Http\Controllers\admin\RecordingsController::class, 'view'])->name('admin.recordings.view');
    Route::get('recordings/analytics', [\App\Http\Controllers\admin\RecordingsController::class, 'analytics'])->name('admin.recordings.analytics');
    Route::get('recordings/download/{id}', [\App\Http\Controllers\admin\RecordingsController::class, 'download'])->name('admin.recordings.download');
    Route::delete('recordings/{id}', [\App\Http\Controllers\admin\RecordingsController::class, 'destroy'])->name('admin.recordings.delete');
    Route::post('recordings/{id}/update-link', [\App\Http\Controllers\admin\RecordingsController::class, 'updateRecordingLink'])->name('admin.recordings.update-link');

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
    Route::post('bookdemo', [DemoController::class, 'bookdemo'])->name('admin.bookdemo');
    Route::get('democancel/{id}', [DemoController::class, 'democancel'])->name('admin.democancel');
    Route::get('demodetails/{id}', [DemoController::class, 'demodetails'])->name('admin.demodetails');
    Route::post('demo/confirm', [GoogleCalendarController::class, 'democonfirm'])->name('admin.demo.confirm');
    Route::post('demo/update', [DemoController::class, 'demoupdate'])->name('admin.demo.update');
    Route::get('demo/status/update', [DemoController::class, 'demostatusupdate'])->name('admin.demo.status.update');
    Route::post('demolist-search', [DemoController::class, 'demolistsearch'])->name('admin.demolist-search');
    Route::post('scheduledclass-search', [ClassController::class, 'scheduledsearch'])->name('admin.scheduledclass-search');
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
    Route::get('tutorslotscheck/{id}',[TutorSearchController::class, 'tutorslotscheck'])->name('admin.tutorslotscheck');
    Route::post('adminslotsdelete', [SlotBookingController::class, 'slotsdelete'])->name('admin.slots.delete');
    Route::post('adminslotsupdate', [SlotBookingController::class, 'slotsupdate'])->name('admin.slots.update');

    // Admin Commission
    Route::get('commission/update',[TutorSearchController::class,'commissionupdate'])->name('admin.commission.update');
    Route::get('rate/update',[TutorSearchController::class,'rateupdate'])->name('admin.rate.update');
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
    Route::post('messages/student/search', [MessagesController::class, 'chatstudentsearch'])->name('admin.chat.student.search');
    Route::post('messages/tutor/search', [MessagesController::class, 'chattutorsearch'])->name('admin.chat.tutor.search');
    // Admin Reports
    Route::get('classes-report',[ReportController::class, 'admin_class_report'])->name('admin.reports.class-list');
    Route::get('chat-report',[ReportController::class, 'admin_chat_report'])->name('admin.reports.chat-list');
    Route::post('payouts-search',[PaymentsController::class,'adminPayoutsSearch'])->name('admin.payouts-search');
    Route::get('blogs', [BlogsController::class, 'index'])->name('admin.blogs.list');
    Route::get('blogs/create', [BlogsController::class, 'create'])->name('admin.blogs.create');
    Route::post('blogs/create', [BlogsController::class, 'store'])->name('admin.blogs.store');
    Route::get('blogs/update/{id}', [BlogsController::class, 'edit'])->name('admin.blogs.edit');
    Route::post('admin/blogs/status', [BlogsController::class, 'changeStatus'])->name('admin.blogs.status');


});


// Tutor Activity
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
    // Tutor Class Scheduling
    // Route::post('classschedule',[ClassScheduleController::class,'create'])->name('tutor.classschedule.create');

    // Demo List
    Route::get('demolist', [DemoController::class, 'tutordemolist'])->name('tutor.demolist');
    Route::post('demolist', [DemoController::class, 'tutordemoupdate'])->name('tutor.demo.update');
    Route::get('demodetails/{id}', [DemoController::class, 'demodetails'])->name('admin.demodetails');
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
    Route::get('learningcontents', [\App\Http\Controllers\tutor\LearningContentsController::class, 'index'])->name('tutor.learningcontents');
    Route::get('learningcontents/add', [\App\Http\Controllers\tutor\LearningContentsController::class, 'add'])->name('tutor.learningcontents.add');
    Route::post('learningcontents/create', [\App\Http\Controllers\tutor\LearningContentsController::class, 'store'])->name('tutor.learningcontents.create');
    Route::get('learningcontents/status', [\App\Http\Controllers\tutor\LearningContentsController::class, 'status'])->name('tutor.learningcontents.status');
    Route::get('learningcontents/{id}', [\App\Http\Controllers\tutor\LearningContentsController::class, 'edit'])->name('tutor.learningcontents.edit');
    Route::post('learningcontents-search', [\App\Http\Controllers\tutor\LearningContentsController::class, 'search'])->name('tutor.learningcontents.search');
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
   //payments
   Route::get('payments', [PaymentsController::class, 'tutorStudentPayments'])->name('tutor.payments');
   Route::any('paymentsearch', [PaymentsController::class, 'paymentSearchTutor'])->name('tutor.paymentsearch');
   Route::post('payment-update', [PaymentsController::class, 'update'])->name('tutor.payments.update');
   // Payouts
   Route::get('payouts',[PaymentsController::class,'tutorpayouts'])->name('tutor.payouts');
   Route::post('payouts-search',[PaymentsController::class,'tutorpayoutsSearch'])->name('tutor.payouts-search');

    // learning Cintents
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
// parent routes
Route::group(['prefix' => 'parent', 'middleware' => ['StudentAuthenticate']], function () {
    // student dashboard
    Route::get('dashboard', [DashboardController::class, 'parent_dashboard'])->name('parent.dashboard');
    // student profile
    // Route::get('profile', [StudentProfileController::class, 'index'])->name('student.profile');
    // Route::get('profileupdate/{id}', [StudentProfileController::class, 'edit'])->name('student.profileupdate');
    // Route::post('updateprofiledata', [StudentProfileController::class, 'updateprofiledata'])->name('student.updateprofiledata');
    // Route::post('updateprofilepic', [StudentProfileController::class, 'profilepicupdate'])->name('student.profilepicupdate');
    // Route::post('studentacadd', [StudentProfileController::class, 'studentacadd'])->name('student.studentacadd');
    // Route::get('studentacdel/{id}', [StudentProfileController::class, 'studentacdel'])->name('student.studentacdel');
    // // tutor search
    // Route::get('yourtutor', [TutorSearchController::class, 'yourtutor'])->name('student.yourtutor');
    // Route::get('tutorprofile/{id}', [TutorSearchController::class, 'tutorprofile'])->name('student.tutorprofile');
    // Route::get('searchtutor', [TutorSearchController::class, 'index'])->name('student.searchtutor');
    // Route::get('sorttutor/{value}/{type}', [TutorSearchController::class, 'sorttutor'])->name('student.sorttutor');
    // Route::post('tutoradvs', [TutorSearchController::class, 'tutoradvs'])->name('student.tutoradvs');
    // // student demo
    Route::get('demolist', [DemoListController::class, 'parentindex'])->name('parent.demolist');
    // Route::post('bookdemo', [DemoListController::class, 'bookdemo'])->name('student.bookdemo');
    // Route::get('democancel/{id}', [DemoListController::class, 'democancel'])->name('student.democancel');
    // Route::post('demolist-search', [DemoListController::class, 'demolistSearch'])->name('student.demolist-search');
    // // Purchase Class
    // Route::post('purchaseclass', [TutorSearchController::class, 'purchaseclass'])->name('student.purchaseclass');
    // // Subjects
    Route::get('subjects', [SubjectsController::class, 'parent_index'])->name('parent.subjects');
    // // Syllabus
    // Route::get('subjects/syllabus/{id}', [SubjectsController::class, 'getsyllabus'])->name('student.subjects.syllabus');
    // // My Learning
    Route::any('mylearnings', [MyLearningController::class, 'parent_index'])->name('parent.mylearnings');
    // // Route::post('mylearnings-search', [MyLearningController::class, 'learningSearch'])->name('student.mylearnings-search');
    // // Classes
    Route::get('classes', [ClassController::class, 'studentclassParent'])->name('parent.classes');
    // Route::post('classes-search', [ClassController::class, 'studentclassSearch'])->name('student.classes-search');

    // Route::get('liveclass/join/update',[ZoomClassesController::class,'liveclassjoinupdate'])->name('tutor.liveclass.join.update');


    // // completed classes
    Route::get('completed-classes', [ClassController::class, 'studentCompletedclassParent'])->name('parent.completed-classes');

    // // Feedback by Student
    // Route::post('feedback/submit',[TutorreviewsController::class,'feedbacksubmitstudent'])->name('student.feedback.submit');
    // // Feedback by tutor
    // Route::get('myfeedback', [TutorreviewsController::class, 'studentfeedbacklist'])->name('student.myfeedback');
    // // Message By Student
    // Route::get('messages', [MessagesController::class, 'messagesbystudent'])->name('student.messages');
    // Route::get('adminmessages', [MessagesController::class, 'messagesbystudentadmins'])->name('student.messages.admins');
    // Route::get('adminmessages/{id}', [MessagesController::class, 'messagesbystudentadminmessages'])->name('student.messages.adminmessages');
    // Route::get('tutormessages', [MessagesController::class, 'messagesbystudenttutor'])->name('student.messages.tutor');
    // Route::get('tutormessages/{id}', [MessagesController::class, 'messagesbystudenttutormessages'])->name('student.messages.tutormessages');
    // Route::post('sendmessage', [MessagesController::class, 'messagesentbystudent'])->name('student.messages.send');
    // // Assignments
    Route::get('assignments',[AssignmentsController::class,'studentassignmentslistParent'])->name('parent.assignments.list');
    // Route::post('assignments/upload',[AssignmentsController::class,'studentassignmentsupload'])->name('student.assignments.upload');
    // Route::post('assignments-search',[AssignmentsController::class,'studentassignmentsSearch'])->name('student.assignments.search');
    // // Student Fees/Payments
    Route::get('studentpayments', [PaymentsController::class, 'studentpaymentsParent'])->name('parent.studentpayments');
    // Route::post('studentpayments-search', [PaymentsController::class, 'studentpaymentsSearch'])->name('student.payments-search');
    // // Online tests/exams
    Route::get('exams', [OnlineTestController::class, 'studentexamsParent'])->name('parent.exams');
    // Route::post('exams-search', [OnlineTestController::class, 'studentexamsSearch'])->name('student.exams-search');
    // Route::get('taketest/{id}', [OnlineTestController::class, 'taketest'])->name('student.taketest');
    // Route::get('taketest-subjective/{id}', [OnlineTestController::class, 'taketestsubjective'])->name('student.taketest.subjective');
    // Route::get('exam/report/{id}', [OnlineTestController::class, 'testreport'])->name('student.test.report');
    // Route::post('/save-responses', [OnlineTestController::class, 'saveResponses'])->name('student.save.responses');
    // // Route::post('/save-responses', 'OnlineTestController@saveResponses')->name('student.save.responses');

    Route::get('attendance-reports',[ClassController::class,'student_attendance_reportParent'])->name('parent.attendance.report');
    Route::get('class-reports',[ClassController::class,'student_class_reportParent'])->name('parent.class.report');

});

// Create Jitsi Meeting
Route::get('/jitsi', [JitsiController::class, 'index']);

// Jitsi Webhook for automatic recording processing
Route::post('/webhook/jitsi/recording', [\App\Http\Controllers\JitsiWebhookController::class, 'handleWebhook'])->name('webhook.jitsi.recording');

// Jibri HTTP API routes (bypass buggy XMPP in Jibri 8.0)
Route::prefix('jibri')->group(function () {
    Route::post('/start-recording', [\App\Http\Controllers\JibriRecordingController::class, 'startRecording'])->name('jibri.start');
    Route::post('/stop-recording', [\App\Http\Controllers\JibriRecordingController::class, 'stopRecording'])->name('jibri.stop');
    Route::get('/status', [\App\Http\Controllers\JibriRecordingController::class, 'getStatus'])->name('jibri.status');
});

// Test page for Jibri recording (remove in production)
Route::get('/test-recording', function () {
    return view('test-recording');
});
// Route::get('oauth2callback', [GoogleCalendarController::class,'oauthCallback'])->name('oauth2callback');
Route::get('tutorprofile/{id}', [TutorSearchController::class, 'teacherprofile'])->name('tutorprofile');
Route::get('/tutor/dashboard/oauth2callback', [GoogleCalendarController::class, 'oauth2callbackdemo']);




// kamran---------------------


Route::get('howitworks', function(){
    return view('front-cms/howitworks');
});

Route::get('price', function(){
    return view('front-cms/price');
});

Route::get('allsubjects',[HomeController::class,'allsubjects'])->name('allsubjectindex');

Route::get('courses', function(){
    return view('front-cms/courses');
});

Route::get('why-choose-us', function(){
    return view('front-cms/aboutus');
});

// Route::get('why-choose-us', function(){
//     return view('front-cms/why_choose_us');
// });

Route::get('aboutus', function(){
    return view('front-cms/why_choose_us');
});

// Route::get('blogdetails', function(){
//     return view('front-cms/blogdetails');
// });

Route::get('contact', function(){
    return view('front-cms/contact');
});
Route::get('privacypolicy', function(){
    return view('front-cms/privacypolicy');
});
Route::get('refundpolicy', function(){
    return view('front-cms/refundpolicy');
});
Route::get('termsandconditions', function(){
    return view('front-cms/termsandconditions');
})->name('termsandconditions');

Route::get('contact', function(){
    return view('front-cms/contact');
});

// WorldPay Payment Gateway
// Route::get('/worldpay/payment/', [WorldPayController::class, 'showPaymentPage'])->name('worldpay.payment.page');
// Route::post('/worldpay/initiate', [WorldPayController::class, 'initiatePayment'])->name('worldpay.payment');
// Route::get('/initiate-payment', [PaymentController::class, 'initiatePayment'])->name('initiate.payment');

// Optional: Success, cancel, and failed routes
Route::get('/payment/success', function () {
    return 'Payment successful!';
})->name('payment.success');

Route::get('/payment/cancel', function () {
    return 'Payment cancelled.';
})->name('payment.cancel');

Route::get('/payment/failed', function () {
    return 'Payment failed.';
})->name('payment.failed');

Route::get('/.well-known/pki-validation/{filename}', function ($filename) {
    $path = public_path('.well-known/pki-validation/' . $filename);

    if (File::exists($path)) {
        return response()->file($path);
    }

    abort(404);
});
