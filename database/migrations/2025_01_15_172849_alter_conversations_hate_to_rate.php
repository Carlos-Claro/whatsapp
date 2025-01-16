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
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'hate',
                'hate_annotation']);
            $table->text('rate_annotation')->after('status')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->integer('hate')->after('status')->default(5);
            $table->text('hate_annotation')->after('hate')->nullable(true);
        });
    }
};
