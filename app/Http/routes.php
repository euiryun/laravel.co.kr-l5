<?php

use App\Post;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    $render4Doc = function ($page = 'introduction') {
        $cacheKey = "{$page}.md-cache";

        if (!Cache::get($cacheKey)) {
            if (!$file = File::get(base_path()."/../../shared/docs/{$page}.md")) {
                App::abort(404, "Unable to find {$page}.md");
            }

            $markdown = App::make('Ciconia\Ciconia');
            Cache::put($cacheKey, $markdown->render($file), Carbon::now()->addMinutes(10));
        }

        return View::make('docs.index')->with([
            'content' => Cache::get($cacheKey),
            'page'    => $page,
        ]);
    };


    Route::pattern('category', '[a-zA-Z]+');
    Route::pattern('postId', '\d+');

    Route::get('/', function () {
        return View::make('home')->with([
            'posts'      => Post::orderBy('id', 'desc')->take(15)->get(),
        ]);
    });

    Route::get('docs/5/{page?}', ['as' => 'doc5', 'uses' => function ($page = 'introduction') {}]);

    Route::get('docs/4.x/{page?}', ['as' => 'doc4', 'uses' => $render4Doc]);

    Route::get('docs/{page?}', $render4Doc);

    Route::get('search', function () {
        $query = Input::get('query');

        if ($query) {
            $posts = Post::with('user')
                ->where('title', 'like', '%'.$query.'%')
                ->orWhere('content', 'like', '%'.$query.'%')
                ->orderBy('id', 'desc')
                ->paginate(15);
        } else {
            $posts = false;
        }

        return View::make('search')->with([
            'posts'      => $posts,
            'query'      => $query,
        ]);
    });

    Route::get('changelog', function () {
        $path = __DIR__.'/../changelog.md';
        $markdown = App::make('Ciconia\Ciconia');

        return View::make('changelog')->with([
            'content' => ($markdown->render(File::get($path))),
        ]);
    });


    // Account
    Route::get('login', [
        'middleware' => 'guest',
        'uses' => 'AccountController@getLogin',
        'as' => 'login.form'
    ]);

    Route::post('login', 'AccountController@postLogin');
    Route::get('register', ['middleware' => 'guest', 'uses' => 'AccountController@getRegister']);
    Route::post('register', 'AccountController@postRegister');
    Route::get('logout', 'AccountController@getLogout');
    Route::get('account/edit', ['uses' => 'AccountController@getEdit']);
    Route::post('account/edit', ['uses' => 'AccountController@postEdit']);
    Route::get('account/delete', ['uses' => 'AccountController@getDelete']);
    Route::post('account/delete', ['uses' => 'AccountController@postDelete']);

    // Users
    Route::get('users/{userId}/posts', 'UserController@getPostsById');
    Route::get('users/{userId}/{username}/posts', 'UserController@getPostsById');
    Route::get('users/{userId}/{username?}', 'UserController@getById');
    Route::get('users', ['uses' => 'UserController@getIndex']);

    // Posts
    Route::get('posts/{category}', 'PostController@getByCategory');
    Route::get('posts/{category}/new', ['middleware' => 'auth', 'uses' => 'PostController@getCreate']);
    Route::get('posts/{postId}', 'PostController@getById');
    Route::get('posts/{postId}/edit', ['middleware' => 'auth', 'uses' => 'PostController@getEdit']);
    Route::get('posts', ['uses' => 'PostController@getByCategory']);
    Route::post('posts/{category}/new', ['uses' => 'PostController@postCreate']);
    Route::post('posts/{postId}/edit', ['uses' => 'PostController@postEdit']);
    Route::get('posts/{postId}/delete', ['middleware' => 'auth', 'uses' => 'PostController@getDelete']);

    // Static Pages
    Route::get('chat', ['uses' => 'PageController@getChat', 'as' => 'chat']);

});
