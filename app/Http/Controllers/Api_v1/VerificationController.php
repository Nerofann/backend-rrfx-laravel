<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\User;
use App\Rules\AlphaSpace;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    
    public function step_1(Request $request) {
        $user = auth()->user();
        $validated = Validator::make($request->only(['fullname', 'gender', 'phone_code']), [
            'fullname' => ['required', 'string', 'max:255', new AlphaSpace],
            'gender' => ['required', 'string'],
            'phone_code' => ['required', 'string', 'max:5', 'exists:countries,phone_code'],
        ]);

        if(!$validated->errors()->isEmpty()) {
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

        /** check status */
        if($user->is_verified) {
            return ApiResponse::error("Invalid Status");
        }

        /** get validated input */
        $data = $validated->validated();

        /** check gender */
        $gender = Gender::where('code', strtoupper($data['gender']))->first();
        if(!$gender) {
            return ApiResponse::error("Invalid Gender");
        }

        /** Validasi nomor telepon */
        $phone = normalize_phone($request->get('phone_code'), $request->get('phone'));
        if(strlen($phone) < 9) {
            $validated->errors()->add('phone', 'Phone number must be at least 9 characters');
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

        /** check nomor telepon sudah digunakan / belum */
        $existingPhone = User::where('phone', $phone)->where('id', '!=', $user->id)->first();
        if ($existingPhone) {
            $validated->errors()->add('phone', 'Phone number already in use');
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

        /** update user */
        $updateData = [
            'name' => $data['fullname'],
            'gender_id' => $gender->id,
            'phone_code' => $data['phone_code'],
            'phone' => $phone,
            'address' => $request->get('address', '-'),
            'is_verified' => 1
        ];

        $update = User::where('id', '=', $user->id)->update($updateData);
        if(!$update) {
            return ApiResponse::error("Failed to update userdata");
        }

        return ApiResponse::success("Successfull");
    }

}
