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
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('jenis_pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('profesi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori_tenaga')->nullable();
            $table->timestamps();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tmt_mulai')->nullable();
            $table->date('tmt_selesai')->nullable();
            $table->string('sk_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pangkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pejabat_penetap')->nullable();
            $table->date('tmt_sk')->nullable();
            $table->string('sk_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('golongan_ruang', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('jenis_sip', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('jenis_diklat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kategori_diklat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nik')->unique();
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->foreignId('jenis_pegawai_id')->nullable()->constrained('jenis_pegawai')->nullOnDelete();
            $table->foreignId('profesi_id')->nullable()->constrained('profesi')->nullOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->enum('status_pegawai', ['aktif', 'tidak aktif'])->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->foreignId('pangkat_id')->nullable()->constrained('pangkat')->nullOnDelete();
            $table->foreignId('golongan_ruang_id')->nullable()->constrained('golongan_ruang')->nullOnDelete();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->date('tmt_pangkat_akhir')->nullable();
            $table->date('masa_kerja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pegawai_pribadi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->unique()->constrained('pegawai')->cascadeOnDelete();
            $table->enum('pendidikan_terakhir', ['SMA/SMK Sederajat', 'D3', 'S1/D4', 'S2', 'S3'])->nullable();
            $table->string('no_kk')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->string('link_kk')->nullable();
            $table->string('foto_path')->nullable();
            $table->string('ktp_file_path')->nullable();
            $table->string('kk_file_path')->nullable();
            $table->string('buku_nikah_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('profesi_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('profesi_id')->nullable()->constrained('profesi')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pangkat_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('pangkat_id')->nullable()->constrained('pangkat')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jabatan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('golongan_ruang_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('golongan_ruang_id')->nullable()->constrained('golongan_ruang')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('unit_kerja_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerja')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pendidikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_pribadi_id')->constrained('pegawai_pribadi')->cascadeOnDelete();
            $table->string('jenjang')->nullable();
            $table->string('institusi')->nullable();
            $table->string('jurusan')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('nomor_ijazah')->nullable();
            $table->string('ijazah_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_pribadi_id')->constrained('pegawai_pribadi')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->enum('hubungan', ['suami', 'istri', 'anak', 'orang tua', 'saudara'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('str', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('nomor_str')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('sk_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jenis_sip_id')->nullable()->constrained('jenis_sip')->nullOnDelete();
            $table->string('nomor_sip')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('sk_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('penugasan_klinis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('nomor_surat')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('dokumen_file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_diklat_id')->nullable()->constrained('jenis_diklat')->nullOnDelete();
            $table->foreignId('kategori_diklat_id')->nullable()->constrained('kategori_diklat')->nullOnDelete();
            $table->string('nama_kegiatan')->nullable();
            $table->enum('status_kelayakan', ['layak', 'tidak layak']);
            $table->enum('status_validasi', ['valid', 'tidak valid']);
            $table->string('penyelenggara')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('tempat')->nullable();
            $table->time('waktu')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('list_jadwal_diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diklat_id')->constrained('diklat')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('laporan_file_path')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->enum('status_diklat', ['belum terlaksana', 'sedang terlaksana', 'sudah terlaksana']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('perubahan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('field_name')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });

        Schema::create('log_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activity');
        Schema::dropIfExists('perubahan_data');
        Schema::dropIfExists('notification');
        Schema::dropIfExists('list_jadwal_diklat');
        Schema::dropIfExists('diklat');
        Schema::dropIfExists('penugasan_klinis');
        Schema::dropIfExists('sip');
        Schema::dropIfExists('str');
        Schema::dropIfExists('keluarga');
        Schema::dropIfExists('pendidikan');
        Schema::dropIfExists('unit_kerja_pegawai');
        Schema::dropIfExists('golongan_ruang_pegawai');
        Schema::dropIfExists('jabatan_pegawai');
        Schema::dropIfExists('pangkat_pegawai');
        Schema::dropIfExists('profesi_pegawai');
        Schema::dropIfExists('pegawai_pribadi');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('kategori_diklat');
        Schema::dropIfExists('jenis_diklat');
        Schema::dropIfExists('jenis_sip');
        Schema::dropIfExists('golongan_ruang');
        Schema::dropIfExists('pangkat');
        Schema::dropIfExists('profesi');
        Schema::dropIfExists('jenis_pegawai');
        Schema::dropIfExists('unit_kerja');
    }
};
