<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_job', 30)->unique();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();

            // Data inventaris (dari Excel Anda)
            $table->string('lok_gedung', 100)->nullable();
            $table->string('lokasi_ruang', 100)->nullable();
            $table->string('computer_name', 100)->nullable();
            $table->string('merk', 100)->nullable();

            $table->string('kategori', 50)->nullable();
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');

            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->nullOnDelete();

            $table->date('tanggal_tugas')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->enum('status', ['DITUGASKAN', 'DIKERJAKAN', 'SELESAI', 'DIVERIFIKASI'])->default('DITUGASKAN');

            $table->text('hasil_pekerjaan')->nullable();
            $table->text('catatan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('prioritas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};