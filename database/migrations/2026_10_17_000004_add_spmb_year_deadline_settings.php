<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Menambah pengaturan SPMB yang dipakai landing: tahun ajaran & tanggal penutupan. Idempoten. */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('settings')) {
            return;
        }
        $rows = [
            ['key' => 'spmb_year', 'label' => 'Tahun ajaran SPMB (contoh: 2026/2027)'],
            ['key' => 'spmb_deadline', 'label' => 'Tanggal penutupan SPMB (contoh: 30 Juni 2026)'],
        ];
        foreach ($rows as $row) {
            if (DB::table('settings')->where('key', $row['key'])->exists()) {
                continue;
            }
            DB::table('settings')->insert($row + [
                'value' => '', 'group' => 'spmb', 'type' => 'text', 'description' => null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        cache()->forget('site_settings');
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['spmb_year', 'spmb_deadline'])->delete();
    }
};
