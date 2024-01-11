<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);
        //render view with posts
        return view('posts.index', compact('posts'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('posts.create');
    }
    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);
        //upload image
        $image = $request->file('image');
        $unik = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('img'), $unik);
        //create post
        Post::create([
            'image'     => $unik,
            'title'     => $request->title,
            'content'   => $request->content
        ]);
        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit(Post $post) {
        return view('posts.edit', compact('post'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, $id)
    {
        //validate form
        $this->validate($request, [
            
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);



        $post = Post::findOrFail($id);
        //check if image is uploaded
        if ($request->hasFile('image')) {
            //upload new image
            $this->validate($request, [
                'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                
            ]); 
            $old = public_path('img'). '/' . $post->image;
            if (file_exists($old)){
                unlink($old);
            }         
            $image = $request->file('image');
            $unik = time(). '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $unik);  
            //update post with new image
            $post->update([
                'image'     => $unik,
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        } else {
            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }
        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(Post $post)
    {
        //delete image
        $old = public_path('img'). '/' . $post->image;
            if (file_exists($old)){
                unlink($old);
            }
        //delete post
        $post->delete();
        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

}

