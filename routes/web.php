<?php

use App\Events\RealTimeMessage;
use App\Http\Controllers\admin\BatchesController;
use App\Http\Controllers\admin\ClassController;
use App\Http\Controllers\admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\DemoController;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\OnlineTestController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\admin\QuestionBankController;
use App\Http\Controllers\admin\SubjectController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JitsiController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\MyFavouriteController;
use App\Http\Controllers\student\DashboardController;
use App\Http\Controllers\tutor\TutorDashboardController;
use App\Http\Controllers\tutor\TutorProfileController;
use App\Http\Controllers\student\DemoListController;
use App\Http\Controllers\student\MyLearningController;
use App\Http\Controllers\student\StudentProfileController;
use App\Http\Controllers\student\SubjectsController;
use App\Http\Controllers\student\TutorSearchController;
use App\Http\Controllers\JibriRecordingController;
use App\Http\Controllers\JitsiWebhookController;
use App\Http\Controllers\tutor\LearningContentsController;

use App\Http\Controllers\TutorreviewsController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ZoomClassesController;
use App\Http\Controllers\SlotBookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Cache;


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



//************************************************ Home Route ************************************************
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/deepesh', function(){
//     event(new RealTimeMessage('uyuyyuy this is a sample broadcast'));
// });

Route::get('notifications',[HomeController::class,'notifications'])->name('notifications');
Route::get('markAsRead/{id}',[HomeController::class,'markAsRead'])->name('markAsRead');
Route::get('checkNotificationDetails/{id}',[HomeController::class,'checkNotificationDetails'])->name('checkNotificationDetails');
Route::get('findatutor',[HomeController::class,'findatutor'])->name('findatutor');
Route::get('tutor-details/{id}',[HomeController::class,'tutordetails'])->name('tutordetails');
Route::get('index/slots/search', [SlotBookingController::class, 'indexslotsearch'])->name('index.slots.search');
Route::get('blogs', [HomeController::class, 'indexblogs'])->name('blogs');
Route::get('blogs/{id}', [HomeController::class, 'indexblogsdetails'])->name('blog_details');
Route::post('toptutorsearch',[HomeController::class, 'toptutorsearch'])->name('toptutorsearch');
Route::post('advancesearch',[HomeController::class, 'advancesearch'])->name('advancesearch');
Route::get('reviews',[HomeController::class,'reviewslist'])->name('reviews_list');


// Changed to new UI for Students
Route::get('/student/register', [HomeController::class, 'std_tutor_registration'])->name('std_tutor_registration');
Route::post('/student/register',[HomeController::class,'student_tutor_registration_form'])->name('student_tutor_registration_form');
Route::get('/student/login', [HomeController::class, 'std_login'])->name('studentlogin');
Route::get('/student/mobile-verify',[HomeController::class,'student_mobile_verify'])->name('student_mobile_verify');
Route::post('/student/mobile-verify',[HomeController::class,'verify_student_mobile'])->name('verify_student_mobile');
// Credentials must never be sent via GET (they end up in the URL).
Route::post('/student-login',[HomeController::class, 'userLogin'])->name('userLogin');
// Backward compatibility for older GET forms/links.
Route::get('/student-login', function () {
    return redirect()->route('studentlogin');
});
Route::post('/forget-password',[HomeController::class, 'forget_password'])->name('forget_password');
Route::get('reset-password/{token}', [HomeController::class, 'reset_password_form'])->name('reset.password.get');
// Handle reset
Route::post('/reset-password/reset_password_submit', [HomeController::class, 'reset_password_submit'])->name('reset.password');

Route::get('/free-trial-class/student-login/{id}',[HomeController::class, 'free_trial_class_student_login_form'])->name('student_login_trial');
Route::post('/free-trial-class-student-login',[HomeController::class, 'free_trial_class_student_login'])->name('student_login_trial_post');

Route::get('/enroll-class/student-login/{id}',[HomeController::class, 'enroll_class_student_login_form'])->name('student_login_enroll');
Route::post('/enroll-class-student-login',[HomeController::class, 'enroll_class_student_login'])->name('student_login_enroll_post');

Route::post('tutor-search-guest', [TutorSearchController::class, 'tutorsindexsearch'])->name('guest.tutorsindexsearch');
Route::post('tutor-dashboard-search',[TutorSearchController::class, 'tutorsdashboardsearch'])->name('student.tutorsdashboardsearch');

// Changed to new UI for Tutors
Route::get('/tutor/register', [HomeController::class, 'ttr_registration'])->name('ttr_registration');
Route::post('/tutor/register',[HomeController::class,'tutor_registration_form'])->name('tutor_registration_form');
Route::get('/tutor/login', [HomeController::class, 'ttr_login'])->name('tutorlogin');
Route::get('/tutor/mobile-verify',[HomeController::class,'tutor_mobile_verify'])->name('tutor_mobile_verify');
Route::post('/tutor/mobile-verify',[HomeController::class,'verify_tutor_mobile'])->name('verify_tutor_mobile');
Route::get('/tutor-login',[HomeController::class, 'tutor_login'])->name('tutor_login');


