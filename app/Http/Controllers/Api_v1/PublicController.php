<?php

namespace App\Http\Controllers\Api_v1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    
    public function countries() {
        return response()->json([
            'countries' => Country::all(['phone_code', 'flag'])->map(function($ar) {
                return [
                    'phone_code' => $ar->phone_code,
                    'flag' => $ar->flag
                ];
            })
        ]);
    }

}
