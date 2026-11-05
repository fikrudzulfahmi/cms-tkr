<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profil penulis: jabatan singkat + bio yang tampil di bawah artikel.
 * Nilai role "author" (penulis) tidak butuh perubahan skema karena kolom role bertipe string.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position', 'bio']);
        });
    }
};
