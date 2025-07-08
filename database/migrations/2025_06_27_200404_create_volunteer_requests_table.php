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
        Schema::create('volunteer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->CascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->CascadeOnDelete();

            $table->string('full_name_ar')->nullable();
            $table->string('full_name_en')->nullable();
            $table->string('gender_ar')->nullable();
            $table->string('gender_en')->nullable();
            $table->date('birth_date');
            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();
            $table->string('study_qualification_ar')->nullable();
            $table->string('study_qualification_en')->nullable();
            $table->string('job_ar')->nullable();
            $table->string('job_en')->nullable();
            $table->string('preferred_times_ar')->nullable();
            $table->string('preferred_times_en')->nullable();
            $table->boolean('has_previous_volunteer');
            $table->string('previous_volunteer_ar')->nullable();
            $table->string('previous_volunteer_en')->nullable();
            $table->string('phone');
            $table->string('notes_ar')->nullable();
            $table->string('notes_en')->nullable();
            $table->string('status_ar')->nullable();
            $table->string('status_en')->nullable();
            $table->string('reason_of_rejection_ar')->nullable();
            $table->string('reason_of_rejection_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_requests');
    }
};
