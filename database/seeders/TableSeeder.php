<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = collect(json_decode(file_get_contents(database_path('data/tables.json')), false, 512, JSON_THROW_ON_ERROR))
            ->map(fn($item) => [
                'tableNumber' => $item->tableNumber
            ]);

        DB::table('tables')->insert($tables->all());
    }
}
