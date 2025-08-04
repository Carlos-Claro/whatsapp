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
        Schema::create('start_conversation', function (Blueprint $table) {
            $table->id();
            $table->integer('start_conversation_id')->nullable();
            $table->string('tag')->nullable();
            $table->integer('sequence')->nullable();
            $table->text('question')->nullable(true);
            $table->json('answer')->nullable(true);
            $table->string('type')->nullable(true);
            $table->integer('status')->nullable()->default(1);
            $table->integer('department_id')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('start_conversation');
    }
};
