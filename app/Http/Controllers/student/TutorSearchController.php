<?php

namespace App\Http\Controllers\student;

use App\Events\RealTimeMessage;
use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\classes;
use App\Models\subjects;
use App\Models\country;
use App\Models\Notification;
use App\Models\payments\paymentdetails;
use App\Models\payments\paymentstudents;
use App\Models\SlotBooking;
use App\Models\tutorachievements;
use App\Models\tutorprofile;
use App\Models\tutorregistration;
use App\Models\tutorreviews;
use App\Models\tutorsubjectmapping;
use App\Helpers\TimezoneHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Helpers\CommonHelper;
use App\Services\TwilioWhatsAppService;
use App\Models\studentprofile;
use Illuminate\Support\Facades\Log;

class TutorSearchController extends Controller
{
    public function index()
    {
        $tutorlist = tutorprofile::select(
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
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id') // Adding join for zoom_classes
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
            ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
            ->get();

        // dd($tutorlist);

        // $subjectlist = subjects::where('is_active', 1)->get();
        $subjectlist = Subjects::getUniqueSubjects();
        $classes = classes::where('is_active', 1)->get();
        $countrylist = country::select('*')->get();
        if (!$tutorlist) {
            return view('student.searchtutor')->with('fail', 'No tutor found');
        }
        return view('student.searchtutor', get_defined_vars());
    }

    public function sorttutor($sort_value, $sort_type)
    {

        // $tutorlist = tutorprofile::select('tutorprofiles.*','subjects.name as subject','subjects.name as subject','tutorreviews.*',DB::raw('SUM(ratings) AS sum_of_1'))

        $query = tutorprofile::select('tutorprofiles.id', 'classes.name as class_name', 'tutorprofiles.experience', 'tutorprofiles.name', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'), 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings)/COUNT(ratings) AS starrating,  COUNT(DISTINCT topics.name) as total_topics'), 'tutorsubjectmappings.id as sub_map_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftjoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->leftjoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->leftjoin('tutorregistrations', 'tutorregistrations.id', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.is_active', '1')
            ->groupby('tutorprofiles.id', 'subjects.id', 'tutorprofiles.experience', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name', 'rate', 'sub_map_id');
        // ->where('teacherclassmappings.class_id', '=', session('userid')->class_id)

        if ($sort_value == "pricing") {
            if ($sort_type == "asc") {
                $query->orderBy('rate', 'ASC');
            }
            if ($sort_type == "desc") {
                $query->orderBy('rate', 'DESC');
            }
        }
        if ($sort_value == "class") {
            if ($sort_type == "asc") {
                $query->orderBy('class_name', 'ASC');
            }
            if ($sort_type == "desc") {
                $query->orderBy('class_name', 'DESC');
            }
        }
        if ($sort_value == "rating") {
            if ($sort_type == "asc") {
                $query->orderBy('starrating', 'ASC');
            }
            if ($sort_type == "desc") {
                $query->orderBy('starrating', 'DESC');
            }
        }
        if ($sort_value == "experience") {
            if ($sort_type == "asc") {
                $query->orderByRaw("CAST(SUBSTRING_INDEX(tutorprofiles.experience, ' ', 1) AS UNSIGNED) ASC");
            }
            if ($sort_type == "desc") {
                $query->orderByRaw("CAST(SUBSTRING_INDEX(tutorprofiles.experience, ' ', 1) AS UNSIGNED) DESC");
            }
        }

        $tutorlist = $query->get();
        // echo "<pre>";
        // dd($tutorlist);
        $subjectlist = subjects::where('is_active', 1)->get();
        $classes = classes::where('is_active', 1)->get();
        $countrylist = country::select('*')->get();
        if (!$tutorlist) {
            return view('student.searchtutor')->with('fail', 'No tutor found');
        }
        return view('student.searchtutor', get_defined_vars());
    }

    public function yourtutor()
    {
        $tutorlist = tutorprofile::select(
            'tutorprofiles.id',
            'tutorprofiles.tutor_id as tutor_id',
            'tutorprofiles.name',
            'tutorprofiles.profile_pic',
            DB::raw('IFNULL(SUM(tutorreviews.ratings) / COUNT(tutorreviews.ratings), 0) AS starrating'),
            DB::raw('IFNULL(ps.total_classes_purchased, 0) as total_classes_purchased'),
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) + tutorprofiles.rateperhour as rate'),
            DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name ASC SEPARATOR ", ") as subject')
        )
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->join(DB::raw('(SELECT tutor_id, SUM(classes_purchased) as total_classes_purchased
                            FROM paymentstudents
                            WHERE student_id = ' . session('userid')->id . '
                            GROUP BY tutor_id) as ps'), 'ps.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->groupBy(
                'tutorprofiles.id',
                'tutorprofiles.tutor_id',
                'tutorprofiles.name',
                'tutorprofiles.profile_pic',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'ps.total_classes_purchased'
            )
            ->get();

        return view('student.yourtutor', compact('tutorlist'));
    }
    public function tutorprofile($id)
    {
        $achievement = collect();
        $reviews = collect();

        // Fetch the tutor's profile along with the associated subject and calculated rate
        $tutorpd = tutorprofile::select(
            'tutorprofiles.*',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) + tutorprofiles.rateperhour as rate'),
            DB::raw('ROUND(COALESCE(AVG(tutorreviews.ratings), 0), 1) AS avg_rating'), // Average rating rounded to 1 decimal
            DB::raw('COUNT(tutorreviews.id) AS total_reviews') // Total reviews count
        )
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id') // Join for reviews to get ratings
            ->where('tutorsubjectmappings.tutor_id', '=', $id)
            ->groupBy(
                'tutorprofiles.tutor_id',
                'tutorprofiles.id',
                'tutorprofiles.goal',
                'tutorprofiles.name',
                'tutorprofiles.mobile',
                'tutorprofiles.secondary_mobile',
                'tutorprofiles.email',
                'tutorprofiles.qualification',
                'tutorprofiles.intro_video_link',
                'tutorprofiles.profile_pic',
                'tutorprofiles.expertise',
                'tutorprofiles.experience',
                'tutorprofiles.certification',
                'tutorprofiles.headline',
                'tutorprofiles.detail_1',
                'tutorprofiles.detail_2',
                'tutorprofiles.detail_3',
                'tutorprofiles.keywords',
                'tutorprofiles.created_at',
                'tutorprofiles.updated_at',
                'tutorprofiles.country_id',
                'tutorprofiles.gender',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.rate',
                'subjects.id',
                'subjects.name'
            )
            ->first();

