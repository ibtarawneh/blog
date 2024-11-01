<?php







namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Blog;
use App\Models\Blog\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Auth;
class C_comment extends Controller
{
    // Get all blogs
    public function index()
    {
        $blogs = Blog::with('user', 'comments')->get(); // Eager load user and comments
        return response()->json($blogs, Response::HTTP_OK);
    }

    // Get a single blog with comments
    public function show($id)
    {
        $blog = Blog::with('user', 'comments')->find($id);
        
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($blog, Response::HTTP_OK);
    }

    // Store a new blog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string',
        ]);

        $blog = Blog::create($validated);
        return response()->json($blog, Response::HTTP_CREATED);
    }

    // Update a blog
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image' => 'nullable|string',
        ]);

        $blog->update($validated);
        return response()->json($blog, Response::HTTP_OK);
    }

    // Delete a blog
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], Response::HTTP_NOT_FOUND);
        }

        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    // Store a comment for a blog
    public function storeComment(Request $request, $blogId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'text' => 'required|string',
        ]);

        $comment = new Comment($validated);
        $comment->blog_id = $blogId;
        $comment->save();

        return response()->json($comment, Response::HTTP_CREATED);
    }

    public function getComments($blogId)
    {
        // Fetch comments with user information
        $comments = Comment::with('user') // Eager load user data
            ->where('blog_id', $blogId)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'text' => $comment->text,
                    'like' => $comment->like,
                    'dislike' => $comment->dislike,
                    'user' => [
                        'name' => $comment->user->name, // Assuming the User model has a name attribute
                        'image' => $comment->user->image, // Assuming the User model has an image attribute
                    ],
                    'created_at' => $comment->created_at, // Optional: include created timestamp
                    'updated_at' => $comment->updated_at, // Optional: include updated timestamp
                ];
            });
    
        return response()->json($comments, Response::HTTP_OK);
    }
    

    // Delete a comment
public function destroyComment($blogId, $commentId)
{
    $comment = Comment::find($commentId);

    if (!$comment) {
        return response()->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
    }

    // Check if the authenticated user is the owner of the comment or the blog
    $user = auth()->user();
    $blog = Blog::find($blogId);

    if ($user->id === $comment->user_id || $user->id === $blog->user_id) {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
}

}
