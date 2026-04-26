<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master Pelanggaran
        Schema::create('bk_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggaran');
            $table->integer('poin')->default(0);
            $table->timestamps();
        });

        // Konsultasi
        Schema::create('bk_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->enum('jenis_masalah', ['pribadi', 'akademik', 'sosial', 'disiplin']);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['pending', 'dijadwalkan', 'selesai'])->default('pending');
            $table->timestamps();
        });

        // Jadwal Konsultasi
        Schema::create('bk_jadwal_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_konsultasi_id')->constrained('bk_konsultasis')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam');
            $table->string('guru_bk')->nullable();
            $table->string('ruang')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Poin Siswa (catatan pelanggaran per siswa)
        Schema::create('bk_poin_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('bk_pelanggaran_id')->constrained('bk_pelanggarans')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Solusi & Tindak Lanjut
        Schema::create('bk_solusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_konsultasi_id')->constrained('bk_konsultasis')->onDelete('cascade');
            $table->text('solusi')->nullable();
            $table->enum('tindakan', ['konseling_lanjutan', 'panggilan_orang_tua', 'surat_peringatan']);
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bk_solusi');
        Schema::dropIfExists('bk_poin_siswas');
        Schema::dropIfExists('bk_jadwal_konsultasis');
        Schema::dropIfExists('bk_konsultasis');
        Schema::dropIfExists('bk_pelanggarans');
    }
};
