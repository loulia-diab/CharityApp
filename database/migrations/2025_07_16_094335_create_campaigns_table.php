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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->CascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->CascadeOnDelete();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('image')->nullable();
            $table->longText('description_en');
            $table->longText('description_ar');
            $table->string('status');
            $table->double('goal_amount')->default(0);
            $table->double('collected_amount')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
