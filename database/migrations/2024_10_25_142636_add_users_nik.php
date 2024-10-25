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
            $table->string('nik')->nullable(); // Menambahkan kolom nik
            $table->string('face_verification')->nullable(); // Menambahkan kolom face_verification
            $table->boolean('is_verified')->default(false); // Menambahkan kolom is_verified
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nik'); // Menghapus kolom nik
            $table->dropColumn('face_verification'); // Menghapus kolom face_verification
            $table->dropColumn('is_verified'); // Menghapus kolom is_verified
        });
    }
};