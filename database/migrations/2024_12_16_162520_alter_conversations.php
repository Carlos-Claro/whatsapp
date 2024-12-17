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
        Schema::table('conversations', function(Blueprint $table) {
            $table->dropColumn([
                'contact_id',
                'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function(Blueprint $table){
            $table->integer('contact_id')->after('id')->nullable(false);
            $table->integer('department_id')->after('contact_id')->nullable(true);
        });
    }
};
