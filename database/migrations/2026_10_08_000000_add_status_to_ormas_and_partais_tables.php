<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ormas', function (Blueprint $table) {
            $table->string('status')->default('aktif')->index();
        });

        Schema::table('partais', function (Blueprint $table) {
            $table->string('status')->default('aktif')->index();
        });
    }

    public function down(): void
    {
        Schema::table('ormas', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });

        Schema::table('partais', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
