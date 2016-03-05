<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserInfoUpdateRequest;
use App\Http\Requests\UserLoginAttemptRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return
     */
    public function getEdit(Request $request)
    {
        return view('account.edit')->with('header', '수정')->with('user', $request->user());
    }


    /**
     * Post edit page
     * @param UserInfoUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(UserInfoUpdateRequest $request)
    {
        $user = $request->user();
        $user->nickname   = $request->nickname;
        $user->email   = $request->email;
        $user->about   = $request->about;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }


        $user->save();

        return redirect()->route('account.edit.form')->with('success', '회원정보가 수정 되었습니다.');
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
        return view('account.delete')->with('header', '탈퇴')->with('user', Auth::user());
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
