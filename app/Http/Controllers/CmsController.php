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
    public function howitworks()
    {
        return view('front-cms/howitworks');
    }

    public function whychooseus()
    {
        return view('front-cms/whychooseus');
    }

    public function aboutus()
    {
        return view('front-cms/about_us');
    }

    public function contact()
    {
        return view('front-cms/contact');
    }
    public function privacypolicy()
    {
        return view('front-cms/privacypolicy');
    }
    public function refundpolicy()
    {
        return view('front-cms/refundpolicy');
    }
    public function termsandconditions()
    {
        return view('front-cms/termsandconditions');
    }
}
