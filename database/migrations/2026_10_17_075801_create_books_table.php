<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->string('kode_buku')->unique();
        $table->string('judul');             // <--- Pastikan baris ini ada!
        $table->string('pengarang');         // <--- Pastikan baris ini ada!
        $table->string('penerbit')->nullable();
        $table->year('tahun')->nullable();
        $table->integer('stok')->default(0);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};