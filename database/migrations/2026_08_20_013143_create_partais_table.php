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
        Schema::create('partais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('desa_id')
                ->constrained('desas')
                ->cascadeOnDelete();
            $table->string('nama', 150);
            $table->unsignedInteger('jumlah_kader')->default(0);
            $table->string('ketua', 150);
            $table->string('sekretaris', 150);
            $table->string('bendahara', 150);
            $table->decimal('lat', 10, 7);
            $table->decimal('long', 10, 7);
            $table->text('alamat')->nullable();
            $table->timestamps();

            $table->index('desa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partais');
    }
};
