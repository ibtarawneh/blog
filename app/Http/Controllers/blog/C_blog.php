<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Add this line
use Auth;

class C_blog extends Controller
{
    // Display a listing of the blogs
    public function index()
    {
        //$blogs = Blog::with('user')->get();
        $blogs = Blog::get();
        return $blogs;
    }

    // Store a newly created blog in storage
   
    















    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => 'required|exists:users,id',
        ]);
    
        $blogData = [
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'user_id' => $validatedData['user_id'],
        ];
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('images', $imageName, 'public');
            $blogData['image'] = $imagePath;
            $blogData['image_url'] = Storage::url($imagePath);
        }
    
        $blog = Blog::create($blogData);
    
        return response()->json([
            'message' => 'Blog created successfully!',
            'blog' => $blog,
            'image_url' => $blogData['image_url'] ?? null,
        ], 201);
    }
    







// Get the top two blogs with the largest views or likes for a specific user
public function topBlogsByUserId($userId)
{
    $topBlogs = Blog::where('user_id', $userId)
        ->orderBy('views', 'desc') // Change 'views' to 'likes' if you want to sort by likes
        ->take(2)
        ->with('user') // Optional: Eager load the related user if needed
        ->get();

    return response()->json($topBlogs);
}






    

     // Display the specified blog
     public function show($id)
{
    // Retrieve the blog post along with the related user
    $blog = Blog::with('user')->findOrFail($id);

    // Return the blog data as JSON, including the user's name and image
    return response()->json([
        'blog' => $blog,
        'user' => [
            'id' => $blog->user->id,
            'name' => $blog->user->name,
            'email' => $blog->user->email,
            'image' => $blog->user->image, // Include the user's image
        ],
    ]);
}

    // Display the specified blog
    public function view($id)
    {
        // Attempt to find the blog post by ID
        $blog = Blog::find($id); // Using find to return null instead of throwing an exception

        // Check if the blog post was found
        if (!$blog) {
            abort(404); // Return 404 if not found
        }

        // Increment views
        $blog->increment('views');

        // Return the blog post (as JSON or to a view)
        return $blog; // Example of returning a view
    }



    // Update the specified blog in storage
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'image' => 'nullable|string',
            'id' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $id = $request['id'];


        $blog = Blog::findOrFail($id);
        $blog->update($validatedData);

        return response()->json($blog);
    }

    // Remove the specified blog from storage
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    public function blog_user($id) {
        // Fetch all blog entries where the user_id matches the provided id
        $blogs = Blog::where('user_id', $id)->get();
    
        // Return all blogs as a JSON array
        return response()->json($blogs);
    }
    
    
}
