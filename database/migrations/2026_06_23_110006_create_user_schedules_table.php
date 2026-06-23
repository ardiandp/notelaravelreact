<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal');
            $table->boolean('is_override')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_schedules');
    }
};
