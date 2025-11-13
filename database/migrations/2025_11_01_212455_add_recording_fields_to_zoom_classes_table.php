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
            // Add recording tracking fields if they don't exist
            if (!Schema::hasColumn('zoom_classes', 'recording_file_path')) {
                $table->string('recording_file_path')->nullable()->after('recording_link');
            }
            if (!Schema::hasColumn('zoom_classes', 'recording_status')) {
                $table->enum('recording_status', ['pending', 'processing', 'completed', 'failed'])->nullable()->after('recording_file_path');
            }
            if (!Schema::hasColumn('zoom_classes', 'recording_duration')) {
                $table->integer('recording_duration')->nullable()->comment('Duration in seconds')->after('recording_status');
            }
            if (!Schema::hasColumn('zoom_classes', 'recording_file_size')) {
                $table->bigInteger('recording_file_size')->nullable()->comment('File size in bytes')->after('recording_duration');
            }
            if (!Schema::hasColumn('zoom_classes', 'recording_started_at')) {
                $table->timestamp('recording_started_at')->nullable()->after('recording_file_size');
            }
            if (!Schema::hasColumn('zoom_classes', 'recording_completed_at')) {
                $table->timestamp('recording_completed_at')->nullable()->after('recording_started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_classes', function (Blueprint $table) {
            //
        });
    }
};
