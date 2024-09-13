<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeveragesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShakeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




// Route::post('add-table', [OrdersController::class, 'addTable']);






// Route::middleware(['api'])->group(function () {

//user

Route::post('dine-in', [AuthController::class, 'startSession']);
Route::post('dine-out', [AuthController::class, 'endSession']);

Route::post('add-customer-name', [CustomerController::class, 'addCustomerName']);

//Orders
Route::post('add-order', [OrdersController::class, 'addToCart']);
Route::get('orders', [OrdersController::class, 'viewCart']);
Route::delete('delete-order', [OrdersController::class, 'removeFromCart']);
Route::post('checkout', [OrdersController::class, 'checkout']);
Route::get('ordered', [OrdersController::class, 'viewOrdered']);

//admin

Route::post('admin/login', [AuthController::class, 'login']);
Route::get('admin/logout', [AuthController::class, 'logout']);

Route::get('admin/orders', [OrdersController::class, 'adminViewCart']);
Route::get('admin/ordered', [OrdersController::class, 'adminViewOrdered']);

Route::post('admin/add-product', [ProductController::class, 'addProduct']);
Route::get('admin/products', [ProductController::class, 'getProducts']);

//});



