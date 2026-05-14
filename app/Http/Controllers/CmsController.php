<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Faq;

class CmsController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', 1)->orderBy('created_at', 'desc')->get();
        return view('cms.faqs.index', compact('faqs'));
    }
}
