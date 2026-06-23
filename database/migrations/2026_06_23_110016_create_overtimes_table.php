<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('total_jam');
            $table->string('jenis')->default('bayar');
            $table->decimal('rupiah_per_jam', 15, 2)->nullable();
            $table->decimal('total_bayar', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
