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


        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('beneficiary_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();
            $table->text('cancelled_note')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->dropColumn(['cancelled_note', 'cancelled_at']);
        });
    }
};
