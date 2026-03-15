   // $tutors = tutorprofile::select(
   // 'tutorprofiles.tutor_id as tutor_id',
   // 'tutorprofiles.name',
   // 'tutorprofiles.headline',
   // 'tutorprofiles.qualification as tutor_qualification',
   // 'tutorprofiles.intro_video_link',
   // 'tutorprofiles.experience',
   // DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as
   rateperhour'),
   // 'tutorprofiles.profile_pic',
   // DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", "), ",", 2) as
   subject'),
   // DB::raw('SUM(tutorreviews.ratings) / COUNT(tutorreviews.id) AS starrating'),
   // DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
   // DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
   // )
   // ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
   // ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
   // ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
   // ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->where('tutorregistrations.is_active', 1)
   // ->where('subjects.id', 'like', '%' . $subjectid . '%') // LIKE search for subject
   // ->where('classes.id', 'like', '%' . $classid . '%') // LIKE search for class
   // ->where('tutorprofiles.name', 'like', '%' . $tutorname . '%'); // LIKE search for tutor

   // $tutors = tutorprofile::select(
   // 'tutorprofiles.tutor_id as tutor_id',
   // 'tutorprofiles.name',
   // 'tutorprofiles.headline',
   // 'tutorprofiles.qualification as tutor_qualification',
   // 'tutorprofiles.intro_video_link',
   // 'tutorprofiles.experience',
   // DB::raw('(tutorprofiles.rateperhour + (tutorprofiles.rateperhour * tutorprofiles.admin_commission / 100)) as
   rateperhour'),
   // 'tutorprofiles.profile_pic',
   // DB::raw('GROUP_CONCAT(DISTINCT subjects.name ORDER BY subjects.name SEPARATOR ", ") as subject'),
   // DB::raw('SUM(tutorreviews.ratings) / COUNT(tutorreviews.id) AS starrating'),
   // DB::raw('COUNT(DISTINCT topics.name) as total_topics'),
   // DB::raw('COUNT(DISTINCT zoom_classes.id) as total_classes_done')
   // )
   // ->leftJoin('teacherclassmappings', 'teacherclassmappings.teacher_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('tutorsubjectmappings', 'tutorsubjectmappings.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('subjects', 'subjects.id', '=', 'tutorsubjectmappings.subject_id')
   // ->leftJoin('classes', 'classes.id', '=', 'tutorsubjectmappings.class_id')
   // ->leftJoin('tutorreviews', 'tutorreviews.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('topics', 'topics.subject_id', '=', 'subjects.id')
   // ->join('tutorregistrations', 'tutorregistrations.id', '=', 'tutorprofiles.tutor_id')
   // ->leftJoin('zoom_classes', 'zoom_classes.tutor_id', '=', 'tutorprofiles.tutor_id')
   // ->where('tutorregistrations.is_active', 1)
   // ->when($subjectid, function ($query, $subjectid) {
   // return $query->where('subjects.id', $subjectid);
   // })
   // ->when($classid, function ($query, $classid) {
   // return $query->where('classes.id', $classid);
   // })
   // ->groupBy(
   // 'tutorprofiles.tutor_id',
   // 'tutorprofiles.name',
   // 'tutorprofiles.headline',
   // 'tutorprofiles.qualification',
   // 'tutorprofiles.intro_video_link',
   // 'tutorprofiles.experience',
   // 'tutorprofiles.rateperhour',
   // 'tutorprofiles.admin_commission',
   // 'tutorprofiles.profile_pic'
   // )
   // ->orderByRaw('profile_pic IS NOT NULL DESC, tutorregistrations.created_at DESC')
   // ->get();


         // Subject Categories with subjects count
            // $subjectcategories = DB::table('subjectcategories')
            //     ->select('subjectcategories.*', DB::raw('COUNT(subjects.id) as subject_count'))
            //     ->leftJoin('subjects', 'subjectcategories.id', '=', 'subjects.category')
            //     ->where('subjectcategories.is_active', 1)
            //     ->groupBy('subjectcategories.id', 'subjectcategories.name', 'subjectcategories.category_image', 'subjectcategories.is_active', 'subjectcategories.created_at', 'subjectcategories.updated_at')
            //     ->get();
