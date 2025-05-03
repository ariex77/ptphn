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
        Schema::create('pengaturan_umum', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('total_jam_bulan')->nullable();
            $table->boolean('denda')->default(false);
            $table->boolean('face_recognition')->default(false);
            $table->string('periode_laporan_dari')->nullable();
            $table->string('periode_laporan_sampai')->nullable();
            $table->boolean('periode_laporan_next_bulan')->default(false);
            $table->string('cloud_id')->nullable();
            $table->string('api_key')->nullable();
            $table->string('logo')->nullable();
            $table->string('domain_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturanumums');
    }
};
