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
        Schema::create('in_kind_beneficiary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('in_kind_id')->constrained('in_kinds');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_kind_beneficiary');
    }
};
