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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->CascadeOnDelete();
            $table->foreignId('beneficiary_request_id')->constrained('beneficiary_requests')->CascadeOnDelete();
            $table->string('priority_ar')->nullable();
            $table->string('priority_en')->nullable();
            $table->boolean('is_sorted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
