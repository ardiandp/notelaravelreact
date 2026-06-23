<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lon', 10, 7)->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lon', 10, 7)->nullable();
            $table->string('check_in_address')->nullable();
            $table->string('check_out_address')->nullable();
            $table->string('check_in_foto')->nullable();
            $table->string('check_out_foto')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('work_from', ['wfo', 'wfa'])->default('wfo');
            $table->integer('terlambat_menit')->default(0);
            $table->enum('status', ['hadir', 'terlambat', 'alpha', 'izin', 'sakit', 'cuti'])->default('hadir');
            $table->string('keterangan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
