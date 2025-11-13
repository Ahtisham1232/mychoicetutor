<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecordingStorageService
{
    /**
     * Storage type: 'local' or 'dropbox'
     */
    private $storageType;

    /**
     * Base storage path
     */
    private $storagePath;

    public function __construct()
    {
        $this->storageType = env('RECORDING_STORAGE_TYPE', 'local');
        $this->storagePath = storage_path('app/public/recordings');
        
        // Create storage directory if it doesn't exist
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Save recording file to local storage
     *
     * @param string $recordingUrl URL or path to recording file
     * @param int $tutorId
     * @param int $studentId
     * @param string $subjectName
     * @param string $meetingId
     * @return array
     */
    public function saveRecording(
        string $recordingUrl,
        int $tutorId,
        int $studentId,
        string $subjectName,
        string $meetingId
    ): array {
        try {
            // Generate file name
            $fileName = $this->generateFileName($tutorId, $studentId, $subjectName, $meetingId);
            
            // Create directory structure: recordings/YYYY/MM/DD/
            $datePath = date('Y/m/d');
            $directory = $this->storagePath . '/' . $datePath;
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $filePath = $directory . '/' . $fileName;
            $relativePath = 'recordings/' . $datePath . '/' . $fileName;
            
            // If recordingUrl is a URL, download it
            if (filter_var($recordingUrl, FILTER_VALIDATE_URL)) {
                $fileContents = file_get_contents($recordingUrl);
                if ($fileContents === false) {
                    throw new \Exception('Failed to download recording from URL');
                }
                file_put_contents($filePath, $fileContents);
            } else {
                // If it's already a local path, copy it
                if (file_exists($recordingUrl)) {
                    copy($recordingUrl, $filePath);
                } else {
                    throw new \Exception('Recording file not found');
                }
            }
            
            // Get file size
            $fileSize = filesize($filePath);
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'relative_path' => $relativePath,
                'storage_url' => asset('storage/' . $relativePath),
                'file_size' => $fileSize,
                'file_name' => $fileName,
            ];
        } catch (\Exception $e) {
            Log::error('Recording storage failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate unique file name for recording
     *
     * @param int $tutorId
     * @param int $studentId
     * @param string $subjectName
     * @param string $meetingId
     * @return string
     */
    private function generateFileName(int $tutorId, int $studentId, string $subjectName, string $meetingId): string
    {
        $cleanSubject = preg_replace('/[^a-z0-9]/i', '_', $subjectName);
        $timestamp = date('Y-m-d_H-i-s');
        $uniqueId = substr(md5($meetingId), 0, 8);
        
        return "recording_{$tutorId}_{$studentId}_{$cleanSubject}_{$timestamp}_{$uniqueId}.mp4";
    }

    /**
     * Get recording file URL for download/view
     *
     * @param string $relativePath
     * @return string
     */
    public function getRecordingUrl(string $relativePath): string
    {
        $fullPath = storage_path('app/public/' . $relativePath);
        
        if (file_exists($fullPath)) {
            return asset('storage/' . $relativePath);
        }
        
        return '';
    }

    /**
     * Delete recording file
     *
     * @param string $relativePath
     * @return bool
     */
    public function deleteRecording(string $relativePath): bool
    {
        try {
            $fullPath = storage_path('app/public/' . $relativePath);
            
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Recording deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recording file size in human readable format
     *
     * @param string $relativePath
     * @return string
     */
    public function getRecordingSize(string $relativePath): string
    {
        $fullPath = storage_path('app/public/' . $relativePath);
        
        if (file_exists($fullPath)) {
            $bytes = filesize($fullPath);
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            
            return round($bytes, 2) . ' ' . $units[$i];
        }
        
        return 'Unknown';
    }
}