// Tutor search in index page
Route::post('tutorsearch', [TutorSearchController::class, 'tutorsearchindex'])->name('tutorsearchindex');


Route::get("logout", [HomeController::class, "logout"])->name("logout");


Route::post('fetchsubjects', [CommonController::class, 'fetchsubjects'])->name('fetchsubjects');
Route::post('fetchtutorsubjects', [CommonController::class, 'fetchtutorsubjects'])->name('fetchtutorsubjects');
Route::post('fetchtopics', [CommonController::class, 'fetchtopics'])->name('fetchtopics');
Route::post('fetchslottime', [CommonController::class, 'fetchslottime'])->name('fetchslottime');
Route::post('studentsbyclass', [CommonController::class, 'studentsbyclass'])->name('studentsbyclass');
Route::post('batchbysubject', [CommonController::class, 'batchbysubject'])->name('batchbysubject');
Route::post('studentsbybatch', [CommonController::class, 'studentsbybatch'])->name('studentsbybatch');
Route::post('fetchtutors', [CommonController::class, 'fetchtutors'])->name('fetchtutors');
Route::get('subjects',[SubjectController::class,'allsubjects'])->name('all-subjects');

// Create Jitsi Meeting
Route::get('/jitsi', [JitsiController::class, 'index']);

// Jitsi Webhook for automatic recording processing
Route::post('/webhook/jitsi/recording', [JitsiWebhookController::class, 'handleWebhook'])->name('webhook.jitsi.recording');

// Jibri HTTP API routes (bypass buggy XMPP in Jibri 8.0)
Route::prefix('jibri')->group(function () {
    Route::post('/start-recording', [JibriRecordingController::class, 'startRecording'])->name('jibri.start');
    Route::post('/stop-recording', [JibriRecordingController::class, 'stopRecording'])->name('jibri.stop');
    Route::get('/status', [JibriRecordingController::class, 'getStatus'])->name('jibri.status');
});

// Test page for Jibri recording (remove in production)
Route::get('/test-recording', function () {
    return view('test-recording');
});
// Route::get('oauth2callback', [GoogleCalendarController::class,'oauthCallback'])->name('oauth2callback');
Route::get('tutorprofile/{id}', [TutorSearchController::class, 'teacherprofile'])->name('tutorprofile');
Route::get('/tutor/dashboard/oauth2callback', [GoogleCalendarController::class, 'oauth2callbackdemo']);
Route::get('allsubjects',[HomeController::class,'allsubjects'])->name('allsubjectindex');


Route::get('/faqs', [CmsController::class, 'index'])->name('faqs');
Route::get('/howitworks', [CmsController::class, 'howitworks'])->name('howitworks');
Route::get('/why-choose-us', [CmsController::class, 'whychooseus'])->name('whychooseus');
Route::get('/aboutus', [CmsController::class, 'aboutus'])->name('aboutus');
Route::get('/contact', [CmsController::class, 'contact'])->name('contact');
Route::post('/contact', [CmsController::class, 'contactsave'])->name('contactsave');
Route::get('/privacypolicy', [CmsController::class, 'privacypolicy'])->name('privacypolicy');
Route::get('/refundpolicy', [CmsController::class, 'refundpolicy'])->name('refundpolicy');
Route::get('/termsandconditions', [CmsController::class, 'termsandconditions'])->name('termsandconditions');

Route::get('password-change-confirmation', function(){
    return view('front-cms/password-change-confirmation');
})->name('password.change.confirmation');

Route::get('/listen', function(){
    return view('listen');
});

Route::get('/.well-known/pki-validation/{filename}', function ($filename) {
    $path = public_path('.well-known/pki-validation/' . $filename);

    if (File::exists($path)) {
        return response()->file($path);
    }

    abort(404);
});

Route::get('/debug-cache', function () {
    Cache::put('test_key', true, 300);
    return Cache::has('test_key') ? 'CACHE WORKING' : 'CACHE FAIL';
});



