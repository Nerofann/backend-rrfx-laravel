<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile() {
        $user = (object) auth()->user()->load('gender');
        return ApiResponse::success("OK", [
            'fullname' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'created_at' => $user->created_at,
            'user_type' => $user->user_type,
            'place_of_birth' => $user->place_of_birth,
            'date_of_birth' => $user->date_of_birth,
            'address' => $user->address,
            'country_id' => $user->country_id,
            'phone' => $user->phone,
            'phone_code' => $user->phone_code,
            'gender' => $user->gender?->code,
            'is_verified' => $user->is_verified,
            'app_theme' => $user->app_theme,
        ]);
    }

}
