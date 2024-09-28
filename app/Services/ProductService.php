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

    public function removeImage($path)
    {
        $flag = Storage::disk('local')->delete(['public/' . $path]);
        return $flag;
    }
    public function addProduct($request)
    {
        try {

            $duplicateProduct = Product::where('name', $request->name)
                ->where('size', $request->size)
                ->where('type', $request->type)
                ->first();

            if ($duplicateProduct) {

                return 'duplicate';
            }
            $foods = Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'size' => $request->size,
                'type' => $request->type,
                'availability' => isset($request->availability) ? $request->availability : 'Not Available'
            ]);

            if ($foods) {

                $existingProduct = Product::where('name', $request->name)
                    ->where('type', $request->type)
                    ->first();


                if (!$existingProduct && $request->hasFile('productImage')) {
                    $path = $this->storeImage($request->file('productImage'), $foods->pkProductId, FileFolderEnum::Products->value);
                    $foods->productImage = $path;
                }


                $foods->save();
            }

            return $foods;

        } catch (Exception $e) {
            throw $e;  // Let the exception be caught by the controller
        }
    }



    public function getProducts()
    {

        return Product::all();
    }

    public function getProductById($productId)
    {
        return Product::find($productId);
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


    public function deleteProduct($productId)
    {
        $product = Product::find($productId);

        if ($product != null) {
            if ($product->productImage != null) {

                $this->removeImage($product->productImage);
            }
            return $product->delete();
        }

        throw new Exception(message: 'Product not found');
    }

    public function updateProduct($productId, $request)
    {
        $product = Product::find($productId);

        if ($product === null) {
            return false;
        }

        if ($request->hasFile('productImage')) {

            if ($product->productImage !== null) {
                $this->removeImage($product->productImage);
            }

            $path = $this->storeImage($request->file('productImage'), $product->pkProductId, FileFolderEnum::Products->value);
            $request->merge(['productImage' => $path]);
        }

        return $product->update($request->all());
    }

}
