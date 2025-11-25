<?php

if(!function_exists('generate_user_id')) {
    /** Generate unique user_id */
    function generate_user_id() {
        $lastUser = \App\Models\User::orderBy('id', 'desc')->first();
        if(!$lastUser) {
            return 1000000001;
        }
        return time() + $lastUser->id;
    }
}



if (!function_exists('normalize_phone')) {
    /**
     * Gabungkan kode negara + nomor telepon
     *
     * @param string $countryCode  contoh: "+62" atau "62"
     * @param string $phone        contoh: "8123456789" atau "08123456789" atau "628123456789"
     * @return string              contoh output: "+628123456789"
     */
    function normalize_phone(string $countryCode, string $phone): string
    {
        // Bersihkan semua karakter non-digit
        $countryCode = preg_replace('/\D+/', '', $countryCode);
        $phone       = preg_replace('/\D+/', '', $phone);

        // Jika nomor sudah mulai dengan country code → langsung gabungkan +countryCode+phone tanpa duplikasi
        if (str_starts_with($phone, $countryCode)) {
            return '+' . $phone;
        }

        // Jika nomor mulai dengan "0" → buang 0
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // Default: concat
        return '+' . $countryCode . $phone;
    }
}



if(!function_exists('generateUniqueUsername')) {
    function generateUniqueUsername(string $username): string
    {
        $base = Str::slug($username, '');

        $new = $base;
        $counter = Str::random(3) . rand(100, 999);
        while (\DB::table('users')->where('username', $new)->exists()) {
            $new = $base . $counter;
            $counter = Str::random(3) . rand(100, 999);
        }

        return $new;
    }
}