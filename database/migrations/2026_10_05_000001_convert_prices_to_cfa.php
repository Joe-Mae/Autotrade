<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'vehicles' => ['prix_vente'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)->whereNotNull($column)->update([
                    $column => DB::raw('ROUND('.$column.' * 655.957)'),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'vehicles' => ['prix_vente'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)->whereNotNull($column)->update([
                    $column => DB::raw('ROUND('.$column.' / 655.957)'),
                ]);
            }
        }
    }
};
