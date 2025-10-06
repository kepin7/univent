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
        Schema::table('account_roles', function (Blueprint $table) {
            if (!Schema::hasColumn('account_roles', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('account_roles', 'role_id')) {
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('account_roles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }

};