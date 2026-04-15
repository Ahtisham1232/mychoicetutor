<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class subjects extends Model
{
    use HasFactory;
    public static function getUniqueSubjects()
    {
        return self::where('is_active', 1)
            ->select(DB::raw('MIN(id) as id'), 'name')
            ->groupBy('name')
            ->get();
    }
}
