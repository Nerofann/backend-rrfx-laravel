<?php

namespace Database\Seeders;

use App\Models\UserGender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("genders")->insertOrIgnore([
            [
                'name' => "Laki-laki",
                'code' => "L",
                'created_at' => date("Y-m-d H:i:s") 
            ],
            [
                'name' => "Perempuan",
                'code' => "P",
                'created_at' => date("Y-m-d H:i:s") 
            ]
        ]);
    }
}
