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
        Schema::table('learningcontents', function (Blueprint $table) {
            $table->integer('tutor_id')->nullable()->after('topic_id');
            $table->json('student_ids')->nullable()->after('tutor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learningcontents', function (Blueprint $table) {
            $table->dropColumn(['tutor_id', 'student_ids']);
        });
    }
};
