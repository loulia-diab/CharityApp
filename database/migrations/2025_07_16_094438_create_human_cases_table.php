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
        Schema::create('human_cases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('beneficiary_id')
                ->constrained()
                ->onDelete('cascade');

            $table->boolean('is_emergency')->default(false);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('human_cases');
    }
};