//************************************************ Student  Routes ************************************************
Route::group(['prefix' => 'student', 'middleware' => ['StudentAuthenticate']], function () {


    // Route::get('/payment/{order_id}', [TutorSearchController::class, 'paymentPage'])->name('payment.checkout');
    // Route::post('/stripe/webhook', [TutorSearchController::class, 'handleStripeWebhook']);
    // Route::get('/payment/success/{order_id}', [TutorSearchController::class, 'paymentSuccess'])->name('payment.success');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('student.dashboard');
    Route::get('notifications', [DashboardController::class, 'notificationslist'])->name('student.notifications');
    // student profile
    Route::get('profile', [StudentProfileController::class, 'index'])->name('student.profile');
    Route::get('profileupdate/{id}', [StudentProfileController::class, 'edit'])->name('student.profileupdate');
    Route::post('updateprofiledata', [StudentProfileController::class, 'updateprofiledata'])->name('student.updateprofiledata');
    // Route::post('updateprofilepic', [StudentProfileController::class, 'profilepicupdate'])->name('student.profilepicupdate');
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
    Route::get('completed-classes-search', [ClassController::class, 'studentCompletedclasssearch'])->name('student.completed-classes-search');

    // Feedback by Student
    Route::post('feedback/submit',[TutorreviewsController::class,'feedbacksubmitstudent'])->name('student.feedback.submit');
    // Feedback by tutor
    Route::get('myfeedback', [TutorreviewsController::class, 'studentfeedbacklist'])->name('student.myfeedback');
    Route::get('trialsuccess', [DemoListController::class, 'trialsuccess'])->name('student.trialsuccess');
    Route::get('enrollsuccess', [TutorSearchController::class, 'enrollsuccess'])->name('student.enrollsuccess');
    // Message By Student
    Route::get('messages', [MessagesController::class, 'messagesbystudent'])->name('student.messages');
    Route::get('adminmessages', [MessagesController::class, 'messagesbystudentadmins'])->name('student.messages.admins');
    Route::get('adminmessages/{id}', [MessagesController::class, 'messagesbystudentadminmessages'])->name('student.messages.adminmessages');
    Route::get('adminmessagesload/{id}', [MessagesController::class, 'messagesbystudentadminmessagesload'])->name('student.messages.adminmessagesload');
    Route::get('tutormessages', [MessagesController::class, 'messagesbystudenttutor'])->name('student.messages.tutor');
    Route::get('tutormessages/{id}', [MessagesController::class, 'messagesbystudenttutormessages'])->name('student.messages.tutormessages');
    Route::get('tutormessagesload/{id}', [MessagesController::class, 'messagesbystudenttutormessagesload'])->name('student.messages.tutormessagesload');
    Route::post('sendmessage', [MessagesController::class, 'messagesentbystudent'])->name('student.messages.send');
    Route::get('chat-presence-status', [MessagesController::class, 'chatPresenceStatusStudent'])->name('student.chat.presence.status');
    Route::post('chat-presence-auth', [MessagesController::class, 'chatPresenceAuth'])->name('student.chat.presence.auth');
    // Assignments
    Route::get('assignments',[AssignmentsController::class,'studentassignmentslist'])->name('student.assignments.list');
    Route::post('assignments/upload',[AssignmentsController::class,'studentassignmentsupload'])->name('student.assignments.upload');
    Route::post('assignments-search',[AssignmentsController::class,'studentassignmentsSearch'])->name('student.assignments.search');
    // Student Fees/Payments
    Route::get('studentpayments', [PaymentsController::class, 'studentpayments'])->name('student.studentpayments');
    Route::get('studentpayments-search', [PaymentsController::class, 'studentpaymentsSearch'])->name('student.payments-search');
    // Online tests/exams
    Route::get('exams', [OnlineTestController::class, 'studentexams'])->name('student.exams');
    Route::get('exams-search', [OnlineTestController::class, 'studentexamsSearch'])->name('student.exams-search');
    Route::get('taketest/{id}', [OnlineTestController::class, 'taketest'])->name('student.taketest');
    Route::get('taketest-subjective/{id}', [OnlineTestController::class, 'taketestsubjective'])->name('student.taketest.subjective');
    Route::get('exam/report/{id}', [OnlineTestController::class, 'testreport'])->name('student.test.report');
    Route::post('/save-responses', [OnlineTestController::class, 'saveResponses'])->name('student.save.responses');
    Route::post('/save-subjective-responses', [OnlineTestController::class, 'saveSubjectiveResponses'])->name('student.save.subjective-responses');
    Route::post('storeSubjectiveDataInTemporaryTable', [OnlineTestController::class, 'storeSubjectiveDataInTemporaryTable'])->name('student.storeSubjectiveDataInTemporaryTable');
    Route::post('getAnswerFromSubjectiveTempTable', [OnlineTestController::class, 'getAnswerFromSubjectiveTempTable'])->name('student.getAnswerFromSubjectiveTempTable');
    // Reports
    Route::get('attendance-reports',[ClassController::class,'student_attendance_report'])->name('student.attendance.report');
    Route::get('class-reports',[ClassController::class,'student_class_report'])->name('student.class.report');
    // My Favourites
    Route::get('addfav/{id}',[MyFavouriteController::class,'addfav'])->name('student.addfav');

    //************************************************ Stripe Routes *********************************************
    Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});

});


require __DIR__ . '/custom/parent.php';
require __DIR__ . '/custom/admin.php';
require __DIR__ . '/custom/tutor.php';



