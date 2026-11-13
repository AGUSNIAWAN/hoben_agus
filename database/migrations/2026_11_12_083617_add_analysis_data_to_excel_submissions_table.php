<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('excel_submissions', function (Blueprint $table) {
            $table->text('analysis_data')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('excel_submissions', function (Blueprint $table) {
            $table->dropColumn('analysis_data');
        });
    }
};
