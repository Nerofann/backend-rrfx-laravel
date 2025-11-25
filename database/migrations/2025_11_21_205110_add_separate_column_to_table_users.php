<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('user_id')->unique()->after('id');
            $table->bigInteger('user_idspn')->after('user_id');
            $table->string('username', 100)->unique()->after('name');
            $table->string('code', 20)->unique()->after('username');
            $table->bigInteger('user_type')->default(0)->after('email');
            $table->integer('max_account')->default(3)->after('remember_token');
            $table->integer('max_account_multi')->default(3)->after('max_account');
            $table->integer('max_account_micro')->default(3)->after('max_account_multi');

            $table->string('oauth_id', 255)->nullable()->after('max_account_micro');
            $table->text('oauth_avatar')->nullable()->after('oauth_id');
            $table->string('oauth_provider', 100)->nullable()->after('oauth_avatar');
            $table->string('place_of_birth', 100)->nullable()->after('oauth_provider');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->foreignId('country_id')->nullable()->after('date_of_birth');
            $table->string('province', 100)->nullable()->after('country_id');
            $table->text('regency')->nullable()->after('province');
            $table->text('district')->nullable()->after('regency');
            $table->text('village')->nullable()->after('district');
            $table->string('postal_code', 20)->nullable()->after('village');
            $table->text('address')->nullable()->after('postal_code');
            $table->string('phone_code', 10)->nullable()->after('address');
            $table->string('phone', 20)->unique()->after('phone_code');
            $table->boolean('is_verified')->default(false)->after('phone');
            $table->enum('status', ['register', 'active', 'disabled'])->default('register')->after('is_verified');
            $table->enum('app_theme', ['light', 'dark'])->default('light')->after('status');
            $table->text('last_ip_address')->nullable()->after('app_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'user_idspn',
                'username',
                'code',
                'user_type',
                'max_account',
                'max_account_multi',
                'max_account_micro',
                'oauth_id',
                'oauth_avatar',
                'oauth_provider',
                'place_of_birth',
                'date_of_birth',
                'country_id',
                'province',
                'regency',
                'district',
                'village',
                'postal_code',
                'address',
                'phone_code',
                'phone',
                'is_verified',
                'status',
                'app_theme',
                'last_ip_address'
            ]);
        });
    }
};
