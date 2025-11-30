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
        Schema::create('presentation_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('prompt')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('score')->nullable();
            $table->json('criteria_scores')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentation_evaluations');
    }
};
