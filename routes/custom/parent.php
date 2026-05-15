<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\student\DashboardController;
use App\Http\Controllers\student\DemoListController;
use App\Http\Controllers\admin\ClassController;
use App\Http\Controllers\admin\OnlineTestController;
use App\Http\Controllers\admin\PaymentsController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\student\SubjectsController;
use App\Http\Controllers\student\MyLearningController;



Route::get('/parent/login', [HomeController::class, 'parent_login'])->name('parentlogin');
Route::post('/parent/login', [HomeController::class, 'parent_login_attempt'])->name('parent.login');
//************************************************ Parent Routes ************************************************
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