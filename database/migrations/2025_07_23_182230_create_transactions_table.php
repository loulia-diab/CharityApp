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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->CascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->CascadeOnDelete();

            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->CascadeOnDelete();
            $table->foreignId('box_id')->nullable()->constrained('boxes')->CascadeOnDelete();

            $table->enum('type', [
                'donation',        // تبرع من المستخدم إلى الجمعية
                'exchange',         // مصروف من الجمعية
                'recharge',    // شحن رصيد المستخدم
            ]);
            $table->enum('direction', ['in', 'out']);
            $table->decimal('amount');
            $table->string('pdf_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
