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
        Schema::table('users', function (Blueprint $table) {
            $table->after('email_verified_at', function ($table) {
                $table->string('avatar')->nullable();
                $table->string('phone_no')->nullable();
                $table->boolean('onboarded')->default(false);
                $table->integer('dob_year')->nullable();
                $table->integer('dob_month')->nullable();
                $table->integer('dob_date')->nullable();
                $table->string('gender')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('phone_no');
            $table->dropColumn('onboarded');
            $table->dropColumn('dob_year');
            $table->dropColumn('dob_date');
            $table->dropColumn('dob_month');
            $table->dropColumn('gender');
        });
    }
};
