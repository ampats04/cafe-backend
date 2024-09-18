<?php
namespace App\Services;

use App\Enums\FileFolderEnum;
use App\Http\Requests\ProductRequest;
use App\Models\Foods;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Storage;
use Str;

class ProductService
{

    public function storeImage($img, $product_name, $folder_name)
    {
        try {
            //code...
            $currentDateTime = Carbon::now()->format('Ymd_His');
            $newFilename = 'document_' . $currentDateTime . Str::random(10) . '.' . $img->getClientOriginalExtension();
            $path = 'images/' . $folder_name . '/' . $product_name . '/' . $newFilename;
            // $filePath = $path;
            Storage::disk('local')->put('public/' . $path, file_get_contents($img), 'public');
            return $path;
        } catch (Exception $th) {
            throw $th;
        }
    }
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

            if ($foods) {

                if ($request->hasFile('productImage')) {
                    $path = $this->storeImage($request->file('productImage'), $foods->pkProductId, FileFolderEnum::Products->value);
                    $foods->productImage = $path;
                }
                $foods->save();
            }

            return $foods;

        } catch (Exception $e) {
            throw $e;
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