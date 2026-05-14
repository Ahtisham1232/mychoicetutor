<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blogs;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blogs::select('*')->orderby('created_at', 'desc')->get();
        return view('admin.blogs', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blogscreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Determine validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'editor1' => 'required|string',
        ];

        // If the request does not have an ID, the image is required
        if (!$request->id) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $rules['image'] = 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        // Validate the request
        $request->validate($rules);

        // Check if the request has an ID
        if ($request->id) {
            $data = Blogs::find($request->id);
        } else {
            $data = new Blogs();
        }

        // Assign the validated values
        $data->name = $request->title;
        $data->description = $request->editor1;

        // Handle the image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blogs'), $imageName);
            $data->image = $imageName;
        }
        // Handle the banner upload
        if ($request->hasFile('banner')) {
            $image = $request->file('banner');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blogs'), $imageName);
            $data->banner = $imageName;
        }

        // Save the blog data
        $data->save();

        // Redirect to the blogs index page
        return redirect()->to('/admin/blogs');
    }


    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:blogs,id',
        ]);

        $blog = Blogs::findOrFail($request->id);

        // Toggle status
        $blog->is_active = $request->status == 1 ? 0 : 1;
        $blog->save();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blogs $blogs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $blog = Blogs::select('*')->where('id', $id)->first();
        return view('admin.blogscreate', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blogs $blogs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blogs $blogs)
    {
        //
    }
}
