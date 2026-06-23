<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 20)->unique()->nullable()->after('id');
            $table->string('phone', 20)->nullable()->after('is_admin');
            $table->boolean('aktif')->default(1)->after('phone');
            $table->string('foto')->nullable()->after('aktif');
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
            $table->dropColumn(['nip', 'phone', 'aktif', 'foto']);
        });
    }
};
