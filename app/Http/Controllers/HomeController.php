<?php

namespace App\Http\Controllers;

use App\Events\RealTimeMessage;
use App\Helpers\CommonHelper;
use App\Mail\SendMail;
use App\Models\Blogs;
use App\Models\classes;
use App\Models\country;
use App\Models\Notification;
use App\Models\SlotBooking;
use App\Models\studentprofile;
use App\Models\studentregistration;
use App\Models\subjects;
use App\Models\tutorachievements;
use App\Models\tutorprofile;
use App\Models\tutorregistration;
use App\Models\tutorreviews;
use App\Models\tutorsubjectmapping;
// use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    // public function deepesh(){
    //     event(new \App\Events\TestNotification('This is testing data'));

    // }
    public function index()
    {

        // $tutors = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject',DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'))
        //     ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        //     ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
        //     ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        //     ->get();
        $tutors = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            'tutorprofiles.profile_pic',
            // Limit subjects to a maximum of two
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", "), ",", 2) as subject'),
            // Round avg_rating to one decimal place
            DB::raw('ROUND((SELECT AVG(tutorreviews.ratings) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id), 1) AS avg_rating'),
            DB::raw('(SELECT COUNT(tutorreviews.id) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id) AS total_reviews'),
            DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
            DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
        )
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id') // Adding join for zoom_classes
            ->where('tutorregistrations.is_active', 1)
            //    ->orderby('tutorregistrations.created_at','desc')
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.profile_pic'
            )
            ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
            ->get();
        //   dd($tutors->toArray());
        $tutorlists = tutorprofile::select(
            'tutorprofiles.id',
            'tutorprofiles.tutor_id',
            'classes.name as class_name',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            'tutorprofiles.rate as rateperhour',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            'tutorprofiles.profile_pic',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('SUM(ratings) / COUNT(ratings) AS starrating, COUNT(DISTINCT topics.name) as total_topics'),
            'tutorsubjectmappings.id as sub_map_id',
            DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done')
        )
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.is_active', '1')
            ->groupby('tutorprofiles.id', 'tutorprofiles.tutor_id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.intro_video_link', 'tutorprofiles.qualification', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();
        // dd($tutorlists);
        // Subject lists with category
        // $subjectlists = DB::table('subjects')
        //     ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
        //     ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
        //     ->where('subjects.is_active', 1)
        //     ->orderBy('subjectcategories.name')
        //     ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        // $subjects = Subjects::where('is_active', 1)->get();
        $subjects = Subjects::getUniqueSubjects();
        $countries = Country::where('is_active', 1)->get();
        $classes = classes::all('id', 'name');
        $blogs = Blogs::select('*')->where('is_active', 1)->orderby('created_at')->get();


        // Subject Categories with subjects count
        // $subjectcategories = DB::table('subjectcategories')
        //     ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
        //     ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
        //     ->where('subjectcategories.is_active', 1)
        //     ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
        //     ->get();

        $reviews = tutorreviews::select('tutorreviews.*', 'subjects.name as subjectname', 'tutorregistrations.name as tutorname', 'studentregistrations.name as studentname')
            ->leftJoin('subjects', 'subjects.id', 'tutorreviews.subject_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorreviews.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', 'tutorreviews.student_id')
            ->where('tutorreviews.ratings', '>', 3)->get();

        // dd($subjectcategories);
        return view('front-cms.index', get_defined_vars());
    }
    public function indexblogs()
    {
        $blogs = Blogs::select('*')->where('is_active', 1)->get();
        return view('front-cms.blogs', compact('blogs'));
    }
    public function indexblogsdetails($id)
    {
        $blog = Blogs::select('*')->where('id', $id)->where('is_active', 1)->first();
        return view('front-cms.blogdetails', compact('blog'));
    }
    private function baseTutorQuery()
    {
        return tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            'tutorprofiles.profile_pic',
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", ") as subject'),
            DB::raw('SUM(tutorreviews.ratings) / COUNT(tutorreviews.id) AS starrating'),
            DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
            DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
        )
        ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
        ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
        ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
        ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
        ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
        ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id')
        ->where('tutorregistrations.is_active', 1)
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.profile_pic'
            )
        ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC');
    }

    public function toptutorsearch(Request $request)
    {
        $subjectid = $request->subject;
        $classid = $request->grade;

        $classes = classes::all('id', 'name');

        $tutors = $this->baseTutorQuery()
        ->when($subjectid, function($q) use ($subjectid) {
            $subjectName = Subjects::where('id', $subjectid)->value('name');
            $q->whereIn('subjects.id', 
                Subjects::where('name', $subjectName)->pluck('id')
            );
        })
        ->when($classid, fn($q) => $q->where('classes.id', $classid))
        ->get();
        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        // dd($tutors->toArray());

        return view('front-cms.findatutor', get_defined_vars());
    }

    public function advancesearch(Request $request)
    {
        // dd($request->all());
        $tutorname = $request->name;
        $subjectid = $request->subject;
        $classid = $request->grade;
        $minPrice = $request->tminprice;
        $maxPrice = $request->tmaxprice;
        $classes = classes::all('id', 'name');
         
        $tutors = $this->baseTutorQuery()
        // ->when($subjectid, fn($q) => $q->where('subjects.id', $subjectid))
        // If the biology subject filter is applied, then we will fetch all the subject of biology
        ->when($subjectid, function($q) use ($subjectid) {
            $subjectName = Subjects::where('id', $subjectid)->value('name');
            $q->whereIn('subjects.id', 
                Subjects::where('name', $subjectName)->pluck('id')
            );
        })
        ->when($classid, fn($q) => $q->where('classes.id', $classid))
        ->when($tutorname, fn($q) => $q->where('tutorprofiles.name', 'like', "%$tutorname%"))
        // ->when($minPrice, fn($q) => $q->havingRaw('rateperhour >= ?', [$minPrice]))
        // ->when($maxPrice, fn($q) => $q->havingRaw('rateperhour <= ?', [$maxPrice]))
        ->when($minPrice, function ($q) use ($minPrice) {
        $q->havingRaw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) >= ?', [$minPrice]);
        })
        ->when($maxPrice, function ($q) use ($maxPrice) {
            $q->havingRaw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) <= ?', [$maxPrice]);
        })
        ->get();
        // dd($tutors->toArray());

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();
        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());
    }

    public function allsubjects()
    {
        $classes = classes::all('id', 'name');

        $tutors = tutorprofile::select(
            'tutorsubjectmappings.id as submapid',
            'tutorprofiles.name',
            'tutorprofiles.profile_pic',
            'subjects.name as subject',
            'classes.name as className',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating')
        )
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'tutorprofiles.name', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Tutors List
        $tutorlists = tutorprofile::select(
            'tutorprofiles.id',
            'classes.name as class_name',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            'tutorprofiles.profile_pic',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('SUM(ratings) / COUNT(ratings) AS starrating, COUNT(DISTINCT topics.name) as total_topics'),
            'tutorsubjectmappings.id as sub_map_id',
            DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done')
        )
            ->join('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.intro_video_link', 'tutorprofiles.qualification', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();
        // dd($tutorlists);
        // Subject lists with category
        // $subjectlists = DB::table('subjects')
        //     ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
        //     ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
        //     ->where('subjects.is_active', 1)
        //     ->orderBy('subjectcategories.name')
        //     ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // Subject Categories with subjects count
        // $subjectcategories = DB::table('subjectcategories')
        //     ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
        //     ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
        //     ->where('subjectcategories.is_active', 1)
        //     ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
        //     ->get();

        // dd( ($tutors));
        return view('front-cms.allsubjects', get_defined_vars());
        // return view('front-cms.index', compact('classes'));
    }
    public function findatutor()
    {
        $tutors = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            'tutorprofiles.profile_pic',
            // Limit subjects to a maximum of two
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", "), ",", 2) as subject'),
            // Round avg_rating to one decimal place
            DB::raw('ROUND((SELECT AVG(tutorreviews.ratings) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id), 1) AS avg_rating'),
            DB::raw('(SELECT COUNT(tutorreviews.id) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id) AS total_reviews'),
            DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
            DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
        )
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id') // Adding join for zoom_classes
            ->where('tutorregistrations.is_active', 1)
            // ->orderby('tutorregistrations.created_at','desc')
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.profile_pic'
            )
            ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
            ->get();


        // $subjectlists = DB::table('subjects')
        //     ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
        //     ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
        //     ->where('subjects.is_active', 1)
        //     ->orderBy('subjectcategories.name')
        //     ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        $subjects = Subjects::where('is_active', 1)->get();
        $countries = Country::where('is_active', 1)->get();
        // Subject Categories with subjects count
        // $subjectcategories = DB::table('subjectcategories')
        //     ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
        //     ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
        //     ->where('subjectcategories.is_active', 1)
        //     ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
        //     ->get();

        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());
        // return view('front-cms.index', compact('classes'));
    }

    public function tutordetails($id)
    {

        $tutorpd = tutorprofile::select(
            'tutorprofiles.*',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
        )
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutorsubjectmappings.tutor_id', '=', $id)
            ->first();

        if ($tutorpd) {
            $achievement = tutorachievements::select('*')->where('tutor_id', '=', $tutorpd->tutor_id)->get();

            // Fetch detailed reviews for the tutor
            $reviews = tutorreviews::select(
                'tutorreviews.id',
                'tutorreviews.name',
                'tutorreviews.ratings',
                'studentprofiles.name as student_name',
                'studentprofiles.profile_pic as student_pic',
                'tutorreviews.subject_id',
                'tutorreviews.tutor_id',
                'subjects.name as subject'
            )
                ->leftJoin('subjects', 'subjects.id', '=', 'tutorreviews.subject_id')
                ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'tutorreviews.student_id')
                ->where('tutorreviews.tutor_id', '=', $tutorpd->tutor_id)
                ->get();

            // dd($reviews);
        }

        if (!$tutorpd) {
            // return view('front-cms.tutordetails')->with('fail', 'Something went wrong');
            return view('front-cms.tutor-not-found')->with('fail', 'Tutor has not completed his profile yet.');

        }
        $subjects = tutorSubjectMapping::select('subjects.name as subject_name')
            ->join('subjects', 'subjects.id', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->groupBy('subjects.name')
            ->get();

        // dd($reviews);
        $averagereview = tutorreviews::select(
            DB::raw('ROUND(AVG(tutorreviews.ratings), 1) as avg_rating')
        )
            ->where('tutorreviews.tutor_id', $id)
            ->first();


        $averagecount = tutorreviews::where('tutorreviews.tutor_id', $id)->count();
        $totalStudents = SlotBooking::where('tutor_id', $id)
            ->select('student_id') // Only select student_id
            ->distinct() // Ensure unique students
            ->get()->count();
        // dd($totalStudents);
        $primarysubjects = tutorSubjectMapping::select('tutorsubjectmappings.*', 'subjects.name as subject_name')
            ->join('subjects', 'subjects.id', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->first();

        $othertutors = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.headline',
            'tutorprofiles.qualification as tutor_qualification',
            'tutorprofiles.intro_video_link',
            'tutorprofiles.experience',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            'tutorprofiles.profile_pic',
            // Limit subjects to a maximum of two
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", "), ",", 2) as subject'),
            // Round avg_rating to one decimal place
            DB::raw('ROUND((SELECT AVG(tutorreviews.ratings) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id), 1) AS avg_rating'),
            DB::raw('(SELECT COUNT(tutorreviews.id) FROM tutorreviews WHERE tutorreviews.tutor_id = tutorprofiles.tutor_id) AS total_reviews'),
            DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
            DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
        )
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id') // Adding join for zoom_classes
            ->where('tutorsubjectmappings.subject_id', '=', $primarysubjects->subject_id)
            ->where('tutorprofiles.tutor_id', '!=', $id)
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.profile_pic'
            )
            ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
            ->get();

        // dd($othertutors);

        return view('front-cms.tutordetails', compact('tutorpd', 'achievement', 'reviews', 'subjects', 'averagecount', 'averagereview', 'totalStudents', 'othertutors', 'primarysubjects'));
    }

    public function registration(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->id == "1") {
                $request->validate([
                    'studentmobile' => 'required|min:4|max:11',
                    'class' => 'required',
                ]);

                $user = new studentregistration();
                $user->mobile = $request->studentmobile;
                $user->role_id = "3";
                $user->class_id = $request->class;
                $user->parent_password = Hash::make($request->studentmobile);
                // $user->is_active = "1";
            } else {
                $request->validate([
                    'tutormobile' => 'required|min:4|max:11',
                ]);

                $user = new tutorregistration();
                $user->mobile = $request->tutormobile;
                $user->role_id = "2";
                $user->is_active = "0";
            }
            $request->validate([
                'name' => 'required',
                'email' => 'email|required',
                'password' => 'min:4|required_with:confirmpassword|same:confirmpassword',
                'confirmpassword' => 'min:4',

            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->is_active = "0";
            $user->password = Hash::make($request->password);

            $res = $user->save();

            if ($res) {
                DB::commit();
                return back()->with('success', 'Registration successfull');
                // return redirect('student/dashboard');
            } else {
                DB::rollBack();
                return back()->with('fail', 'Registration failed');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return back()->with('fail', 'Registration failed. Please try again later.');
        }
    }

    // Login Controller Code

    public function std_login()
    {
        return view('front-cms.login');
    }

    public function userLogin(Request $request)
    {

        $request->validate([
            'mobile' => 'required|numeric|digits_between:7,20',
            'country_code' => 'required|numeric',
            'password' => 'required',
            'loginAs' => 'required',
        ]);
        // dd($request->all());
        // Format the input to match your E.164 database entries
        $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
        $loginAs = $request->loginAs === 'parents' ? 'parent' : $request->loginAs;

        if ($loginAs == 'student') {
            $user = studentregistration::where('mobile', $fullMobile)->first();
            // dd($user->toArray());
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Student');
                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            // if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('tutor/dashboard')]);
                            return redirect('tutor/dashboard');
                        case 3:
                            // if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
                            return redirect('student/dashboard');
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
                // return back()->with('fail', 'Password does not match');
                return back()->withErrors(['password' => 'Password does not match'])->withInput();
            } else {
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
                // return back()->with('fail', 'Mobile No. Not Registered');
                return back()->withErrors(['mobile' => 'Mobile No. Not Registered'])->withInput();
            }
        }
        if ($loginAs == 'parent') {
               // Format the mobile number to E.164 (same as stored during registration)
            $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
            // Find the student by mobile
            $user = studentregistration::where('mobile', $fullMobile)->first();

            if ($user) {
                if (Hash::check($request->password, $user->parent_password)) {
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Parent');
                    // if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
                    return redirect('student/dashboard');
                }
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
                // return back()->with('fail', 'Password does not match');
                                return back()->withErrors(['password' => 'Password does not match'])->withInput();
            } else {
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
                // return back()->with('fail', 'Mobile No. Not Registered');
                return back()->withErrors(['mobile' => 'Mobile No. Not Registered'])->withInput();
            }
        }
        if ($loginAs == 'tutor') {
            $user = tutorregistration::where('mobile', $fullMobile)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);

                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            // if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('tutor/dashboard')]);
                            return redirect('tutor/dashboard');
                        case 3:
                            // if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
                            return redirect('student/dashboard');
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
                // return back()->with('fail', 'Password does not match');
                                return back()->withErrors(['password' => 'Password does not match'])->withInput();
            } else {
                // if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
                // return back()->with('fail', 'Mobile No. Not Registered');
                return back()->withErrors(['mobile' => 'Mobile No. Not Registered'])->withInput();
            }
        }

        return back()->with('fail', 'Invalid login type selected')->withInput();
    }
    
    public function free_trial_class_student_login_form($id)
    {
        $tutorid = $id;
        return view('front-cms.trial_class_login', compact('tutorid'));
    }
    public function free_trial_class_student_login(Request $request)
    {

        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
            'loginAs' => 'required',
        ]);
        $loginAs = $request->loginAs === 'parents' ? 'parent' : $request->loginAs;
        if ($loginAs == 'student') {

            $mobile = $request->mobile;
            $countryCode = $request->country_code;
            $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
                    
            $user = studentregistration::where('mobile', $fullMobileWithPlus)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Student');
                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            if ($request->tutorid) {
                                return redirect("student/tutorprofile/{$request->tutorid}");
                            } else {
                                return redirect('student/dashboard');
                            }
                            break;
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
        if ($loginAs == 'parent') {
            $mobile = $request->mobile;
            $countryCode = $request->country_code;
            $fullMobile = CommonHelper::ensureE164($mobile, $countryCode);
            // Find the student by mobile
            $user = studentregistration::where('mobile', $fullMobile)->first();

            if ($user) {
                if (Hash::check($request->password, $user->parent_password)) {
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Parent');
                    return redirect('student/dashboard');
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
        if ($loginAs == 'tutor') {

            $mobile = $request->mobile;
            $countryCode = $request->country_code;
            $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
                    
            $user = tutorregistration::where('mobile', $fullMobileWithPlus)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);

                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            if ($request->tutorid) {
                                return redirect("student/tutorprofile/{$request->tutorid}");
                            } else {
                                return redirect('student/dashboard');
                            }
                            break;
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
    }

    public function enroll_class_student_login_form($id)
    {
        $tutorid = $id;
        return view('front-cms.enroll_class_login', compact('tutorid'));
    }
    public function enroll_class_student_login(Request $request)
    {

        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
            'loginAs' => 'required',
        ]);
        $loginAs = $request->loginAs === 'parents' ? 'parent' : $request->loginAs;
        if ($loginAs == 'student') {
            $mobile = $request->mobile;
            $countryCode = $request->country_code;
            $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
                    
            $user = studentregistration::where('mobile', $fullMobileWithPlus)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Student');
                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            if ($request->tutorid) {
                                return redirect("student/enrollnow/{$request->tutorid}");
                            } else {
                                return redirect('student/dashboard');
                            }
                            break;
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
        if ($loginAs == 'parent') {
            $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
            // Find the student by mobile
            $user = studentregistration::where('mobile', $fullMobile)->first();


            if ($user) {
                if (Hash::check($request->password, $user->parent_password))  {
                    $request->session()->put('userid', $user);
                    $request->session()->put('usertype', 'Parent');
                    return redirect('student/dashboard');
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
        if ($loginAs == 'tutor') {
            $mobile = $request->mobile;
                $countryCode = $request->country_code;
                $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
                $user = tutorregistration::where('mobile', $fullMobileWithPlus)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    //  event(new Registered($user));

                    $user_role = Auth::user();
                    // dd($user->role_id);
                    $request->session()->put('userid', $user);

                    switch ($user->role_id) {
                        case 1:
                            echo "Admin - Under development";
                            dd($user->role_id);
                            break;
                        case 2:
                            return redirect('tutor/dashboard');
                        case 3:
                            return redirect('student/dashboard');
                        case 4:
                            echo "Parent";
                            dd($user->role_id);

                            break;
                    }
                    // return redirect(RouteServiceProvider::HOME);
                }
                return back()->with('fail', 'Password does not match');
            } else {
                return back()->with('fail', 'Mobile No. Not Registered');
            }
        }
    }

    // Logout Controller
    public function logout()
    {

        if (session()->has('userid')) {
            session()->pull('userid');
            return redirect('/');
        } else {
            return redirect('/');
        }
    }

    // Users Calls (Tutor, Student, Parent)
    // public function userLogin(Request $request)
    // {

    //     $request->validate([
    //         'mobile' => 'required|numeric|digits_between:7,20',
    //         'country_code' => 'required|numeric',
    //         'password' => 'required',
    //         'loginAs' => 'required',
    //     ]);
    //     // dd($request->all());
    //     // Format the input to match your E.164 database entries
    //     $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);

    //     if ($request->loginAs == 'student') {
    //         // dd('i am in student logi');
    //         $user = studentregistration::where('mobile', $fullMobile)->first();
    //         // dd($user->toArray());
    //         if ($user) {
    //             if (Hash::check($request->password, $user->password)) {
    //                 // dd('inside the hash');
    //                 //  event(new Registered($user));

    //                 $user_role = Auth::user();
    //                 // dd($user->role_id);
    //                 $request->session()->put('userid', $user);
    //                 $request->session()->put('usertype', 'Student');
    //                 switch ($user->role_id) {
    //                     case 1:
    //                         echo "Admin - Under development";
    //                         dd($user->role_id);
    //                         break;
    //                     case 2:
    //                         if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('tutor/dashboard')]);
    //                         return redirect('tutor/dashboard');
    //                     case 3:
    //                         if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
    //                         return redirect('student/dashboard');
    //                     case 4:
    //                         echo "Parent";
    //                         dd($user->role_id);

    //                         break;
    //                 }
    //                 // return redirect(RouteServiceProvider::HOME);
    //             }
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
    //             return back()->with('fail', 'Password does not match');
    //         } else {
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
    //             return back()->with('fail', 'Mobile No. Not Registered');
    //         }
    //     }
    //     if ($request->loginAs == 'parent') {
    //            // Format the mobile number to E.164 (same as stored during registration)
    //         $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
    //         // Find the student by mobile
    //         $user = studentregistration::where('mobile', $fullMobile)->first();

    //         if ($user) {
    //             if (Hash::check($request->password, $user->parent_password)) {
    //                 $request->session()->put('userid', $user);
    //                 $request->session()->put('usertype', 'Parent');
    //                 if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
    //                 return redirect('student/dashboard');
    //             }
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
    //             return back()->with('fail', 'Password does not match');
    //         } else {
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
    //             return back()->with('fail', 'Mobile No. Not Registered');
    //         }
    //     }
    //     if ($request->loginAs == 'tutor') {
    //         $user = tutorregistration::where('mobile', $fullMobile)->first();
    //         if ($user) {
    //             if (Hash::check($request->password, $user->password)) {
    //                 //  event(new Registered($user));

    //                 $user_role = Auth::user();
    //                 // dd($user->role_id);
    //                 $request->session()->put('userid', $user);

    //                 switch ($user->role_id) {
    //                     case 1:
    //                         echo "Admin - Under development";
    //                         dd($user->role_id);
    //                         break;
    //                     case 2:
    //                         if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('tutor/dashboard')]);
    //                         return redirect('tutor/dashboard');
    //                     case 3:
    //                         if ($request->ajax()) return response()->json(['status' => 'success', 'redirectUrl' => url('student/dashboard')]);
    //                         return redirect('student/dashboard');
    //                     case 4:
    //                         echo "Parent";
    //                         dd($user->role_id);

    //                         break;
    //                 }
    //                 // return redirect(RouteServiceProvider::HOME);
    //             }
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Password does not match']);
    //             return back()->with('fail', 'Password does not match');
    //         } else {
    //             if ($request->ajax()) return response()->json(['status' => 'error', 'message' => 'Mobile No. Not Registered']);
    //             return back()->with('fail', 'Mobile No. Not Registered');
    //         }
    //     }
    // }


    public function forget_password(Request $request)
    {
        // 1. User Lookup Logic
        if ($request->requestAs == 'student' || $request->requestAs == 'parent') {
            $user = studentregistration::where('email', $request->email)->first();
        } else if ($request->requestAs == 'tutor') {
            $user = tutorregistration::where('email', $request->email)->first();
        } else {
            $msg = 'No User Found!';
            return $request->ajax() ? response()->json(['status' => 'error', 'message' => $msg]) : back()->with('fail', $msg);
        }

        if (!$user) {
            $msg = 'Email not found!';
            return $request->ajax() ? response()->json(['status' => 'error', 'message' => $msg]) : back()->with('fail', $msg);
        }

        // 2. Token Management
        DB::table('password_resets')->where('email', $request->email)->delete();
        $token = Str::random(64);
        $email = $request->email;

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // 3. The Mail Attempt (The problematic part)
        try {
            Mail::send('emails.forgetPassword', ['token' => $token], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password');
            });

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Token sent successfully! Check mail inbox and spam both']);
            }
            return redirect()->route('home')->with('success', 'Token sent successfully!');

        } catch (\Exception $e) {
            // This catches the SMTP/SendGrid error and returns it to your AJAX
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Mail Error: ' . $e->getMessage()
                ]);
            }
            return back()->with('fail', 'Mail Error: ' . $e->getMessage());
        }
    }

    public function reset_password_form($token)
    {
        $validToken = DB::table('password_resets')->where('token', $token)->first();

        if (!$validToken) {
            return redirect()->route('home')->with('fail', 'This password reset link is invalid or has already been used.');
        }

        return view('front-cms.forgetpassword', ['token' => $token]);
    }
    public function reset_password_submit(Request $request)
    {
        // Validate new password
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed', // expects password_confirmation
            'token'    => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Check if token exists in password_resets table
        $updatePassword = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$updatePassword) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired token!']);
            }
            return back()->with('fail', 'Invalid or expired token!');
        }

        // Try to find the user in students or tutors
        $user = studentregistration::where('email', $updatePassword->email)->first()
            ?? tutorregistration::where('email', $updatePassword->email)->first();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'User not found!']);
            }
            return back()->with('fail', 'User not found!');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token so it can't be reused
        DB::table('password_resets')
            ->where('email', $updatePassword->email)
            ->delete();

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully!',
                'redirect_url' => route('password.change.confirmation')
            ]);
        }

        return redirect()->route('password.change.confirmation');
    }

    public function std_tutor_registration()
    {
        // return view('common.student-register');
        return view('front-cms.register');
    }
    public function student_tutor_registration_form(Request $request)
    {
        try {
            // if ($request->id == "1") {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'country_code' => 'required|numeric',
                'mobile' => 'required|numeric|digits_between:6,15',
                'password' => 'required|min:8|max:50',
                'confpassword' => 'required|min:8|max:50|same:password',
                'expcheck' => 'required|accepted',
            ], [
                'name.required' => 'Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                // 'mobile.required' => 'Mobile number is required.',
                // 'mobile.min' => 'Mobile number must be at least 4 digits.',
                // 'mobile.max' => 'Mobile number must not exceed 13 digits.',
                // 'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.max' => 'Password must not exceed 50 characters.',
                'confpassword.required' => 'Confirmation password is required.',
                'confpassword.min' => 'Confirmation password must be at least 8 characters.',
                'confpassword.max' => 'Confirmation password must not exceed 50 characters.',
                'confpassword.same' => 'Password and confirmation password must match.',
                'expcheck.required' => 'You must accept the terms.',
                'expcheck.accepted' => 'The terms must be accepted.',
            ]);

            if ($request->registerAs == 'student') {
                DB::beginTransaction();

                try {
                    // Here we are setting the phone of the student according to the E.164 format
                    // by combining the country code and mobile number, and removing any leading zeros from the mobile number.
                    $mobile = $request->mobile;
                    $countryCode = $request->country_code;
                    $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
                    
                    $user = studentregistration::where('mobile', $fullMobileWithPlus)->first();

                    if ($user) {
                        DB::rollBack();
                        return back()->with('fail', 'Mobile Already Registered');
                    }

                    $user = studentregistration::where('email', '=', $request->email,)->first();
                    if ($user) {
                        DB::rollBack();
                        return back()->with('fail', 'Email Already Registered');
                    }
           
                    $user = new studentregistration();
                    $user->mobile = $fullMobileWithPlus;
                    $user->role_id = "3";
                    $user->class_id = '0';
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->is_active = "1";
                    $user->timezone = $request->timezone ?? null;
                    $user->password = Hash::make($request->password);
                    $user->parent_password = Hash::make($fullMobileWithPlus);
                    $res = $user->save();

                    if (!$res) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to save student registration.');
                    }

                    $finduser = studentregistration::select('*')->where('mobile', $fullMobileWithPlus)->first();
                    if (!$finduser) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to retrieve student registration.');
                    }

                    $studentprofile = new studentprofile();
                    $studentprofile->name = $request->name;
                    $studentprofile->mobile = $fullMobileWithPlus;
                    $studentprofile->email = $request->email;
                    $studentprofile->student_id = $finduser->id;
                    // $studentprofile->profile_pic =
                    $studentprofile->grade = 1;
                    $profileRes = $studentprofile->save();

                    if (!$profileRes) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to save student profile.');
                    }

                    // Send welcome mail
                    try {
                        $details = [
                            'name' => $request->name,
                            'mobile' => $fullMobileWithPlus,
                            'password' => $request->password,
                            'mailtype' => 1,
                        ];

                        Mail::to($request->email)->send(new SendMail($details));
                    } catch (\Exception $e) {
                        Log::error('Failed to send welcome email: ' . $e->getMessage());
                        // Don't fail registration if email fails
                    }

                    $mobile = $request->mobile;
                    $formattedDate = Carbon::now()->format('Y-m-d');
                    // Generate a random 4-digit OTP
                    $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    $username = 'BhashWAPAI';
                    $pass = '123456';
                    $sender = 'BUZWAP';
                    // $phone = '+917004920897';
                    $phone = $mobile;
                    // $phone = $res->mobile;
                    $text = 'delivery';
                    $priority = 'wa';
                    $stype = 'normal';
                    $params = $otp . ',' . $formattedDate;

                    $url = "https://bhashsms.com/api/sendmsg.php?user=$username&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

                    // Initialize Guzzle client
                    $client = new Client();

                    try {
                        // Send GET request to the URL
                        $response = $client->get($url);

                        // Get the response body
                        $responseBody = $response->getBody();

                        // You can process the response here
                        // For example, you can log the response or return it to the view
                        response()->json(['message' => 'OTP sent successfully', 'response' => $responseBody]);
                        // return view('common.tutor-mobile-verify');
                    } catch (\Exception $e) {
                        // Handle any exceptions that occur during the request
                        Log::error('OTP sending failed: ' . $e->getMessage());
                        // Don't fail registration if OTP sending fails
                    }

                    $studentRegistration = studentregistration::where('mobile', $fullMobileWithPlus)->first();
                    if ($studentRegistration) {
                        $studentRegistration->mobile_otp = '1234';
                        // $studentRegistration->mobile_otp = $otp;
                        $studentRegistration->save();
                    }

                    DB::commit();

                    $user = studentregistration::where('mobile', $fullMobileWithPlus)->first();

                    if ($user) {
                        $request->session()->put('userid', $user);
                        return redirect('student/dashboard')->with('success', 'Registration successful. Explore tutors and book classes. Best Wishes!!');
                    } else {
                        return back()->with('fail', 'Registration completed but session failed. Please login.');
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Student registration failed: ' . $e->getMessage());
                    return back()->with('fail', 'Registration failed. Please try again later.');
                }
            }
            if ($request->registerAs == 'tutor') {
                DB::beginTransaction();

                try {
                    $request->validate([
                        'name' => 'required',
                        'email' => 'required',
                        'country_code' => 'required|numeric',
                        'mobile' => 'required|numeric|digits_between:6,15',
                        'password' => 'required|min:8|max:50',
                    ]);

                     // Here we are setting the phone of the student according to the E.164 format
                    // by combining the country code and mobile number, and removing any leading zeros from the mobile number.
        
                    $mobile = $request->mobile;
                    $countryCode = $request->country_code;
                    $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);


                    $user = tutorregistration::where('email', '=', $request->email,)->first();
                    if ($user) {
                        DB::rollBack();
                        return back()->with('fail', 'Email Already Registered');
                    }

                    $user = tutorregistration::where('mobile', $fullMobileWithPlus)->first();

                    if ($user) {
                        DB::rollBack();
                        return back()->with('fail', 'Mobile Already Registered');
                    }

                    $user = new tutorregistration();
                    $user->mobile = $fullMobileWithPlus;
                    $user->role_id = "2";
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->timezone = $request->timezone ?? null;
                    $user->is_active = "0";
                    $user->password = Hash::make($request->password);

                    $res = $user->save();

                    if (!$res) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to save tutor registration.');
                    }

                    $checktutorid = tutorregistration::select('*')->where('mobile', $fullMobileWithPlus)->first();
                    if (!$checktutorid) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to retrieve tutor registration.');
                    }

                    $tprofile = new tutorprofile();
                    $tprofile->name = $request->name;
                    $tprofile->mobile = $fullMobileWithPlus;
                    $tprofile->email = $request->email;
                    $tprofile->tutor_id = $checktutorid->id;
                    $tprofile->qualification = " ";
                    $tprofile->rateperhour = 0;
                    $tprofile->admin_commission = 0;
                    $profileRes = $tprofile->save();

                    if (!$profileRes) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to save tutor profile.');
                    }

                    // Send welcome mail
                    try {
                        $details = [
                            'name' => $request->name,
                            'mobile' => $fullMobileWithPlus,
                            'password' => $request->password,
                            'mailtype' => 1,
                        ];

                        Mail::to($request->email)->send(new SendMail($details));
                    } catch (\Exception $e) {
                        Log::error('Failed to send welcome email: ' . $e->getMessage());
                        // Don't fail registration if email fails
                    }

                    $mobile = $fullMobileWithPlus;

                    $user = 'BhashWAPAI';
                    $pass = '123456';
                    $sender = 'BUZWAP';
                    // $phone = '7004920897';
                    $phone = $mobile;
                    $text = 'delivery';
                    $priority = 'wa';
                    $stype = 'normal';
                    $params = '1195,23aug2023';

                    $url = "https://bhashsms.com/api/sendmsg.php?user=$user&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

                    // Initialize Guzzle client
                    $client = new Client();

                    // OTP sending is non-critical, so we'll try but not fail registration
                    try {
                        $response = $client->get($url);
                    } catch (\Exception $e) {
                        Log::error('OTP sending failed: ' . $e->getMessage());
                        // Don't fail registration if OTP sending fails
                    }

                    $user = tutorregistration::where('mobile', '=', $mobile)->first();
                    if (!$user) {
                        DB::rollBack();
                        return back()->with('fail', 'Failed to retrieve tutor after registration.');
                    }

                    // Notification Starts here
                    try {
                        $notificationdata = new Notification();
                        $notificationdata->alert_type = 8;
                        $notificationdata->notification = $request->name . " Registered as tutor and pending for approval";
                        $notificationdata->initiator_id = $user->id;
                        $notificationdata->initiator_role = "2";
                        $notificationdata->event_id = $user->id;
                        // Sending to admin
                        $notificationdata->show_to_admin = 1;
                        $notificationdata->show_to_admin_id = 1;
                        $notificationdata->show_to_all_admin = 1;
                        $notificationdata->read_status = 0;
                        $notificationdata->save();
                        broadcast(new RealTimeMessage($notificationdata));
                    } catch (\Exception $e) {
                        Log::error('Failed to send notification: ' . $e->getMessage());
                        // Don't fail registration if notification fails
                    }

                    DB::commit();

                    $request->session()->put('userid', $user);
                    return redirect('tutor/dashboard')->with('success', 'Registration successful. Please complete your profile.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Tutor registration failed: ' . $e->getMessage());
                    return back()->with('fail', 'Registration failed. Please try again later.');
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Registration form error: ' . $e->getMessage());
            return back()->with('fail', 'An error occurred. Please try again later.');
        }
    }

    public function student_mobile_verify()
    {
        return view('common.student-mobile-verify');
    }

    public function verify_student_mobile(Request $request)
    {

        $request->validate([
            'digit1_input' => 'required',
            'digit2_input' => 'required',
            'digit3_input' => 'required',
            'digit4_input' => 'required',
            'mobile' => 'required',

        ]);
        $userotp = $request->digit1_input . $request->digit2_input . $request->digit3_input . $request->digit4_input;
        $mobile = $request->mobile;
        $countryCode = $request->country_code;
        $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
            
        $user = studentregistration::where('mobile', $fullMobileWithPlus)->first();
        if ($user && $userotp == $user->mobile_otp) {
            return view('common.student-mobile-verified')->with('success', 'OTP Verified');
        } else {

            return back()->with('fail', 'Invalid OTP. Please try again');
        }
    }

    // Tutor Calls

    public function ttr_login()
    {
        // return view('common.tutor-login');
        return view('front-cms.login');
    }

    public function tutor_login(Request $request)
    {

        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
            'country_code' => 'required'
        ]);

         $mobile = $request->mobile;
            $countryCode = $request->country_code;
            $fullMobileWithPlus = CommonHelper::ensureE164($mobile, $countryCode);
            $user = tutorregistration::where('mobile', $fullMobileWithPlus)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                //  event(new Registered($user));

                $user_role = Auth::user();
                // dd($user->role_id);
                $request->session()->put('userid', $user);

                switch ($user->role_id) {
                    case 1:
                        echo "Admin - Under development";
                        dd($user->role_id);
                        break;
                    case 2:
                        return redirect('tutor/dashboard');
                    case 3:
                        return redirect('student/dashboard');
                    case 4:
                        echo "Parent";
                        dd($user->role_id);

                        break;
                }
                // return redirect(RouteServiceProvider::HOME);
            }
            return back()->with('fail', 'Password does not match');
        } else {
            return back()->with('fail', 'Mobile No. Not Registered');
        }
    }
    public function ttr_registration()
    {

        return view('front-cms.register');
    }
    public function tutor_registration_form(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required|numeric|digits_between:6,15',
                'password' => 'required|min:8|max:50',
                'country_code' => 'required',
            ]);

            // Check for existing email
            $existingEmail = tutorregistration::where('email', '=', $request->email)->first();
            if ($existingEmail) {
                DB::rollBack();
                return back()->with('fail', 'Email Already Registered');
            }

            $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
            $existingMobile = tutorregistration::where('mobile', $fullMobile)->first();
            if ($existingMobile) {
                DB::rollBack();
                return back()->with('fail', 'Mobile Already Registered');
            }

            $user = new tutorregistration();
            $user->mobile = $fullMobile;
            $user->role_id = "2";
            $user->name = $request->name;
            $user->email = $request->email;
            $user->is_active = "0";
            $user->password = Hash::make($request->password);

            $res = $user->save();

            if (!$res) {
                DB::rollBack();
                return back()->with('fail', 'Registration failed');
            }

            // Create tutor profile
            try {
                $checktutorid = tutorregistration::select('*')->where('mobile', $mobileToStore)->first();
                if ($checktutorid) {
                    $tprofile = new tutorprofile();
                    $tprofile->name = $request->name;
                    $tprofile->mobile = $fullMobile;
                    $tprofile->email = $request->email;
                    $tprofile->tutor_id = $checktutorid->id;
                    $tprofile->qualification = " ";
                    $tprofile->rateperhour = 0;
                    $tprofile->admin_commission = 0;
                    $tprofile->save();
                }
            } catch (\Exception $e) {
                Log::error('Failed to create tutor profile: ' . $e->getMessage());
                DB::rollBack();
                return back()->with('fail', 'Registration failed. Please try again.');
            }

            DB::commit();

            $mobile = $fullMobile;
            return view('common.tutor-mobile-verify', compact('mobile'))->with('success', 'Registration successful. Please Login Now To Access More Features.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tutor registration form error: ' . $e->getMessage());
            return back()->with('fail', 'Registration failed. Please try again later.');
        }
    }

    public function tutor_mobile_verify()
    {
        $user = 'BhashWAPAI';
        $pass = '123456';
        $sender = 'BUZWAP';
        $phone = '7004920897';
        $text = 'delivery';
        $priority = 'wa';
        $stype = 'normal';
        $params = '1195,23aug2023';

        $url = "https://bhashsms.com/api/sendmsg.php?user=$user&pass=$pass&sender=$sender&phone=$phone&text=$text&priority=$priority&stype=$stype&params=$params";

        // Initialize Guzzle client
        $client = new Client();

        try {
            // Send GET request to the URL
            $response = $client->get($url);

            // Get the response body
            $responseBody = $response->getBody();

            // You can process the response here
            // For example, you can log the response or return it to the view
            return view('common.tutor-mobile-verify')->with('success', 'OTP sent successfully');
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the request
            return response()->json(['message' => 'OTP sending failed', 'error' => $e->getMessage()], 500);
        }
        // return view('common.tutor-mobile-verify');
    }

    public function verify_tutor_mobile(Request $request)
    {
        // echo "test";
        // dd();
        $request->validate([
            'digit1_input' => 'required',
            'digit2_input' => 'required',
            'digit3_input' => 'required',
            'digit4_input' => 'required',
            // 'mobile' => 'required',

        ]);
        $otp = $request->digit1_input . $request->digit2_input . $request->digit3_input . $request->digit4_input;
        echo $otp;
        // dd();

        if ($otp == '1234') {
            return view('common.tutor-mobile-verified')->with('success', 'OTP Verified');
        } else {

            return back()->with('fail', 'Invalid OTP. Please try again');
        }
    }

    // parent authentications
    public function parent_login()
    {
        return view('front-cms.login');
    }
    public function parent_login_attempt(Request $request)
    {

        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);
        $fullMobile = CommonHelper::ensureE164($request->mobile, $request->country_code);
        // Find the student by mobile
        $user = studentregistration::where('mobile', $fullMobile)->first();
        if ($user) {
            if (Hash::check($request->password, $user->parent_password)) {
                $request->session()->put('userid', $user);
                $request->session()->put('usertype', 'Parent');
                return redirect('student/dashboard');
            }
            return back()->with('fail', 'Password does not match');
        } else {
            return back()->with('fail', 'Mobile No. Not Registered');
        }
    }

    public function notifications()
    {
        $user = session('userid');
        // if no session, return empty response safely
        if (!$user) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
        // Logged In User Role
        $logged_in_role = session('userid')->role_id;

        // Notification for Admin
        if ($logged_in_role == 1) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_admin_id', session('userid')->id)
                        ->orWhere('show_to_all_admin', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_admin_id', session('userid')->id)
                        ->orWhere('show_to_all_admin', 1);
                })
                ->count();
        }
        // Notification for Tutor
        if ($logged_in_role == 2) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_tutor_id', session('userid')->id)
                        ->orWhere('show_to_all_tutor', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_tutor_id', session('userid')->id)
                        ->orWhere('show_to_all_tutor', 1);
                })
                ->count();
        }
        // Notification for Student
        if ($logged_in_role == 3) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_student_id', session('userid')->id)
                        ->orWhere('show_to_all_student', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_student_id', session('userid')->id)
                        ->orWhere('show_to_all_student', 1);
                })
                ->count();
        }
        // Notification for Parent
        if ($logged_in_role == 4) {
            $notifications = Notification::orderBy('created_at', 'desc')
                ->where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_parent_id', session('userid')->id)
                        ->orWhere('show_to_all_parent', 1);
                })
                ->leftJoin('admins', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'admins.id')
                        ->where('notifications.initiator_role', '=', 1);
                })
                ->leftJoin('tutorprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'tutorprofiles.tutor_id')
                        ->where('notifications.initiator_role', '=', 2);
                })
                ->leftJoin('studentprofiles', function ($join) {
                    $join->on('notifications.initiator_id', '=', 'studentprofiles.student_id')
                        ->where('notifications.initiator_role', '=', 3);
                })
                ->select(
                    'notifications.*',
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.name
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.name
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.fathers_name
                            ELSE NULL
                        END AS initiator_name'),
                    DB::raw('CASE
                            WHEN notifications.initiator_role = 1 THEN admins.name
                            WHEN notifications.initiator_role = 2 THEN tutorprofiles.profile_pic
                            WHEN notifications.initiator_role = 3 THEN studentprofiles.profile_pic
                            WHEN notifications.initiator_role = 4 THEN studentprofiles.profile_pic
                            ELSE NULL
                        END AS initiator_pic'),
                    DB::raw('CASE
                                WHEN notifications.initiator_role = 1 THEN "Admin"
                                WHEN notifications.initiator_role = 2 THEN "Tutor"
                                WHEN notifications.initiator_role = 3 THEN "Student"
                                WHEN notifications.initiator_role = 4 THEN "Parent"
                                ELSE NULL
                            END AS initiator_role')
                )
                ->get();
            $unreadCount = Notification::where('read_status', 0)
                ->where(function ($query) {
                    $query->where('show_to_parent_id', session('userid')->id)
                        ->orWhere('show_to_all_parent', 1);
                })
                ->count();
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {

        $updatestatus = Notification::find($id);
        $updatestatus->read_status = 1;
        $updatestatus->update();

        $notifications = Notification::orderBy('created_at', 'desc')->get();
        $unreadCount = Notification::where('read_status', 0)->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
    public function checkNotificationDetails($id)
    {
        $notificationData = Notification::find($id);
        // Check if notification exists
        if (!$notificationData) {
            return redirect()->back()->with('fail', 'Notification not found or invalid.');
        }
        // Notification Event On Chat
        if ($notificationData->alert_type == 12) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/adminmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/adminmessages/' . $notificationData->initiator_id);
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/tutormessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('/student/tutormessages/' . $notificationData->initiator_id);
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/studentmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/studentmessages/' . $notificationData->initiator_id);
                }
            }
            // Initiated by parent
            if ($notificationData->initiator_role == 4) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/studentmessages/' . $notificationData->initiator_id);
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/studentmessages/' . $notificationData->initiator_id);
                }
            }
        }
        // Notification Event On Trial Class
        if ($notificationData->alert_type == 2) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/demolist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/demolist');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/demolist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/demolist');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/demolist');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/demolist');
                }
            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Assignments
        if ($notificationData->alert_type == 3) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/assignments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/assignments');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/assignments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/assignments');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/assignments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/assignments');
                }
            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Quiz/Online Test
        if ($notificationData->alert_type == 4) {

            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/onlinetestresponseslist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/exams');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/onlinetestlist');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/exams');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/onlinetestlist');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/onlinetestresponseslist');
                }
            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Feedback
        if ($notificationData->alert_type == 5) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/feedback');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/myfeedback');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/dashboard');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/myfeedback');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/dashboard');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/feedback');
                }
            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }

        // Notification Event On Enrollment
        if ($notificationData->alert_type == 6) {

            // Initiated By Admin
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/students');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/yourtutor');
                }
            }
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/yourtutor');
                }
            }
            // Chat Initiated by student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/students');
                }
            }
            // Initiated by parent
            // if($notificationData->initiator_role == 4){
            //     if(session('userid')->role_id == 1){
            //         return redirect()->to('admin/studentmessages/'.$notificationData->initiator_id);
            //     }
            //     if(session('userid')->role_id == 2){
            //         return redirect()->to('tutor/studentmessages/'.$notificationData->initiator_id);
            //     }
            // }
        }
        // Notification Event On Slot Booking
        if ($notificationData->alert_type == 7) {

            // Slot Booked By Student
            if ($notificationData->initiator_role == 3) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/tutorslots');
                }
                if (session('userid')->role_id == 3) {
                    // return redirect()->to('student/enrollupdate/' . $notificationData->initiator_id);
                    return redirect()->to('student/classes');
                }
            }
            // Slot Booked By Student
            if ($notificationData->initiator_role == 2) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/tutorslots');
                }
                if (session('userid')->role_id == 3) {
                    // return redirect()->to('student/enrollupdate/' . $notificationData->initiator_id);
                    return redirect()->to('student/classes');
                }
            }
        }
        // dd($notificationData->alert_type);
        if ((int)$notificationData->alert_type === 9) {
            // dd($notificationData->initiator_role);
            // Slot Booked By Student
            if ($notificationData->initiator_role == 1) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/tutorslots');
                }
                if (session('userid')->role_id == 3) {
                    // return redirect()->to('student/enrollupdate/' . $notificationData->initiator_id);
                    return redirect()->to('student/classes');
                }
            }
            // Slot Booked By Student
            if ($notificationData->initiator_role == 2) {

                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/payments');
                }
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/tutorslots');
                }
                if (session('userid')->role_id == 3) {
                    // return redirect()->to('student/enrollupdate/' . $notificationData->initiator_id);
                    return redirect()->to('student/classes');
                }
            }
        }

        // Notification Event On Enrollment Approval (Tutor)
        if ((int)$notificationData->alert_type === 10) {
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 2) {
                    return redirect()->to('tutor/getclasslist');
                }
            }
        }

        // Notification Event On Enrollment Rejection (Student)
        if ((int)$notificationData->alert_type === 11) {
            if ($notificationData->initiator_role == 1) {
                if (session('userid')->role_id == 3) {
                    return redirect()->to('student/dashboard');
                }
            }
        }
        // Notification Event On Tutor Registration
        if ($notificationData->alert_type == 8) {
            // dd($notificationData->initiator_role);
            // Initiated by tutor
            if ($notificationData->initiator_role == 2) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/tutors');
                }
            }
            if ($notificationData->initiator_role == 3) {
                if (session('userid')->role_id == 1) {
                    return redirect()->to('admin/enrollment-requests');
                }
            }
        }
    }
    public function reviewslist()
    {
        $reviews = tutorreviews::select('tutorreviews.*', 'subjects.name as subjectname', 'tutorregistrations.name as tutorname', 'studentregistrations.name as studentname')
            ->leftJoin('subjects', 'subjects.id', 'tutorreviews.subject_id')
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', 'tutorreviews.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', 'tutorreviews.student_id')
            ->where('tutorreviews.ratings', '>', 3)->get();
        // dd($reviews);
        return view('front-cms.reviews', compact('reviews'));
    }
}
