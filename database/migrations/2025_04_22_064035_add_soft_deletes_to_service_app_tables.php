<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('midwives', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('midwives', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('medicines', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
