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

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::post('dine-in', [AuthController::class, 'startSession']);
Route::post('{tableId}/dine-out', [AuthController::class, 'endSession']);
Route::get('{tableId}/orders', [OrdersController::class, 'viewCart']);
Route::get('{tableId}/ordered', [OrdersController::class, 'viewOrdered']);

Route::get('foods', [ProductController::class, 'getFoods']);
Route::get('shakes', [ProductController::class, 'getShakes']);
Route::get('beverages', [ProductController::class, 'getBeverages']);
Route::get('product/{productId}', [ProductController::class, 'getProductById']);


Route::post('{tableId}/add-customer-name', [CustomerController::class, 'addCustomerName']);

//Orders
Route::post('{tableId}/{productId}/add-order', [OrdersController::class, 'addToCart']);

Route::delete('{tableId}/{productId}/delete-order', [OrdersController::class, 'removeFromCart']);
Route::post('{tableId}/checkout', [OrdersController::class, 'checkout']);

//User Side


//admin

Route::post('admin/login', [AuthController::class, 'login']);
Route::get('admin/logout', [AuthController::class, 'logout']);

Route::get('admin/ordered', [OrdersController::class, 'adminViewOrdered']);
Route::get('admin/pending-orders', [OrdersController::class, 'adminViewPendingOrders']);
Route::get('admin/served-orders', [OrdersController::class, 'adminViewServedOrders']);
Route::get('admin/orders-history', [OrdersController::class, 'adminViewCompletedOrders']);

Route::post('admin/serve-order/{tableId}', [OrdersController::class, 'served']);


Route::get('admin/products', [ProductController::class, 'getProducts']);
Route::post('admin/add-product', [ProductController::class, 'addProduct']);
Route::post('admin/{productId}/update-product', [ProductController::class, 'updateProduct']);
Route::delete('admin/{productId}/delete-product', [ProductController::class, 'deleteProduct']);



