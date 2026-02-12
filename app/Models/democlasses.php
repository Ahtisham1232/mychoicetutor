<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Pagination\Paginator;

class democlasses extends Model
{
    use HasFactory;
    protected $casts = [
        'slot_confirmed' => 'datetime',
    ];
}
