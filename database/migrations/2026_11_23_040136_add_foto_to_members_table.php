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
    Schema::table('members', function (Blueprint $table) { 
        // Kolom baru untuk menyimpan ALAMAT/PATH berkas foto (bukan berkas fotonya) 
        $table->string('foto')->nullable()->after('no_hp'); 
    }); 
} 
 
public function down(): void 
{ 
    Schema::table('members', function (Blueprint $table) { 
        // Kebalikan dari up(): menghapus kolom foto jika migration di-rollback 
        $table->dropColumn('foto'); 
    }); 
}
};