        if (!$tutorpd) {
            return redirect()->back()->with('fail', 'Tutor not found');
        }

        // Fetch achievements for the tutor
        $achievement = tutorachievements::where('tutor_id', $tutorpd->tutor_id)->get();

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
        $subjects = tutorSubjectMapping::select('subjects.name as subject_name')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->groupBy('subjects.name')
            ->get();

        return view('student.tutorprofile', compact('tutorpd', 'achievement', 'reviews', 'subjects'));
    }

    public function admintutorprofile($id)
    {

        // Fetch the tutor's profile along with the associated subject and calculated rate
        $tutorpd = tutorprofile::select(
            'tutorprofiles.*',
            'subjects.id as subjectid',
            'subjects.name as subject',
            DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) + tutorprofiles.rateperhour as rate'),
            DB::raw('ROUND(COALESCE(AVG(tutorreviews.ratings), 0), 1) AS avg_rating'), // Average rating rounded to 1 decimal
            DB::raw('COUNT(tutorreviews.id) AS total_reviews') // Total reviews count
        )
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id') // Join for reviews to get ratings
            ->where('tutorregistrations.id', '=', $id)
            // ->where('tutorsubjectmappings.tutor_id', '=', $id)     
            ->groupBy('tutorprofiles.tutor_id', 'tutorprofiles.id', 'tutorprofiles.goal', 'tutorprofiles.name', 'tutorprofiles.mobile', 'tutorprofiles.secondary_mobile', 'tutorprofiles.email', 'tutorprofiles.qualification', 'tutorprofiles.intro_video_link', 'tutorprofiles.profile_pic', 'tutorprofiles.expertise', 'tutorprofiles.experience', 'tutorprofiles.certification', 'tutorprofiles.headline', 'tutorprofiles.detail_1', 'tutorprofiles.detail_2', 'tutorprofiles.detail_3', 'tutorprofiles.keywords', 'tutorprofiles.created_at', 'tutorprofiles.updated_at', 'tutorprofiles.country_id', 'tutorprofiles.gender', 'tutorprofiles.rateperhour', 'tutorprofiles.admin_commission', 'tutorprofiles.rate', 'subjects.id', 'subjects.name') // Group by tutor ID to aggregate the ratings
            ->first();
        // dd( $tutorpd);
        if ($tutorpd) {
            // Fetch achievements for the tutor
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
        }

        if (!$tutorpd) {
            return view('admin.tutorprofile')->with('fail', 'Something went wrong');
        }
        $subjects = tutorSubjectMapping::select('subjects.name as subject_name')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutor_id', $id)
            ->groupBy('subjects.name')
            ->get();

        return view('admin.tutorprofile', compact('tutorpd', 'achievement', 'reviews', 'subjects'));
    }

    public function tutoradvs(Request $request)
    {

        $classes = classes::all('id', 'name');

        $tutors = tutorprofile::select(
            'tutorsubjectmappings.id as submapid',
            'tutorprofiles.name',
            'tutorprofiles.profile_pic',
            'subjects.name as subject',
            'classes.name as className',
            'my_favourites.status as myfav',
            DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as rateperhour'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating')
        )
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            // ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('my_favourites', function ($join) {
                $join->on('my_favourites.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->where('my_favourites.student_id', session('userid')->id);
            })
            ->leftjoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'my_favourites.status', 'tutorprofiles.name', 'tutorprofiles.rateperhour', 'tutorprofiles.admin_commission', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Get form data
        $subjectIds = $request->input('subjectlistid');
        $gradeId = $request->input('gradelistid');
        $minPrice = $request->input('tminprice');
        $maxPrice = $request->input('tmaxprice');
        $ratings = $request->input('ratings');
        $locations = $request->input('locations');
        $tutorName = $request->input('ttsearch');

        // Tutors List
        $tutorlist = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'my_favourites.status as myfav',
            'tutorprofiles.name',
            'tutorprofiles.headline',
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
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
            ->leftJoin('my_favourites', function ($join) {
                $join->on('my_favourites.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->where('my_favourites.student_id', session('userid')->id);
            })
            ->leftJoin('classschedules', 'classschedules.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->where('tutorregistrations.is_active', 1)
            // ->when($subjectIds, function ($query) use ($subjectIds) {
            //     return $query->whereIn('tutorsubjectmappings.subject_id', $subjectIds);
            // })
            ->when($subjectIds, function($q) use ($subjectIds) {
            $subjectName = Subjects::where('id', $subjectIds)->value('name');
            $q->whereIn('subjects.id', 
                Subjects::where('name', $subjectName)->pluck('id')
            );
            })

            ->when($gradeId, function ($query) use ($gradeId) {
                return $query->where('classes.id', $gradeId);
            })
            ->when($minPrice, function ($query) use ($minPrice) {
                return $query->where(DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100))'), '>=', $minPrice);
            })
            ->when($maxPrice, function ($query) use ($maxPrice) {
                return $query->where(DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100))'), '<=', $maxPrice);
            })
            ->when($minPrice && $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween(DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100))'), [$minPrice, $maxPrice]);
            })
            ->when($tutorName, function ($query) use ($tutorName) {
                return $query->where('tutorregistrations.name', 'like', '%' . $tutorName . '%');
            })
            ->groupBy(
                'tutorprofiles.tutor_id',
                'my_favourites.status',
                'tutorprofiles.name',
                'tutorprofiles.headline',
                'tutorprofiles.experience',
                'tutorprofiles.rateperhour',
                'tutorprofiles.profile_pic',
                'tutorprofiles.admin_commission'
            )
            ->get();

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        // Pass only the necessary data to the Blade view
        // $html = view('front-cms.index_tutor_search', compact('tutorlists'))->render();

        // Return the HTML response
        // return response()->json(['html' => $html]);

        $subjectlist = subjects::select('*')->get();
        $classes = classes::where('is_active', 1)->get();
        $countrylist = country::select('*')->get();
        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // dd( ($tutors));
        // return view('front-cms.findatutor',get_defined_vars());
        if (!$tutorlist) {
            return view('student.searchtutor')->with('fail', 'No tutor found');
        }
        return view('student.searchtutor', get_defined_vars());
    }
    public function tutorsearchindex(Request $request)
    {
        // dd($request->all());

        // $query = tutorprofile::select('tutorprofiles.id','classes.name as class_name','tutorprofiles.name',DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'), 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings)/COUNT(ratings) AS starrating,  COUNT(DISTINCT topics.name) as total_topics'),'tutorsubjectmappings.id as sub_map_id')
        // // select('tutorprofiles.id', 'tutorprofiles.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings)/COUNT(ratings) AS starrating, COUNT(topics.name) as total_topics'))
        //     ->leftjoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
        //     ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        //     ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        //     ->leftjoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
        //     ->leftjoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
        //     ->leftjoin('topics', 'topics.subject_id', '=', 'subjects.id');
        //     if($request->gradelistid){
        //         $query->where('teacherclassmappings.class_id',$request->gradelistid);
        //     }
        //     if($request->subjectlistid){
        //         $query->where('tutorsubjectmappings.subject_id',$request->subjectlistid);
        //     }

        //     $tutorlist=  $query->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name',  'classes.name','tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name','rate','sub_map_id')
        //     ->get();
        // // dd($tutorlist);

        // if (!$tutorlist) {
        //     return view('student.searchtutor')->with('fail', 'No tutor found');
        // }
        //////////////////////==========

        $classes = classes::all('id', 'name');

        // $tutors = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject',DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'))
        //     ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
        //     ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
        //     ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
        //     ->get();
        $tutors = tutorprofile::select(
            'tutorsubjectmappings.id as submapid',
            'tutorprofiles.name',
            'tutorprofiles.profile_pic',
            'subjects.name as subject',
            'classes.name as className',
            DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),
            DB::raw('AVG(tutorreviews.ratings) as avg_rating')
        )
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftjoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'tutorprofiles.name', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Check if either gradelistid or subjectlistid is provided
        if ($request->has('gradelistid') || $request->has('subjectlistid')) {
            $tutorlists = tutorprofile::select('tutorprofiles.id', 'classes.name as class_name', 'tutorprofiles.name', 'tutorprofiles.headline', 'tutorprofiles.experience', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'), 'tutorprofiles.profile_pic', 'subjects.id as subjectid', 'subjects.name as subject', DB::raw('SUM(ratings) / COUNT(ratings) AS starrating', 'COUNT(DISTINCT topics.name) as total_topics'), 'tutorsubjectmappings.id as sub_map_id', DB::raw('(SELECT COUNT(*) FROM classschedules WHERE classschedules.tutor_id = tutorprofiles.id) AS total_classes_done'))
                ->join('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
                ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
                ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
                ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
                ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
                ->join('topics', 'topics.subject_id', '=', 'subjects.id')
                ->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done');

            // Check if gradelistid is provided and add the condition
            if ($request->has('gradelistid')) {
                $tutorlists->where('classes.id', $request->gradelistid);
            }

            // Check if subjectlistid is provided and add the condition
            if ($request->has('subjectlistid')) {
                $tutorlists->where('tutorsubjectmappings.subject_id', $request->subjectlistid);
            }

            // Get the results
            $tutorlists = $tutorlists->get();
        } else {
            // If neither gradelistid nor subjectlistid is provided, show an error or return an appropriate response.
            // You can handle the error or response based on your application's requirements.
        }

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // dd( ($tutors));
        return view('front-cms.index', get_defined_vars());
    }

    // BACKUP: Original Stripe payment method - renamed for backup
    public function purchaseclass_backup_stripe(Request $request)
    {
        dd($request);
        $request->validate([
            'tutorenrollid' => 'required',
            'subjectenrollid' => 'required',
            'requiredclassenroll' => 'required',
            'rateperhourenroll' => 'required',
            'totalamountenroll' => 'required',
        ]);

        // Store in session temporarily or pass to Stripe payment view
        $stripeData = [
            'tutorenrollid' => $request->tutorenrollid,
            'subjectenrollid' => $request->subjectenrollid,
            'requiredclassenroll' => $request->requiredclassenroll,
            'rateperhourenroll' => $request->rateperhourenroll,
            'totalamountenroll' => $request->totalamountenroll,
            'slotids' => $request->slotids,
            'contactadmin' => $request->contactadmin,
        ];

        session(['stripe_payload' => $stripeData]);
        $stripekey = config('services.stripe.key');

        return view('stripe', [
            'amt' => $request->totalamountenroll,
            'stripeKey' => $stripekey
        ]);
    }

    // NEW: Enrollment request method with Stripe integration
    public function purchaseclass(Request $request)
    {
        try {
            $request->validate([
                'tutorenrollid' => 'required',
                'subjectenrollid' => 'required',
                'requiredclassenroll' => 'required',
                'rateperhourenroll' => 'required',
                'totalamountenroll' => 'required',
            ]);

            // 1. Validate 'Required Classes' is present and greater than 0
            if (empty($request->requiredclassenroll) || $request->requiredclassenroll < 1) {
                return redirect()->back()->with('fail', 'Please enter the number of required classes.');
            }

            // 2. Validate scheduling (Must have slots OR Contact Admin checked)
            if (empty($request->slotids) && ($request->contactadmin !== 'on')) {
                return redirect()->back()->with('fail', 'Please select your preferred slots or check "Contact Admin" if no slots suit you.');
            }

            $classId = subjects::find($request->subjectenrollid);
            $tutorname = tutorprofile::where('tutor_id', $request->tutorenrollid)->first();

            // Check if subject and tutor exist
            if (!$classId) {
                return redirect()->back()->with('fail', 'Subject not found.');
            }
            if (!$tutorname) {
                return redirect()->back()->with('fail', 'Tutor not found.');
            }

            // Store in session temporarily and pass to Stripe payment view
            $stripeData = [
                'tutorenrollid' => $request->tutorenrollid,
                'subjectenrollid' => $request->subjectenrollid,
                'requiredclassenroll' => $request->requiredclassenroll,
                'rateperhourenroll' => $request->rateperhourenroll,
                'totalamountenroll' => $request->totalamountenroll,
                'slotids' => $request->slotids,
                'contactadmin' => $request->contactadmin,
            ];

            session(['stripe_payload' => $stripeData]);

            $stripekey = config('services.stripe.key');

            // dd($stripekey);

            return view('stripe', [
                'amt' => $request->totalamountenroll,
                'stripeKey' => $stripekey
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'fail',
                'Something went wrong while processing your request. Please try again or contact support.'
            );
            Log::error('Purchase Class Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),

                // Important request data (for debugging)
                'request_data' => $request->all(),
                'user_id' => session('userid')->id ?? null,
            ]);
        }
    }
    // public function purchaseclass(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'tutorenrollid' => 'required',
    //         'subjectenrollid' => 'required',
    //         // 'availableclassenroll' => 'required', //nnn
    //         'requiredclassenroll' => 'required',
    //         'rateperhourenroll' => 'required', //nn
    //         'totalamountenroll' => 'required', //nn
    //     ]);

    //     $totalamt = intval($request->totalamountenroll) * 100;
    //     // dd($totalamt);
    //     $selectedSlotIds = $request->slotids;
    //     $contactadmin = $request->contactadmin;

    //     $order_id = substr(uniqid(date('YmdHis')), 0, 20);

    //     $classId = subjects::select('*')->where('id', $request->subjectenrollid)->first();
    //     $tutorname = tutorprofile::select('*')->where('tutor_id', $request->tutorenrollid)->first();

    //     $amt = $request->totalamountenroll;
    //      return view('stripe',compact('amt'));



    //     // Step 1: Save the paymentdetails record
    //     $paymentdetails = new paymentdetails();
    //     $paymentdetails->transaction_id = $order_id;
    //     $paymentdetails->payment_mode = 'Credit Card';
    //     $paymentdetails->amount = $request->totalamountenroll;
    //     $paymentdetails->status = 0;
    //     $test = $paymentdetails->save();

    //     // Step 2: Save the studentpayment record
    //     $studentpayment = new paymentstudents();
    //     $studentpayment->transaction_id = $order_id;
    //     $studentpayment->student_id = session('userid')->id;
    //     $studentpayment->class_id = $classId->class_id;
    //     $studentpayment->subject_id = $request->subjectenrollid;
    //     $studentpayment->tutor_id = $request->tutorenrollid;
    //     $studentpayment->classes_purchased = $request->requiredclassenroll;
    //     $studentpayment->rate_per_hr = $request->rateperhourenroll;
    //     $spdres = $studentpayment->save();

    //     // Send welcome mail
    //     $details = [
    //         'name' => session('userid')->name,
    //         'total_classes' => $request->requiredclassenroll,
    //         'tutor_name' => $tutorname->name,
    //         'mailtype' => 4,
    //     ];

    //     Mail::to(session('userid')->email)->send(new SendMail($details));

    //     $selectedSlotIdsArray = explode(',', $selectedSlotIds);
    //     foreach ($selectedSlotIdsArray as $slotId) {
    //         // Find the existing slotbooking record by id
    //         $slotbooking = SlotBooking::find($slotId);

    //         // Check if the record exists
    //         if ($slotbooking) {
    //             // Update the fields as needed
    //             $slotbooking->student_id = session('userid')->id;
    //             $slotbooking->booked_at = Carbon::now();
    //             $slotbooking->transaction_id = $order_id;
    //             $slotbooking->subject_id = $request->subjectenrollid;
    //             $slotbooking->status = 1;
    //             $slotbooking->contact_admin = $request->contactadmin == 'on' ? 1 : 0;
    //             $slotbooking->class_schedule_id = $studentpayment->id;
    //             // $slotbooking->slot_id = $slotId;

    //             // Save the changes
    //             $slotbooking->save();
    //         }
    //     }
    //     if ($contactadmin == 'on') {

    // Here I need to pass notification into db
    //         $notificationdata = new Notification();
    //         $notificationdata->alert_type = 6;
    //         $notificationdata->notification = session('userid')->name . ' Need your help in slot booking';
    //         $notificationdata->initiator_id = session('userid')->id;
    //         $notificationdata->initiator_role = session('userid')->role_id;
    //         $notificationdata->event_id = $request->tutorenrollid;
    // Sending to admin
    // if($request->receiver_role_id == 1){
    //     $notificationdata->show_to_admin = 1;
    //     $notificationdata->show_to_admin_id = $request->receiver_id;
    //         $notificationdata->show_to_all_admin = 1;
    // }
    // Sending to tutor
    // if($request->receiver_role_id == 2){
    // $notificationdata->show_to_tutor = 1;
    // $notificationdata->show_to_tutor_id = $tutor_id->tutor_id;
    // $notificationdata->show_to_all_tutor = 0;
    // }
    // Sending to student
    // if($request->receiver_role_id == 3){
    //     $notificationdata->show_to_student = 1;
    //     $notificationdata->show_to_student_id = $request->receiver_id;
    //     // $notificationdata->show_to_all_student = 0;
    // }
    // // Sending to parent
    // if($request->receiver_role_id == 3){
    //     $notificationdata->show_to_parent = 1;
    //     $notificationdata->show_to_parent_id = $request->receiver_id;
    //     // $notificationdata->show_to_all_parent = 0;
    // }
    //         $notificationdata->read_status = 0;

    //         $notified = $notificationdata->save();
    //         broadcast(new RealTimeMessage('$notification'));

    //         $msg = 'Enrollment completed. Please have patience, admin you contact you soon.';
    //     } else {
    //         $msg = 'Enrollment Completed & Slots confirmed. Kindly use your registered Email Id to join class.';
    //     }

    //     if ($spdres) {

    //         //////////////// Here I need to pass notification into db
    //         $notificationdata = new Notification();
    //         $notificationdata->alert_type = 7;
    //         $notificationdata->notification = session('userid')->name . ' Enrolled for classes';
    //         $notificationdata->initiator_id = session('userid')->id;
    //         $notificationdata->initiator_role = session('userid')->role_id;
    //         $notificationdata->event_id = $request->tutorenrollid;
    //         // Sending to admin
    //         // if($request->receiver_role_id == 1){
    //         //     $notificationdata->show_to_admin = 1;
    //         //     $notificationdata->show_to_admin_id = $request->receiver_id;
    //         //     // $notificationdata->show_to_all_admin = 1;
    //         // }
    //         // Sending to tutor
    //         // if($request->receiver_role_id == 2){
    //         $notificationdata->show_to_tutor = 1;
    //         $notificationdata->show_to_tutor_id = $request->tutorenrollid;
    //         // $notificationdata->show_to_all_tutor = 0;
    //         // }
    //         // Sending to student
    //         // if($request->receiver_role_id == 3){
    //         //     $notificationdata->show_to_student = 1;
    //         //     $notificationdata->show_to_student_id = $request->receiver_id;
    //         //     // $notificationdata->show_to_all_student = 0;
    //         // }
    //         // // Sending to parent
    //         // if($request->receiver_role_id == 3){
    //         //     $notificationdata->show_to_parent = 1;
    //         //     $notificationdata->show_to_parent_id = $request->receiver_id;
    //         //     // $notificationdata->show_to_all_parent = 0;
    //         // }
    //         $notificationdata->read_status = 0;

    //         $notified = $notificationdata->save();
    //         broadcast(new RealTimeMessage('$notification'));

    //         // return back()->with('success', $msg);
    //         return redirect()->to('student/enrollsuccess');
    //     } else {
    //         return back()->with('fail', 'Something Went Wrong. Try Again Later');
    //     }
    // }

    // NEW: Stripe payment success method
    public function stripePaymentSuccess(Request $request)
    {
        try {
            $data = session('stripe_payload');

            if (!$data) {
                return redirect()->route('student.dashboard')->with('fail', 'Session expired or payment data lost.');
            }

            $order_id = session('order_id') ?? substr(uniqid(date('YmdHis')), 0, 20);
            $classId = subjects::find($data['subjectenrollid']);
            $tutorname = tutorprofile::where('tutor_id', $data['tutorenrollid'])->first();

            // Check if subject and tutor exist
            if (!$classId || !$tutorname) {
                return redirect()->route('student.dashboard')->with('fail', 'Course or tutor missing in records.');
            }

            // Save enrollment request (pending admin approval)
            $paymentdetails = new paymentdetails();
            $paymentdetails->transaction_id = $order_id;
            $paymentdetails->payment_mode = paymentdetails::MODE_STRIPE;
            $paymentdetails->amount = $data['totalamountenroll'];
            $paymentdetails->status = 0; // 0 = pending approval
            $paymentdetails->save();

            // Save student enrollment request
            $studentpayment = new paymentstudents();
            $studentpayment->transaction_id = $order_id;
            $studentpayment->student_id = session('userid')->id;
            $studentpayment->class_id = $classId->class_id;
            $studentpayment->subject_id = $data['subjectenrollid'];
            $studentpayment->tutor_id = $data['tutorenrollid'];
            $studentpayment->classes_purchased = $data['requiredclassenroll'];
            $studentpayment->rate_per_hr = $data['rateperhourenroll'];
            $studentpayment->save();

            // Book slots (temporarily reserved)
            $slotIds = explode(',', $data['slotids'] ?? '');
            foreach ($slotIds as $slotId) {
                if (empty($slotId)) continue;
                $slotbooking = SlotBooking::find($slotId);
                if ($slotbooking) {
                    $slotbooking->student_id = session('userid')->id;
                    $slotbooking->booked_at = now();
                    $slotbooking->transaction_id = $order_id;
                    $slotbooking->subject_id = $data['subjectenrollid'];
                    $slotbooking->status = 2; // 2 = pending approval
                    $slotbooking->contact_admin = ($data['contactadmin'] ?? '') == 'on' ? 1 : 0;
                    $slotbooking->class_schedule_id = $studentpayment->id;
                    $slotbooking->save();
                }
            }

            // Send notification to admin
            try {
                $notificationdata = new Notification();
                $notificationdata->alert_type = 8; // alert type for enrollment requests
                $notificationdata->notification = session('userid')->name . ' has paid and requested enrollment for classes with ' . $tutorname->name;
                $notificationdata->initiator_id = session('userid')->id;
                $notificationdata->initiator_role = session('userid')->role_id;
                $notificationdata->event_id = $data['tutorenrollid'];
                $notificationdata->show_to_admin = 1;
                $notificationdata->show_to_all_admin = 1;
                $notificationdata->read_status = 0;
                $notificationdata->save();
                broadcast(new RealTimeMessage('$notification'));
            } catch (\Exception $e) {
            }

            // Send WhatsApp notification to Tutor when student purchases class
            try {
                $whatsApp = app(TwilioWhatsAppService::class);
                $tutor = tutorregistration::find($data['tutorenrollid']);
                $student = session('userid');

                $firstSlot = null;
                if (!empty($data['slotids'])) {
                    $firstSlot = SlotBooking::whereIn('id', explode(',', $data['slotids']))
                        ->orderBy('date', 'asc')
                        ->first();
                }

                if (!empty($tutor->mobile) && $firstSlot) {
                    $templateIdTutor = 1605; // Your approved template ID
                    $tutorNumber = $tutor->mobile;

                    // Format date & time in tutor's timezone for WhatsApp message
                    $slotDate = TimezoneHelper::formatInUserTz($firstSlot->date, 'd M Y', 'UTC', $tutor);
                    $slotTime = TimezoneHelper::formatInUserTz($firstSlot->slot, 'h:i A', 'UTC', $tutor);

                    // Variables must match your template placeholders exactly
                    $bodyVariablesTutor = [
                        $tutor->name,                       // {{1}}
                        $classId->name ?? 'N/A',            // {{2}}
                        $student->name,                     // {{3}}
                        $slotDate,                          // {{4}}
                        $slotTime,                          // {{5}}
                    ];

                    $whatsApp->sendMessage($tutorNumber, $bodyVariablesTutor, $templateIdTutor);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp send failed for class purchase: ' . $e->getMessage());
            }

            // Send mail to student
            try {
                $details = [
                    'name' => session('userid')->name,
                    'total_classes' => $data['requiredclassenroll'],
                    'tutor_name' => $tutorname->name,
                    'mailtype' => 5, // New mail type for enrollment request
                ];
                Mail::to(session('userid')->email)->send(new SendMail($details));
            } catch (\Exception $e) {
            }

            session()->forget('stripe_payload');

            return redirect()->route('student.admission', ['id' => $data['tutorenrollid']])->with('success', 'Payment successful and enrollment request submitted. Admin will review and approve your request.');
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Something went wrong processing your successful payment. Please contact admin.');
            Log::error('Stripe Payment Success Flow Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => session('userid')->id ?? null,
            ]);
        }
    }



    public function tutorslist()
    {
        $ttrlists = tutorregistration::select(
            'tutorregistrations.id as tutor_id',
            'tutorregistrations.name as tutor_name',
            'tutorregistrations.mobile as tutor_mobile',
            'tutorregistrations.email as tutor_email',
            'tutorregistrations.is_active as tutor_status',
            'tutorprofiles.rateperhour as rate',
            'tutorprofiles.admin_commission as admin_commission',
            'tutorprofiles.id as rate_id'
        )
            ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorregistrations.id')
            // ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            // ->leftJoin('classes', 'classes.id', '=', 'subjects.class_id')
            ->leftJoin('tutorprofiles', 'tutorprofiles.tutor_id', 'tutorregistrations.id')
            ->groupBy(
                'tutorregistrations.id',
                'tutorregistrations.name',
                'tutorregistrations.mobile',
                'tutorregistrations.email',
                'tutorregistrations.is_active',
                'tutorprofiles.rateperhour',
                'tutorprofiles.admin_commission',
                'tutorprofiles.id'
            )
            ->orderBy('tutorregistrations.created_at', 'desc')
            ->get();

        $classes = classes::where('is_active', 1)->get();

        return view('admin.tutors', compact('ttrlists', 'classes'));
    }

    //  tutors search
    public function tutorslistsearch(Request $request)
    {
        // return $request->all();
        $query = tutorregistration::select('*', 'tutorregistrations.id as tutor_id', 'classes.name as class_name', 'tutorregistrations.name as tutor_name', 'tutorregistrations.mobile as tutor_mobile', 'tutorregistrations.email as tutor_email', 'tutorregistrations.is_active as tutor_status', 'subjects.name as subject_name', 'tutorsubjectmappings.rate as rate', 'tutorsubjectmappings.admin_commission as admin_commission', 'tutorsubjectmappings.id as rate_id')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorregistrations.id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'subjects.class_id');
        // ->get();
        if ($request->tutor_name) {
            $query->where('tutorregistrations.name', 'like', '%' . $request->tutor_name . '%');
        }
        if ($request->tutor_mobile) {
            $query->where('tutorregistrations.mobile', 'like', '%' . $request->tutor_mobile . '%');
        }
        if ($request->class_name) {
            $query->where('subjects.class_id', $request->class_name);
        }
        if ($request->status_field) {
            if ($request->status_field == '2') {
                $request->status_field = '0';
            }
            $query->where('tutorregistrations.is_active', $request->status_field);
        }
        $ttrlists = $query->paginate(10);
        $type = 'tutors';
        $viewTable = view('admin.partials.students-tutor-search', compact('ttrlists', 'type'))->render();
        $viewPagination = $ttrlists->links()->render();
        return response()->json([
            'table' => $viewTable,
            'pagination' => $viewPagination,
        ]);
    }

    // tutor search index page/common page
    public function tutorsindexsearch(Request $request)
    {

        // dd($request->gradelistid);

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
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftjoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'tutorprofiles.name', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Get form data
        $subjectIds = $request->input('subjectlistid');
        $gradeId = $request->input('gradelistid');
        $minPrice = $request->input('tminprice');
        $maxPrice = $request->input('tmaxprice');
        $ratings = $request->input('ratings');
        $locations = $request->input('locations');

        // Tutors List
        $tutorlists = tutorprofile::select(
            'tutorprofiles.tutor_id as tutor_id',
            'classes.name as class_name',
            'tutorprofiles.name',
            'tutorprofiles.headline',
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
            ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
            ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.id')
            ->join('topics', 'topics.subject_id', '=', 'subjects.id')
            ->where('tutorregistrations.is_active', 1)
            ->when($subjectIds, function ($query) use ($subjectIds) {
                return $query->whereIn('tutorsubjectmappings.subject_id', $subjectIds);
            })
            ->when($gradeId, function ($query) use ($gradeId) {
                return $query->where('classes.id', $gradeId);
            })
            ->when($minPrice && $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween(DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100))'), [$minPrice, $maxPrice]);
            })
            // ->when($ratings, function ($query) use ($ratings) {
            //     return $query->whereIn('tutorreviews.ratings', $ratings);
            // })
            // ->when($locations, function ($query) use ($locations) {
            //     return $query->whereIn('locations.name', $locations);
            // })
            // ->when($subjectId, function ($query, $subjectId) {
            //     return $query->where('subjects.id', $subjectId);
            // })
            // ->when($gradeId, function ($query, $gradeId) {
            //     return $query->where('classes.id', $gradeId);
            // })
            // ->when($minPrice && $maxPrice, function ($query, $minPrice, $maxPrice) {
            //     return $query->whereBetween(DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100))'), [$minPrice, $maxPrice]);
            // })
            ->groupby('tutorprofiles.id', 'tutorprofiles.tutor_id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        // Pass only the necessary data to the Blade view
        $html = view('front-cms.index_tutor_search', compact('tutorlists'))->render();

        // Return the HTML response
        // return response()->json(['html' => $html]);

        $subjectlist = subjects::select('*')->get();
        $classes = classes::where('is_active', 1)->get();
        $countrylist = country::select('*')->get();
        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();

        // dd( ($tutors));
        return view('front-cms.findatutor', get_defined_vars());
    }

    // tutor search dashboard page
    public function tutorsdashboardsearch(Request $request)
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
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->join('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
            ->leftjoin('tutorreviews', function ($join) {
                $join->on('tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
                    ->on('tutorreviews.subject_id', '=', 'tutorsubjectmappings.subject_id');
            })
            ->groupBy('tutorsubjectmappings.id', 'tutorprofiles.name', 'subjects.name', 'tutorsubjectmappings.rate', 'tutorsubjectmappings.admin_commission', 'classes.name', 'tutorprofiles.profile_pic')
            ->get();

        // Get form data
        $subjectId = $request->input('subjectlistid');
        $gradeId = $request->input('gradelistid');
        $minPrice = $request->input('tminprice');
        $maxPrice = $request->input('tmaxprice');

        // Tutors List
        $tutorlists = tutorprofile::select(
            'tutorprofiles.id',
            'classes.name as class_name',
            'tutorprofiles.name',
            'tutorprofiles.headline',
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
            ->join('topics', 'topics.subject_id', '=', 'subjects.id')
            ->when($subjectId, function ($query, $subjectId) {
                return $query->where('subjects.id', $subjectId);
            })
            ->when($gradeId, function ($query, $gradeId) {
                return $query->where('classes.id', $gradeId);
            })
            ->when($minPrice && $maxPrice, function ($query, $minPrice, $maxPrice) {
                return $query->whereBetween(DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100))'), [$minPrice, $maxPrice]);
            })
            ->groupby('tutorprofiles.id', 'subjects.id', 'subjects.name', 'classes.name', 'tutorprofiles.rate', 'tutorprofiles.profile_pic', 'tutorprofiles.name', 'rate', 'sub_map_id', 'experience', 'headline', 'total_classes_done')
            ->get();

        // Subject lists with category
        $subjectlists = DB::table('subjects')
            ->join('subjectcategories', 'subjects.category', '=', 'subjectcategories.id')
            ->select('subjectcategories.name as category_name', 'subjects.name as subject_name', 'subjects.id as subject_id')
            ->where('subjects.is_active', 1)
            ->orderBy('subjectcategories.name')
            ->get();

        // Grades/Level
        $gradelists = Classes::where('is_active', 1)->get();
        // Pass only the necessary data to the Blade view
        $html = view('student.tutorsearchdashboard', compact('tutorlists'))->render();

        // Return the HTML response
        return response()->json(['html' => $html]);
    }
    public function status(Request $request)
    {
        $data = tutorregistration::find($request->id);
        if ($request->status == 1) {
            $status = 0;
        }
        if ($request->status == 0) {
            $status = 1;
        }
        $data->is_active = $status;

        $res = $data->save();
        return json_encode(array('statusCode' => 200));
    }

    public function commissionupdate(Request $request)
    {
        // dd($request->id);
        // $tutorid = tutorsubjectmapping::find($request->id);
        $data = TutorProfile::where('tutor_id', $request->id)->first();
        // dd($data);
        $data->admin_commission = $request->commission;
        $res = $data->save();
        return json_encode(array('statusCode' => 200));
    }

    public function rateupdate(Request $request)
    {

        $data = TutorProfile::where('tutor_id', $request->id)->first();
        // dd($data);
        $data->rateperhour = $request->rate;
        $res = $data->save();
        return json_encode(array('statusCode' => 200));
    }

    public function teacherProfile($id)
    {
        $tutorpd = tutorprofile::select('tutorprofiles.*', 'subjects.name as subject', 'subjects.name as subject', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'))
            ->leftjoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
            ->leftjoin('teacherclassmappings', 'teacherclassmappings.subject_mapping_id', '=', 'tutorsubjectmappings.id')
            ->leftjoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            // ->where('tutorprofiles.id', '=', $id)
            ->where('tutorsubjectmappings.id', '=', $id)
            ->first();

        if ($tutorpd) {
            $achievement = tutorachievements::select('*')->where('tutor_id', '=', $tutorpd->id)->get();

            $reviews = tutorreviews::select('tutorreviews.id', 'tutorreviews.name', 'tutorreviews.ratings', 'tutorreviews.subject_id', 'tutorreviews.tutor_id', 'subjects.name as subject')
                ->leftjoin('subjects', 'subjects.id', '=', 'tutorreviews.subject_id')
                ->where('tutorreviews.tutor_id', '=', $tutorpd->id)->get();
        }

        if (!$tutorpd) {
            return back();
        }

        return view('common.tutorprofile', compact('tutorpd', 'achievement', 'reviews'));
    }

    public function enrollnow($id)
    {
        $subjects = subjects::select('subjects.*')
            ->join('tutorsubjectmappings', 'tutorsubjectmappings.subject_id', 'subjects.id')
            ->where('tutorsubjectmappings.tutor_id', $id)->get();


        $currentDate = Carbon::now('UTC')->toDateString();
        $enrollment = TutorSubjectMapping::select('tutorsubjectmappings.*', 'subjects.name as subject_name', 'tutorprofiles.name as tutor_name', DB::raw('(tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100) + tutorprofiles.rateperhour as rate'))
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', '=', 'tutorsubjectmappings.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            ->where('tutorprofiles.tutor_id', $id)
            ->first();

        $slotsAvailability = SlotBooking::select('slot_bookings.*', 'studentprofiles.name as student_name', 'subjects.name as subject')
            ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'slot_bookings.student_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
            ->where('slot_bookings.tutor_id', $id)
            ->whereDate('slot_bookings.date', '>=', $currentDate) // Filter for current date or later
            ->orderBy('slot_bookings.date')
            ->get();
        // dd($slotsAvailability);
        $groupedSlots = [];
        foreach ($slotsAvailability as $slot) {
            // Convert UTC date and time to student's timezone for display
            $date = TimezoneHelper::toUserTz($slot->date, 'UTC')->isoFormat('DD-MM-YYYY (dddd)');
            $time = TimezoneHelper::timeToUserTz($slot->slot);
            $id = $slot->id;
            $isAvailable = $slot->status == 0; // Adjust this based on your actual logic
            // dd($slotsAvailability);
            if (!isset($groupedSlots[$date])) {
                $groupedSlots[$date] = [];
            }

            $groupedSlots[$date][] = [
                'time' => $time,
                'is_available' => $isAvailable,
                'id' => $id,
            ];
        }

        return view('student.enrollnow', compact('enrollment', 'groupedSlots', 'subjects'));
    }

    public function enrollupdate($id)
    {
        $tutor_profile_id = $id;
        $studentid = session('userid')->id;

        $enrollment = TutorSubjectMapping::select('tutorsubjectmappings.*', 'subjects.name as subject_name', 'tutorprofiles.name as tutor_name', DB::raw('(tutorsubjectmappings.rate + (tutorsubjectmappings.rate * tutorsubjectmappings.admin_commission / 100)) as rate'),)
            ->join('tutorprofiles', 'tutorprofiles.tutor_id', '=', 'tutorsubjectmappings.tutor_id')
            ->join('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
            // ->where('tutorsubjectmappings.id', $id)
            ->where('tutorsubjectmappings.tutor_id', $id)
            ->first();

        $slotsAvailability = SlotBooking::select('slot_bookings.*', 'studentprofiles.name as student_name', 'subjects.name as subject', 'studentregistrations.id as student_id')
            ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'slot_bookings.student_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', '=', 'slot_bookings.student_id')
            ->where('slot_bookings.tutor_id', $id)
            ->where(function ($query) {
                // Use UTC for comparison
                $query->whereDate('slot_bookings.date', '>', Carbon::now('UTC')->toDateString())
                    ->orWhere(function ($q) {
                        $q->whereDate('slot_bookings.date', Carbon::now('UTC')->toDateString())
                            ->whereTime('slot_bookings.slot', '>=', Carbon::now('UTC')->format('H:i:s'));
                    });
            })
            ->orderBy('slot_bookings.date')
            ->get();
        // dd($slotsAvailability->toArray());

        $groupedSlots = [];
        $bookedSlotsCount = 0; // Initialize the count variable

        foreach ($slotsAvailability as $slot) {
            // Convert UTC date and time to student's timezone for display
            $date = TimezoneHelper::dateToUserTz($slot->date);
            $time = TimezoneHelper::timeToUserTz($slot->slot);

            $id = $slot->id;
            $isAvailable = $slot->status == 0; // Adjust this based on your actual logic
            $bookedById = $slot->student_id == $studentid; // Check if the slot is booked by the current student

            if (!isset($groupedSlots[$date])) {
                $groupedSlots[$date] = [];
            }

            $groupedSlots[$date][] = [
                'time' => $time,
                'is_available' => $isAvailable,
                'id' => $id,
                'bookedbyme' => $bookedById,
            ];

            // Increment the count if the slot is booked by the current user
            if ($bookedById) {
                $bookedSlotsCount++;
            }
        }


        return view('student.enrollupdate', compact('enrollment', 'groupedSlots', 'bookedSlotsCount', 'tutor_profile_id'));
    }

    public function updateslots(Request $request)
    {
        // dd($request->all());
        $selectedSlotIds = $request->slotids;
        $contactadmin = $request->contactadmin;

        $studentpayment = paymentstudents::select('*')
            ->where('student_id', session('userid')->id)
            ->where('tutor_id', $request->tutorenrollid)
            ->where('subject_id', $request->subjectenrollid)
            ->orderBy('id', 'desc')
            ->first();

        $spdres = "";
        // Step 3: Update slotbooking records for each selected slot
        // Converting comma-separated string to an array
        $selectedSlotIdsArray = explode(',', $selectedSlotIds);
        foreach ($selectedSlotIdsArray as $slotId) {
            // Find the existing slotbooking record by id
            $slotbooking = SlotBooking::find($slotId);

            // Check if the record exists
            if ($slotbooking) {
                // Update the fields as needed
                $slotbooking->student_id = session('userid')->id;
                $slotbooking->booked_at = Carbon::now('UTC');
                $slotbooking->transaction_id = $studentpayment->transaction_id;
                $slotbooking->subject_id = $request->subjectenrollid;
                $slotbooking->status = 1;
                $slotbooking->contact_admin = $request->contactadmin == 'on' ? 1 : 0;
                $slotbooking->class_schedule_id = $studentpayment->id;
                // $slotbooking->slot_id = $slotId;

                // Save the changes
                $spdres = $slotbooking->save();
            }
        }
        if ($contactadmin == 'on') {
            $msg = 'Slots Updated. Please have patience, admin will contact you soon.';
        } else {
            $msg = 'Slots Updated & confirmed. For any querry, you can contact admin.';
        }

        // dd($studentpayment);
        if ($spdres) {

            return back()->with('success', $msg);
        } else {
            return back()->with('fail', 'Something Went Wrong. Try Again Later');
        }
    }
    public function tutorslotscheck($id)
    {
        $slots = SlotBooking::select('slot_bookings.*', 'studentprofiles.name as student_name', 'subjects.name as subject')
            ->leftJoin('studentprofiles', 'studentprofiles.student_id', '=', 'slot_bookings.student_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'slot_bookings.subject_id')
            ->where('slot_bookings.tutor_id', $id)
            ->get();

        return view('admin.tutorslots', compact('slots'));
    }

    public function enrollsuccess()
    {

        return view('student.enrollsuccess');
    }

    public function tutordelete($id)
    {
        // Check if there are any payments related to the tutor
        $check = paymentstudents::where('tutor_id', $id)->first();

        if ($check) {
            // Return with a failure message if the tutor has been involved in payments
            return back()->with('fail', "Tutor can't be deleted because it is in use; a student has purchased some classes from this tutor.");
        } else {
            // Use transaction to ensure both deletions happen atomically
            DB::beginTransaction();
            try {
                // Find the tutor profile by tutor_id and delete it
                $tutorProfile = tutorprofile::where('tutor_id', $id)->first();
                // $tutorProfile->delete();
                if ($tutorProfile) {
                    $tutorProfile->delete();
                }

                // Find the tutor registration by id and delete it
                $tutorRegistration = tutorregistration::find($id);
                if ($tutorRegistration) {
                    $tutorRegistration->delete();
                } else {
                    // If the tutor registration doesn't exist, return with an error message
                    return back()->with('fail', 'Tutor registration not found.');
                }

                // Commit the transaction
                DB::commit();

                return back()->with('success', 'Tutor deleted successfully.');
            } catch (\Exception $e) {
                // Rollback the transaction in case of any error
                DB::rollBack();
                return back()->with('fail', 'Something went wrong, please try again later.');
            }
        }
    }
}
