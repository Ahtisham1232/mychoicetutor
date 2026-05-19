<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\student\DashboardController;
use App\Http\Controllers\student\StudentProfileController;
use App\Http\Controllers\student\TutorSearchController;
use App\Http\Controllers\student\DemoListController;
use App\Http\Controllers\student\SubjectsController;
use App\Http\Controllers\student\MyLearningController;
use App\Http\Controllers\admin\ClassController;
use App\Http\Controllers\ZoomClassesController;
use App\Http\Controllers\TutorreviewsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\admin\OnlineTestController;
use App\Http\Controllers\MyFavouriteController;
use App\Http\Controllers\StripePaymentController;



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
    Route::get('liveclass/join/update', [ZoomClassesController::class, 'liveclassjoinupdate'])->name('tutor.liveclass.join.update');

    // completed classes
    Route::get('completed-classes', [ClassController::class, 'studentCompletedclass'])->name('student.completed-classes');
    Route::get('completed-classes-search', [ClassController::class, 'studentCompletedclasssearch'])->name('student.completed-classes-search');

    // Feedback by Student
    Route::post('feedback/submit', [TutorreviewsController::class, 'feedbacksubmitstudent'])->name('student.feedback.submit');
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
    Route::get('assignments', [AssignmentsController::class, 'studentassignmentslist'])->name('student.assignments.list');
    Route::post('assignments/upload', [AssignmentsController::class, 'studentassignmentsupload'])->name('student.assignments.upload');
    Route::post('assignments-search', [AssignmentsController::class, 'studentassignmentsSearch'])->name('student.assignments.search');
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
    Route::get('attendance-reports', [ClassController::class, 'student_attendance_report'])->name('student.attendance.report');
    Route::get('class-reports', [ClassController::class, 'student_class_report'])->name('student.class.report');
    // My Favourites
    Route::get('addfav/{id}', [MyFavouriteController::class, 'addfav'])->name('student.addfav');

    //************************************************ Stripe Routes *********************************************
    Route::controller(StripePaymentController::class)->group(function () {
        Route::get('stripe', 'stripe');
        Route::post('stripe', 'stripePost')->name('stripe.post');
    });
});
