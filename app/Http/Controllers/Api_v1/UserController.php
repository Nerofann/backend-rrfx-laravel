<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use App\Rules\AlphaSpace;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userinfo() {
        $user = (object) auth()->user();
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
            'is_verified' => $user->is_verified,
            'app_theme' => $user->app_theme,
        ]);
    }

    public function verificationStep1(Request $request) {
        $user = auth()->user();
        $validated = Validator::make($request->only(['fullname', 'gender', 'phone_code', 'phone']), [
            'fullname' => ['required', 'string', 'max:255', new AlphaSpace],
            'gender' => ['required', 'string'],
            'phone_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        if(!$validated->errors()->isEmpty()) {
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

    }
}
