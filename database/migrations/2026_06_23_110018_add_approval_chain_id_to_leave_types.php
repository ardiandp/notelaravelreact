<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->foreignId('approval_chain_id')->nullable()->constrained('approval_chains')->nullOnDelete()->after('max_days');
        });
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropForeign(['approval_chain_id']);
            $table->dropColumn('approval_chain_id');
        });
    }
};
