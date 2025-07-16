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
        Schema::create('assistance_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_request_id')->constrained('beneficiary_requests')->cascadeOnDelete();
            $table->string('field_name_ar')->nullable();
            $table->string('field_name_en')->nullable();
            $table->string('field_value_ar')->nullable();
            $table->string('field_value_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_details');
    }
};
