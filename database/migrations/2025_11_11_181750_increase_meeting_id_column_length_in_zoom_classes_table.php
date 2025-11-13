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
        Schema::table('zoom_classes', function (Blueprint $table) {
            // Change meeting_id to text type to handle large meeting IDs
            $table->text('meeting_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_classes', function (Blueprint $table) {
            // Revert back to string type (adjust if you know the original length)
            $table->string('meeting_id', 100)->change();
        });
    }
};