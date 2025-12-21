<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->id();
            $table->string('event_title');
            $table->string('organizer_name');
            $table->string('organizer_type');
            $table->string('event_category');
            $table->text('event_description');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');
            $table->string('event_location');
            $table->string('registration_link')->nullable();
            $table->string('contact_person');
            $table->longText('event_poster')->nullable();

            // Kolom tambahan (digabung dari add_status migration)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};