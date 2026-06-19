<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('category_suggested')->nullable();
            $table->float('confidence')->default(0);
            $table->boolean('is_anomaly')->default(false);
            $table->string('anomaly_level', 10)->nullable(); // low, medium, high
            $table->text('anomaly_reason')->nullable();
            $table->text('reasoning')->nullable();
            $table->jsonb('recommendations')->nullable();
            $table->boolean('needs_review')->default(false);
            $table->string('user_feedback')->nullable();
            $table->integer('prompt_tokens')->nullable();
            $table->integer('completion_tokens')->nullable();
            $table->string('model_used')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
