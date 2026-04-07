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

        Schema::create('pangkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pejabat_penetap')->nullable();
            $table->date('tmt_sk')->nullable();
            $table->unsignedBigInteger('sk_file_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nik')->unique();
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->restrictOnDelete();
            $table->date('tmt_mulai')->nullable();
            $table->date('tmt_selesai')->nullable();
            $table->unsignedBigInteger('sk_file_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pegawai_pribadi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->unique()->constrained('pegawai')->cascadeOnDelete();
            $table->enum('pendidikan_terakhir', ['SMA/SMK Sederajat', 'D3', 'S1/D4', 'S2', 'S3'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            $table->enum('status_perkawinan', ['kawin', 'belum kawin', 'cerai hidup', 'cerai mati'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('foto_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pegawai_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jenis_pegawai_id')->nullable()->constrained('jenis_pegawai')->nullOnDelete();
            $table->foreignId('profesi_id')->nullable()->constrained('profesi')->nullOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->enum('status_pegawai', ['aktif', 'tidak aktif'])->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->foreignId('pangkat_id')->nullable()->constrained('pangkat')->nullOnDelete();
            $table->enum('golongan_ruang', ['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'])->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->date('tmt_pangkat_akhir')->nullable();
            $table->date('masa_kerja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('riwayat_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('pegawai_pekerjaan_id')->nullable()->constrained('pegawai_pekerjaan')->nullOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->foreignId('pangkat_id')->nullable()->constrained('pangkat')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->unsignedBigInteger('record_id');
            $table->enum('file_type', ['foto', 'sertifikat', 'surat', 'dokumen'])->nullable();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
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
            $table->foreignId('ijazah_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('str', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_pekerjaan_id')->constrained('pegawai_pekerjaan')->cascadeOnDelete();
            $table->string('nomor_str')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->foreignId('sk_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sip', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_pekerjaan_id')->constrained('pegawai_pekerjaan')->cascadeOnDelete();
            $table->foreignId('jenis_sip_id')->nullable()->constrained('jenis_sip')->nullOnDelete();
            $table->string('nomor_sip')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->foreignId('sk_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('penugasan_klinis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_pekerjaan_id')->constrained('pegawai_pekerjaan')->cascadeOnDelete();
            $table->string('nomor_surat')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->foreignId('dokumen_file_id')->nullable()->constrained('files')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('nama')->nullable();
            $table->enum('hubungan', ['suami', 'istri', 'anak', 'orang tua', 'saudara'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('diklat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_diklat_id')->nullable()->constrained('jenis_diklat')->nullOnDelete();
            $table->foreignId('kategori_diklat_id')->nullable()->constrained('kategori_diklat')->nullOnDelete();
            $table->string('nama_kegiatan')->nullable();
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
            $table->foreignId('laporan_file_id')->nullable()->constrained('files')->nullOnDelete();
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

        Schema::table('pangkat', function (Blueprint $table) {
            $table->foreign('sk_file_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->foreign('sk_file_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('pegawai_pribadi', function (Blueprint $table) {
            $table->foreign('foto_id')->references('id')->on('files')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_pribadi', function (Blueprint $table) {
            $table->dropForeign(['foto_id']);
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropForeign(['sk_file_id']);
        });

        Schema::table('pangkat', function (Blueprint $table) {
            $table->dropForeign(['sk_file_id']);
        });

        Schema::dropIfExists('log_activity');
        Schema::dropIfExists('perubahan_data');
        Schema::dropIfExists('notification');
        Schema::dropIfExists('list_jadwal_diklat');
        Schema::dropIfExists('diklat');
        Schema::dropIfExists('keluarga');
        Schema::dropIfExists('penugasan_klinis');
        Schema::dropIfExists('sip');
        Schema::dropIfExists('str');
        Schema::dropIfExists('pendidikan');
        Schema::dropIfExists('files');
        Schema::dropIfExists('riwayat_pekerjaan');
        Schema::dropIfExists('pegawai_pekerjaan');
        Schema::dropIfExists('pegawai_pribadi');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('kategori_diklat');
        Schema::dropIfExists('jenis_diklat');
        Schema::dropIfExists('jenis_sip');
        Schema::dropIfExists('pangkat');
        Schema::dropIfExists('profesi');
        Schema::dropIfExists('jenis_pegawai');
        Schema::dropIfExists('unit_kerja');
    }
};
