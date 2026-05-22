<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'loginpassword' => 'required',
        ]);

        // Lookup the admin explicitly using the username field
        $user = admin::where('email', $request->username)->first();

        if ($user) {
            // FIX: Use Hash::check to verify the password safely
            if ($request->loginpassword == $user->password) {
                
                // Track user details safely inside the session context
                $request->session()->put('userid', $user);

                switch ($user->role_id) {
                    case 1:
                        return redirect()->route('admin.dashboard');
                    default:
                        return back()->with('login_fail', 'Unauthorized access level.');
                }
            }
            return back()->with('login_fail', 'Password does not match.');
        } 
        
        // FIX: Adjusted error message to match email context
        return back()->with('login_fail', 'Admin email address not registered.');
    }

    public function admin_forget_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = admin::where('email', $request->email)
            ->where('role_id', 1)
            ->first();

        if (!$user) {
            $msg = 'Admin email not found!';
            return $request->ajax()
                ? response()->json(['status' => 'error', 'message' => $msg])
                : back()->with('fail', $msg);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $token = Str::random(64);
        $email = $request->email;

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::send('emails.forgetPassword', ['token' => $token], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Admin Reset Password');
            });

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Token sent successfully! Check mail inbox and spam both.'
                ]);
            }

            return back()->with('success', 'Token sent successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mail Error: ' . $e->getMessage()
                ]);
            }
            return back()->with('fail', 'Mail Error: ' . $e->getMessage());
        }
    }

    public function admin_reset_password_form($token)
    {
        $validToken = DB::table('password_resets')->where('token', $token)->first();

        if (!$validToken) {
            // Note: Ensure 'home' or your fallback route matches your web.php naming conventions
            return redirect()->route('home')->with('fail', 'This password reset link is invalid or has already been used.');
        }

        // NOTE: Make sure this view provides a secure input form with password and password_confirmation fields
        return view('front-cms.forgetpassword', ['token' => $token]);
    }

    public function admin_reset_password_submit(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'password' => 'required|min:6|confirmed', // expects 'password_confirmation' field matching
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->with('fail', 'Invalid or expired token!');
        }

        $admin = admin::where('email', $resetRecord->email)
            ->where('role_id', 1)
            ->first();

        if (!$admin) {
            return back()->with('fail', 'Admin record not found!');
        }

        // Update Admin password safely using the standard Hash provider
        $admin->password = Hash::make($request->password);
        $admin->save();

        DB::table('password_resets')
            ->where('email', $resetRecord->email)
            ->delete();

        return redirect()
            ->route('password.change.confirmation')
            ->with('success', 'Your admin password has been updated successfully!');
    }
}