<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAssignments extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'submission_link',
        'submitted_on',
        'submitted_by',
        'reamrks',
        'results',
        'is_active'
    ];
}
