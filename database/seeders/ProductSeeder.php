<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect(json_decode(file_get_contents(database_path('data/products.json')), false, 512, JSON_THROW_ON_ERROR))
            ->map(fn($item) => [
                'name' => $item->name,
                'price' => $item->price,
                'type' => $item->type,
                'size' => $item->size,
                'availability' => $item->availability
            ]);

        DB::table('products')->insert($products->all());
    }
}
