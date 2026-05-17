<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Faq;

class FaqsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetching all FAQs ordered by newest first
        $faqs = Faq::orderby('created_at', 'desc')->paginate(10);
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created or updated resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request - matching your SQL table fields
        $request->validate([
            'question' => 'required|string',
            'answer'   => 'required|string',
        ]);

        // Check if the request has an ID for Update, otherwise Create new
        if ($request->id) {
            $data = Faq::findOrFail($request->id);
        } else {
            $data = new Faq();
        }

        // Assign the values based on your SQL structure
        $data->question = $request->question;
        $data->answer = $request->answer;

        // Save the FAQ data
        $data->save();

        // Redirect to the FAQ list page with a success message
        return redirect()->route('admin.faqs.list')->with('success', 'FAQ saved successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        // Using 'create' view as per your single-file form approach
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Toggle the active status (1 to 0 or 0 to 1)
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:faqs,id',
        ]);

        $faq = Faq::findOrFail($request->id);

        // Toggle logic: If currently 1, set to 0. If 0, set to 1.
        $faq->is_active = ($faq->is_active == 1) ? 0 : 1;
        $faq->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Status updated successfully',
            'newStatus' => $faq->is_active
        ]);
    }

    /**
     * Optional: Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
        return redirect()->back()->with('success', 'FAQ deleted successfully');
    }
}
