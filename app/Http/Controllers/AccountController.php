<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserLoginAttemptRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{
    /**
    * Show login page
    */
    public function getLogin()
    {
        return view('account.login');
    }

    /**
     * Post login
     * @param UserLoginAttemptRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(UserLoginAttemptRequest $request)
    {
        $username = $request->username;
        $password = $request->password;

        if (Auth::attempt(compact('username', 'password'))) {
            return redirect('')->with('success', '로그인 되었습니다.');
        } else {
            $request->flashOnly('username');
            return redirect()->route('login.form')->with('error', '아이디또는 비밀번호가 잘못되었습니다.');
        }
    }

    /**
    * Show register page
    */
    public function getRegister()
    {
        return view('account/register');
    }

    /**
     * Post register page
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRegister(CreateUserRequest $request)
    {
        $user = new User;
        $user->username   = $request->input('username');
        $user->email      = $request->input('email');
        $user->password   = Hash::make($request->input('password'));
        $user->nickname   = $request->input('nickname');
        $user->save();

        $user->roles()->attach(3, [
          'created_at'  => Carbon::now(),
          'updated_at'  => Carbon::now()
        ]);

        return redirect('')->with('success', '회원가입이 되었습니다.');
    }


    /**
    * Show edit page
    */
    public function getEdit()
    {
        return view('account.edit')->with('header', '수정')->with('user', Auth::user());
    }


    /**
    * Post edit page
    */
    public function postEdit()
    {

        $user = Auth::user();

        $rules = [
            'nickname'  => 'required|unique:users,nickname,' . $user->id . '|min:2|max:10',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'about'     => 'max:100',
            'password'  => 'min:5|max:20'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $user->nickname   = Input::get('nickname');
            $user->email   = Input::get('email');
            $user->about   = Input::get('about');

            if(Input::has('password')) {
                $user->password = Hash::make(Input::get('password'));
            }

            $user->save();

            return Redirect::to('account/edit')->with('success', '회원정보가 수정 되었습니다.');
        }

        return Redirect::to('account/edit')->withInput(Input::all())->withErrors($validator);
    }

    /**
    * Logout
    */
    public function getLogout()
    {
        Auth::logout();
        return Redirect::back()->with('success', '로그아웃 되었습니다.');
    }

    /**
    * Delete
    */
    public function getDelete()
    {
        return View::make('account.delete')->with('header', '탈퇴')->with('user', Auth::user());
    }

    /**
    * Delete
    */
    public function postDelete()
    {
        $user= Auth::user();
        $user->posts()->delete();
        $user->delete();
        Auth::logout();
        return Redirect::to('/')->with('success', '탈퇴 되었습니다.');
    }
}
