<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sponsorship_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('file_url');
            $table->timestamps();


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
