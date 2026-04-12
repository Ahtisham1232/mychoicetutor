<?php

namespace App\Models\payments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentdetails extends Model
{
    use HasFactory;
    const MODE_STRIPE = 'stripe';
    const MODE_PHYSICAL = 'physical';
}
