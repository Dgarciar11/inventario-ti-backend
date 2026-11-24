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
            $table->string('ldap_guid', 64)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('ldap_username', 255)
                ->nullable()
                ->unique()
                ->after('ldap_guid');

            $table->string('employee_id', 50)
                ->nullable()
                ->after('ldap_username');

            $table->boolean('is_active')
                ->default(true)
                ->after('employee_id');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ldap_guid',
                'ldap_username',
                'employee_id',
                'is_active',
                'last_login_at',
            ]);
        });
    }
};
