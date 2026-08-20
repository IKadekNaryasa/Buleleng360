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
        Schema::create('sebaran_agamas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agama_id')
                ->constrained('agamas')
                ->cascadeOnDelete()
                ->index('sebaran_agamas_agama_id_foreign');
            $table->foreignUuid('desa_id')
                ->constrained('desas')
                ->cascadeOnDelete()
                ->index('sebaran_agamas_desa_id_foreign');
            $table->unsignedInteger('jumlah_pemeluk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sebaran_agamas');
    }
};
