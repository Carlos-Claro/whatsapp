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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string("wam_id");
            $table->string("name");
            $table->string("wa_id");
            $table->string("type");
            $table->text("body");
            $table->text("caption")->nullable(true)->default(null);
            $table->text("data")->nullable(true)->default(null);
            $table->string("status");
            $table->integer("contact_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
