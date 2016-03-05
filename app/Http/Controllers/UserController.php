<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends BaseController
{
    /**
    * Show all users
    */
    public function getIndex()
    {
        $users = User::orderBy('id', 'desc')->paginate(16);

        return view('users.index', [
            'header' => '사용자',
            'users' => $users
        ]);
    }

    /**
    * Show user page
    */
    public function getById($userId)
    {
        $user = User::find($userId);
        $posts = $user->posts()->take(10)->orderBy('id', 'desc')->get();

        return view('users.view', compact('user', 'posts'));
    }

    /**
    * Show user's posts
    */
    public function getPostsById($userId)
    {
        $user = User::find($userId);
        $posts = $user->posts()->orderBy('id', 'desc')->paginate(15);

        return view('users.posts', compact('user', 'posts'));
    }
}