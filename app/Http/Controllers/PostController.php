<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Post;
use Ciconia\Ciconia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PostController extends BaseController
{
    /**
     * Create a post
     * @param CreatePostRequest $request
     * @param $category
     * @return
     */
    public function postCreate(CreatePostRequest $request, $category)
    {
        $post           = new Post;
        $post->title    = $request->title;
        $post->content  = $request->content;
        $post->category = $category;

        Auth::user()->posts()->save($post);

        return redirect()->route('post.view', ['postId' => $post->id])
                ->with('success', '글이 등록 되었습니다.');
    }

    /**
     * Edit a post
     * @param UpdatePostRequest $request
     * @param $postId
     * @return
     */
    public function postEdit(UpdatePostRequest $request, $postId)
    {
        $post           = Post::find($postId);
        $post->title = $request->title;
        $post->content = $request->content;
        $post->category = $request->category;
        $post->save();

        return redirect()->route('post.view', [
            'postId' => $post->id
        ])->with('success', '글이 수정 되었습니다.');
    }

    /**
     * Delete a post
     */
    public function getDelete($postId)
    {
        $post = Post::find($postId);

        if(Auth::check() && $post->user_id == Auth::user()->id) {
            $category = $post->category;
            $post->delete();

            return Redirect::to('posts/' . $category)->with('success', '글이 삭제 되었습니다.');
        }

        return Redirect::to('posts/' . $postId);
    }

    /**
     * Create a post
     * @param Ciconia $markdown
     * @param $category
     * @return View
     */
    public function getCreate(Ciconia $markdown, $category)
    {
        return view('posts.create', compact('category', 'markdown'));
    }

    /**
    * Display posts by category
    */
    public function getByCategory($category = 'all')
    {
        if($category == 'all') {
            $posts = Post::with('user')->orderBy('id', 'desc')->paginate(15);
        } else {
            $posts = Post::with('user')->where('category', $category)->orderBy('id', 'desc')->paginate(15);
        }

        return view('posts.index', compact('posts', 'category'));
    }

    /**
     * Display a post
     * @param Request $request
     * @param Ciconia $markdown
     * @param $postId
     * @return View
     */
    public function getById(Request $request, Ciconia $markdown, $postId)
    {
        $post = Post::find($postId);
        $post->views++;
        $post->save();

        $content = $markdown->render($post->content);
        $category = $post->category;

        $signedInUser = $request->user();

        return view('posts.view', compact('post', 'content', 'category', 'signedInUser'));
    }

    /**
     * Edit a post
     * @param Request $request
     * @param Ciconia $markdown
     * @param $postId
     * @return View
     */
    public function getEdit(Request $request, Ciconia $markdown, $postId)
    {
        $post = Post::find($postId);
        $user = $request->user();

        if (!$user) {
            return Redirect::to('posts/' . $postId);
        }

        if ($user->id === $post->userId()) {
            $category = $post->category;
            return view('posts.edit', compact('post', 'markdown', 'category'));
        }
    }
}