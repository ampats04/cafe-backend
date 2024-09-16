<?php
namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Foods;
use App\Models\Product;
use Exception;

class ProductService
{

    public function addProduct($request)
    {
        try {

            $foods = Product::create([

                'name' => $request->name,
                'price' => $request->price,
                'size' => $request->size,
                'type' => $request->type,
                'availability' => isset($request->availability) ? $request->availability : 'Not Available'
            ]);

            return $foods;

        } catch (Exception $e) {
            return $e;
        }
    }

    public function getProducts()
    {

        return Product::all();
    }

    public function getShakes()
    {


        return Product::shake()
            ->get();
    }

    public function getFood()
    {


        return Product::food()
            ->get();
    }

    public function getBeverage()
    {


        return Product::beverage()
            ->get();
    }

}