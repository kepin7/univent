<?php

namespace App\Models;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();

            // Kolom untuk menghubungkan ke User (siapa yang mendaftar)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom untuk menghubungkan ke Event (event mana yang didaftar)
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            // Kolom Status Pendaftaran (misal: pending, approved, rejected)
            $table->string('status')->default('pending');

            // Tambahkan kolom lain yang relevan dari form pendaftaran jika ada
            // $table->string('data_tambahan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};