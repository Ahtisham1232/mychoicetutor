<?php

use App\Events\RealTimeMessage;

use App\Http\Controllers\admin\SubjectController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JitsiController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\student\TutorSearchController;
use App\Http\Controllers\JibriRecordingController;
use App\Http\Controllers\JitsiWebhookController;
use App\Http\Controllers\CmsController;
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




require __DIR__ . '/custom/student.php';
require __DIR__ . '/custom/parent.php';
require __DIR__ . '/custom/admin.php';
require __DIR__ . '/custom/tutor.php';



