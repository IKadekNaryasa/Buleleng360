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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('desa_id')
                ->constrained('desas')
                ->cascadeOnDelete();
            $table->unsignedInteger('total_jiwa');
            $table->year('tahun');
            $table->timestamps();

            $table->unique(['desa_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
