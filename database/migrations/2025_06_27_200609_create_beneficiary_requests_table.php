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
        Schema::create('beneficiary_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->CascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->CascadeOnDelete();

            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('father_name_ar')->nullable();
            $table->string('father_name_en')->nullable();
            $table->string('mother_name_ar')->nullable();
            $table->string('mother_name_en')->nullable();
            $table->string('gender_ar')->nullable();
            $table->string('gender_en')->nullable();
            $table->date('birth_date');
            $table->string('marital_status_ar')->nullable();
            $table->string('marital_status_er')->nullable();
            $table->integer('num_of_members');
            $table->string('study_ar')->nullable();
            $table->string('study_en')->nullable();
            $table->boolean('has_job');
            $table->string('job_ar')->nullable();
            $table->string('job_en')->nullable();
            $table->string('housing_type_ar')->nullable();
            $table->string('housing_type_en')->nullable();
            $table->boolean('has_fixed_income');
            $table->string('fixed_income')->nullable();
            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();
            $table->string('phone');
            $table->string('main_category');
            $table->string('sub_category');
            $table->text('notes_ar')->nullable();
            $table->text('notes_en')->nullable();
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
        Schema::dropIfExists('beneficiary_requests');
    }
};
