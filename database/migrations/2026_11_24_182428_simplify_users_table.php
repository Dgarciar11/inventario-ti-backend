<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['ldap_guid']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'password',
                'remember_token',
                'email_verified_at',
                'ldap_guid',
                'employee_id',
            ]);

            $table->string('rol')->default('usuario')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');

            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('ldap_guid')->nullable();
            $table->string('employee_id')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
            $table->unique('ldap_guid');
        });
    }
};
