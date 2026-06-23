<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_approvals', function (Blueprint $table) {
            $table->id();
            $table->morphs('requestable');
            $table->foreignId('approval_chain_step_id')->constrained('approval_chain_steps')->cascadeOnDelete();
            $table->integer('step_order');
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_approvals');
    }
};
