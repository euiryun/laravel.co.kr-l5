<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Post;
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
//        if (!$validator->passes()) {
//            return Redirect::to('posts/' . $category . '/' . 'new')
//                    ->withInput(Input::all())
//                    ->withErrors($validator)
//                    ->with('category', $category);
//        }

        $post           = new Post;
        $post->title    = $request->input('title');
        $post->content  = $request->input('content');
        $post->category = $category;

        Auth::user()->posts()->save($post);

        return Redirect::to('posts/' . $post->id)->with('success', '글이 등록 되었습니다.');
    }

    /**
    * Edit a post
    */
    public function postEdit($postId)
    {
        $validator = $this->getValidator();
        if (!$validator->passes()) {
            return Redirect::to('posts/' . $postId . '/edit')
                ->withInput(Input::all())
                ->withErrors($validator);
        }

        $post           = Post::find($postId);
        $post->title    = Input::get('title');
        $post->content  = Input::get('content');
        $post->category = Input::get('category');
        $post->save();

        return Redirect::to('posts/' . $post->id)->with('success', '글이 수정 되었습니다.');
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
    */
    public function getCreate($category)
    {
        $markdown = app('Ciconia\Ciconia');
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
    */
    public function getById($postId)
    {
        $post = Post::find($postId);
        $post->views++;
        $post->save();

        $content = App('Ciconia\Ciconia')->render($post->content);
        $category = $post->category;

        return view('posts.view', compact('post', 'content', 'category'));
    }

    /**
    * Edit a post
    */
    public function getEdit($postId)
    {
        $post = Post::find($postId);

        if(Auth::check() && $post->user_id == Auth::user()->id) {
          return View::make('posts.edit')->with([
            'post'     => $post,
            'markdown' => App::make('Ciconia\Ciconia'),
            'category' => $post->category
          ]);
        }

        return Redirect::to('posts/' . $postId);
    }
}