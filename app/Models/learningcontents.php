<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class learningcontents extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'topic_id',
        'tutor_id',
        'student_ids',
        'content_link',
        'content_description',
        'video_link',
        'video_description',
        'blog_link',
        'blog_description',
        'is_active'
    ];

    protected $casts = [
        'student_ids' => 'array',
    ];
}
