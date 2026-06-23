<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
            $table->boolean('is_recurring')->default(0);
            $table->timestamps();
            $table->unique('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
