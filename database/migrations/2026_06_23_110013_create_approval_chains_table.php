<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_chains', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('approval_chain_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_chain_id')->constrained('approval_chains')->cascadeOnDelete();
            $table->integer('step_order');
            $table->string('approver_type');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->unique(['approval_chain_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_chain_steps');
        Schema::dropIfExists('approval_chains');
    }
};
