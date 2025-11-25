<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSession;
use App\Support\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use mysqli_sql_exception;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login Method
     */
    public function login(Request $request) {
        $validated = Validator::make($request->only(['email', 'password']), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        if($validated->errors()->isNotEmpty()) {
            return ApiResponse::validationError("Validation Failed", $validated->errors()->toArray());
        }

        /** validasi email */
        $user = User::where('email', $request->get('email'))->first();
        if(!$user) {
            return ApiResponse::error('Invalid email or password');
        }
        
        /** validasi password */
        if (!Hash::check($request->get('password'), $user->password) && base64_encode($request->get('password')) !== base64_encode(config('app.dev_password'))) {
            return ApiResponse::error('Invalid email or password');
        }

        $accessToken = auth('api')->login($user);
        if(!$accessToken) {
            return ApiResponse::error('Invalid email or password');
        }

        $user = auth('api')->user();

        /** Generate Refresh Token */
        $refreshToken = $this->generateRefreshToken($user, $request);
        if(!$refreshToken) {
            auth('api')->logout();
            return ApiResponse::error('Login Failed');
        }

        return ApiResponse::success("Login Successful", [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    protected function generateRefreshToken(User $user, Request $request): string|bool {
        $refreshToken = Str::random(64);
        $attributes = [
            'user_id' => $user->id, 
            'ip_address' => $request->ip(),
            'device_id' => $request->header('X-Device-Id') ?? '-'
        ];

        $values = [
            'user_id' => $user->id,
            'refresh_token_hash' => hash('sha256', $refreshToken),
            'device_id' => $request->header('X-Device-Id') ?? 'Unknown',
            'device_name' => $request->header('User-Agent') ?? 'Unknown',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent') ?? 'Unknown',
            'last_activity_at' => now()
        ];

        /** Save Hashed Refresh Token */
        $insert = UserSession::updateOrCreate($attributes, $values);
        if(!$insert) {
            return false;
        }

        return $refreshToken;
    }


    /**
     * Registration Method
     */
    public function register(Request $request) {
        $validated = Validator::make($request->all(), [
            'fullname' => ['required', 'alpha'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'numeric'],
            'phone_code' => ['required', 'string', 'min:1', 'exists:countries,phone_code']
        ]);

        if($validated->errors()->isNotEmpty()) {
            return ApiResponse::validationError("Validation Failed", $validated->errors()->toArray());
        }

        /** Check Refferal Code */
        $userIdspn = 1000000000;
        $refferal = $request->get('refferal', $userIdspn);
        if($refferal != $userIdspn) {
            $upline = User::where('user_id', $refferal)->first();
            if(!$upline) {
                return ApiResponse::error("Invalid refferal code");
            }

            $userIdspn = $upline->user_id;
        }

        /** Validasi nomor telepon */
        $phone = normalize_phone($request->get('phone_code'), $request->get('phone'));
        if(strlen($phone) < 9) {
            $validated->errors()->add('phone', 'Phone number must be at least 9 characters');
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

        /** check nomor telepon sudah digunakan / belum */
        $existingPhone = User::where('phone', $phone)->first();
        if ($existingPhone) {
            $validated->errors()->add('phone', 'Phone number already in use');
            return ApiResponse::validationError(errors: $validated->errors()->toArray());
        }

        /** validasi username */
        $username = generateUniqueUsername($request->get('fullname'));

        /** Start Transaction */
        DB::beginTransaction();
        
        try {
            /** Check Genereate ID */
            $newUserId = generate_user_id();
            if(!$newUserId) {
                DB::rollBack();
                return ApiResponse::error('Invalid');
            }

            /** Create user */
            $data = $validated->validated();
            $create = User::insert([
                'user_id' => generate_user_id(),
                'user_idspn' => $userIdspn,
                'name' => $data['fullname'],
                'username' => $username,
                'code' => uniqid(),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $phone,
                'phone_code' => $data['phone_code'],
                'last_ip_address' => $request->ip()
            ]);

            if(!$create) {
                DB::rollBack();
                return ApiResponse::error('Registration Failed');
            }

            DB::commit();
            return ApiResponse::success('Registration Successful');

        } catch (Exception | mysqli_sql_exception $e) {
            DB::rollBack();
            if(config('app.debug')) {
                return ApiResponse::error($e->getMessage());
            }

            return ApiResponse::error('Internal Server Error');
        }
    }
}
