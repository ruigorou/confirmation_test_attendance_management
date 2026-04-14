<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegularMemberRegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function member_registration () {
        return view('regular_member_register');
    }

    public function store (RegularMemberRegisterRequest $request) {
        $user = User::where('email', $request->email)->first();

        if(!$user) {
            $user = $this->user_store($request);
        }
        $user->sendEmailVerificationNotification();
        Auth::login($user);
        return redirect()->route('verification.notice', compact('user'));
    }

    public function user_store (RegularMemberRegisterRequest $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $user;
    }

}
