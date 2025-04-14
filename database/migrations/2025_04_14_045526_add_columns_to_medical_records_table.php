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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->text('complaint')->nullable()->change();
            $table->text('diagnosis')->nullable()->change();
            $table->text('medical_history')->nullable();
            $table->text('examination_results')->nullable();
            $table->text('medical_treatment')->nullable();
            $table->text('consultation_and_follow_up')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('medical_history');
            $table->dropColumn('examination_results');
            $table->dropColumn('medical_treatment');
            $table->dropColumn('consultation_and_follow_up');
        });
    }
};
