<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;


    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }



    public function addProduct(ProductRequest $request)
    {
        try {

            $product = $this->productService->addProduct($request);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!',
                'data' => $product
            ], 201); // 201 status code for created

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to add product.',
                'error' => "Something went wrong"
            ], 500); // 500 status code for server error
        }
    }

    public function getProducts()
    {

        try {

            $shakes = $this->productService->getProducts();

            if ($shakes->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'message' => 'No products found.',
                    'data' => $shakes
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully!',
                'data' => $shakes
            ], 200);
        } catch (Exception $e) {
            // Return server error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500); // 500 

        }
    }

    public function getFoods()
    {

        try {

            $food = $this->productService->getFood();

            if ($food->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'message' => 'No products found.',
                    'data' => $food
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully!',
                'data' => $food
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',

            ], 500);

        }
    }

    public function getShakes()
    {

        try {

            $food = $this->productService->getShakes();

            if ($food->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'message' => 'No products found.',
                    'data' => $food
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully!',
                'data' => $food
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',

            ], 500);

        }
    }

    public function getBeverages()
    {

        try {

            $food = $this->productService->getBeverage();

            if ($food->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'message' => 'No products found.',
                    'data' => $food
                ], 200);
            }
            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully!',
                'data' => $food
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',

            ], 500);

        }
    }
}
