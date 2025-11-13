<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\zoom_classes;
use App\Models\tutorregistration;
use App\Models\studentregistration;
use App\Models\subjects;
use App\Services\RecordingStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecordingsController extends Controller
{
    protected $recordingStorage;

    public function __construct(RecordingStorageService $recordingStorage)
    {
        $this->recordingStorage = $recordingStorage;
    }

    /**
     * Display all recordings in admin dashboard
     */
    public function index(Request $request)
    {
        $query = zoom_classes::select(
            'zoom_classes.*',
            'tutorregistrations.name as tutor_name',
            'tutorregistrations.mobile as tutor_mobile',
            'tutorregistrations.email as tutor_email',
            'studentregistrations.name as student_name',
            'studentregistrations.mobile as student_mobile',
            'subjects.name as subject_name',
            'subjects.id as subject_id'
        )
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftJoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->whereNotNull('zoom_classes.recording_link')
            ->where('zoom_classes.recording_link', '!=', '')
            ->orderBy('zoom_classes.created_at', 'desc');

        // Apply filters
        if ($request->filled('tutor_name')) {
            $query->where('tutorregistrations.name', 'like', '%' . $request->tutor_name . '%');
        }

        if ($request->filled('student_name')) {
            $query->where('studentregistrations.name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subjects.id', $request->subject_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('zoom_classes.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('zoom_classes.created_at', '<=', $request->date_to);
        }

        // Get all subjects for filter dropdown
        $subjects = subjects::where('is_active', 1)->orderBy('name')->get();

        $recordings = $query->paginate(20)->withQueryString();

        return view('admin.recordings.index', compact('recordings', 'subjects'));
    }

    /**
     * View/download recording with preview
     */
    public function view($id)
    {
        $recording = zoom_classes::select(
            'zoom_classes.*',
            'tutorregistrations.name as tutor_name',
            'studentregistrations.name as student_name',
            'subjects.name as subject_name'
        )
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftJoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->findOrFail($id);

        if (empty($recording->recording_link)) {
            return back()->with('error', 'Recording not found');
        }

        // Get recording URL for preview
        $recordingUrl = $recording->recording_link;
        if (strpos($recording->recording_link, 'recordings/') !== false) {
            // Local file - generate URL (check if it exists in public storage)
            $filePath = storage_path('app/public/' . $recording->recording_link);
            if (file_exists($filePath)) {
                $recordingUrl = asset('storage/' . $recording->recording_link);
            }
        }

        return view('admin.recordings.view', compact('recording', 'recordingUrl'));
    }

    /**
     * Get recording analytics/statistics
     */
    public function analytics(Request $request)
    {
        // Overall statistics
        $totalRecordings = zoom_classes::whereNotNull('recording_link')
            ->where('recording_link', '!=', '')
            ->count();

        $totalSize = zoom_classes::whereNotNull('recording_file_size')
            ->sum('recording_file_size');

        $averageDuration = zoom_classes::whereNotNull('recording_duration')
            ->avg('recording_duration');

        // Statistics per tutor
        $tutorStats = zoom_classes::select(
            'zoom_classes.tutor_id',
            'tutorregistrations.name as tutor_name',
            DB::raw('COUNT(*) as total_recordings'),
            DB::raw('SUM(recording_file_size) as total_size'),
            DB::raw('AVG(recording_duration) as avg_duration'),
            DB::raw('SUM(recording_duration) as total_duration')
        )
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->whereNotNull('zoom_classes.recording_link')
            ->where('zoom_classes.recording_link', '!=', '')
            ->groupBy('zoom_classes.tutor_id', 'tutorregistrations.name')
            ->orderBy('total_recordings', 'desc')
            ->get();

        // Statistics per subject
        $subjectStats = zoom_classes::select(
            'subjects.id',
            'subjects.name as subject_name',
            DB::raw('COUNT(*) as total_recordings'),
            DB::raw('SUM(recording_file_size) as total_size'),
            DB::raw('AVG(recording_duration) as avg_duration')
        )
            ->leftJoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->whereNotNull('zoom_classes.recording_link')
            ->where('zoom_classes.recording_link', '!=', '')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('total_recordings', 'desc')
            ->get();

        // Recent recordings
        $recentRecordings = zoom_classes::select(
            'zoom_classes.*',
            'tutorregistrations.name as tutor_name',
            'studentregistrations.name as student_name',
            'subjects.name as subject_name'
        )
            ->leftJoin('tutorregistrations', 'tutorregistrations.id', '=', 'zoom_classes.tutor_id')
            ->leftJoin('studentregistrations', 'studentregistrations.id', '=', 'zoom_classes.student_id')
            ->leftJoin('topics', 'topics.id', '=', 'zoom_classes.topic_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'topics.subject_id')
            ->whereNotNull('zoom_classes.recording_link')
            ->where('zoom_classes.recording_link', '!=', '')
            ->orderBy('zoom_classes.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.recordings.analytics', compact(
            'totalRecordings',
            'totalSize',
            'averageDuration',
            'tutorStats',
            'subjectStats',
            'recentRecordings'
        ));
    }

    /**
     * Download recording file
     */
    public function download($id)
    {
        $recording = zoom_classes::findOrFail($id);

        if (empty($recording->recording_link)) {
            return back()->with('error', 'Recording not found');
        }

        // If recording_link is a file path
        if (strpos($recording->recording_link, 'recordings/') !== false) {
            $filePath = storage_path('app/public/' . $recording->recording_link);
            
            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }

        // If it's a URL, redirect to it
        return redirect($recording->recording_link);
    }

    /**
     * Delete recording
     */
    public function destroy($id)
    {
        try {
            $recording = zoom_classes::findOrFail($id);

            if (!empty($recording->recording_link)) {
                // Delete file if it's stored locally
                if (strpos($recording->recording_link, 'recordings/') !== false) {
                    $this->recordingStorage->deleteRecording($recording->recording_link);
                }

                // Clear recording link from database
                $recording->recording_link = null;
                $recording->save();
            }

            return back()->with('success', 'Recording deleted successfully');
        } catch (\Exception $e) {
            Log::error('Recording deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete recording');
        }
    }

    /**
     * Update recording link (manual update)
     */
    public function updateRecordingLink(Request $request, $id)
    {
        $request->validate([
            'recording_link' => 'required|url',
        ]);

        try {
            $recording = zoom_classes::findOrFail($id);
            
            // If it's a URL and we want to save it locally
            if ($request->save_locally) {
                $saveResult = $this->recordingStorage->saveRecording(
                    $request->recording_link,
                    $recording->tutor_id,
                    $recording->student_id ?? 0,
                    $recording->topic_name ?? 'Recording',
                    $recording->meeting_id
                );

                if ($saveResult['success']) {
                    $recording->recording_link = $saveResult['relative_path'];
                } else {
                    return back()->with('error', 'Failed to save recording: ' . $saveResult['error']);
                }
            } else {
                $recording->recording_link = $request->recording_link;
            }

            $recording->save();

            return back()->with('success', 'Recording link updated successfully');
        } catch (\Exception $e) {
            Log::error('Recording link update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update recording link');
        }
    }
}

