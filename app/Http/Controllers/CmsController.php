<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Faq;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;


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

    public function contact()
    {
        return view('front-cms/contact');
    }

    public function contactSave(Request $request)
    {
        // 1. Validate request
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'message' => 'required|string|max:500',
        ]);

        // 2. Get IP address
        $ip = $request->ip();

        // 3. Daily limit check (5 messages per day)
        $dailyCount = ContactMessage::where(function ($query) use ($validated, $ip) {
            $query->where('email', $validated['email'])
                ->orWhere('ip_address', $ip);
        })
            ->whereDate('created_at', today())
            ->count();

        if ($dailyCount >= 5) {
            return back()->with(
                'error',
                'Your daily contact request limit has been reached. Please try again tomorrow.'
            );
        }

        // 4. Check duplicate message within 5 minutes
        $isDuplicate = ContactMessage::where('email', $validated['email'])
            ->where('message', $validated['message'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($isDuplicate) {
            return back()->with('error', 'You already sent a similar message recently.');
        }

        // 5. Save message
        $contact = ContactMessage::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'message'    => $validated['message'],
            'ip_address' => $ip,
        ]);

        // 6. Send email to admin
        Mail::to('mychoicetutor@gmail.com')->send(new ContactUsMail($contact));

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
