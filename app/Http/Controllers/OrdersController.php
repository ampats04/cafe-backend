<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\TableRequest;
use App\Models\Orders;
use App\Models\Product;
use App\Models\Table;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function addToCart(OrderRequest $orderRequest, $tableId, $productId)
    {

        $quantity = $orderRequest->quantity;

        $table = Table::find($tableId);
        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $cartItem = Orders::where('fkTableId', '=', $tableId)
            ->where('fkProductId', '=', $productId)
            ->active()
            ->whereHas('product', function ($query) {
                $query->available();
            })
            ->first();


        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
            return response()->json(['success' => true, 'message' => 'Quantity updated', 'data' => $cartItem]);
        }

        $orders = Orders::create([
            'fkTableId' => $tableId,
            'fkProductId' => $productId,
            'quantity' => $quantity,
            'status' => 'Active',
        ]);

        return response()->json(['success' => true, 'message' => 'Item added to cart', 'data' => $orders]);
    }

    public function viewCart($tableId)
    {

        $cartItems = Orders::where('fkTableId', '=', $tableId)
            ->active()
            ->with(['table', 'product'])
            ->get();


        if ($cartItems->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Cart is empty', 'data' => []], 200);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'total' => $total,
        ], 200);
    }

    public function removeFromCart($tableId, $productId)
    {
        $cartItem = Orders::where('fkTableId', '=', $tableId)
            ->where('fkProductId', '=', $productId)
            ->active()
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json(['success' => true, 'message' => 'Item removed from cart'], 200);
    }

    public function adminViewCart()
    {
        $cartItems = Orders::active()
            ->with(['table', 'product'])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Retrieved all Orders',
            'data' => $cartItems,
        ], 200);
    }

    public function adminViewPendingOrders()
    {
        $orders = Orders::pending()
            ->with(['product', 'table'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Retrieved Pending orders',
                'data' => $orders,
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'No orders found',
        ], 200);
    }

    public function adminViewCompletedOrders()
    {

        $orders = Orders::completed()
            ->with(['product', 'table'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Retrieved Order History',
                'data' => $orders,
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'No orders found',
        ], 200);
    }

   public function served($tableId)
{
    $order = Orders::where('fkTableId', '=', $tableId)
        ->pending()
        ->first();

    if ($order) {
        $order->status = 'served';
        $order->save();
        return response()->json(['success' => true, 'message' => 'Order status updated to served', 'data' => $order], 200);
    }

    return response()->json(['message' => 'No pending order found'], 404);
}

public function adminViewServedOrders()
{
    $orders = Orders::where('status', 'served') 
        ->with(['product', 'table'])
        ->orderBy('created_at', 'desc')
        ->get();

    if ($orders->isNotEmpty()) {
        return response()->json([
            'success' => true,
            'message' => 'Retrieved Order History',
            'data' => $orders,
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'No orders found',
    ], 200);
}
public function checkout($tableId)
{

    $cartItems = Orders::where('fkTableId', $tableId)
        ->active()
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['success' => true, 'message' => 'Cart is empty', 'data' => []], 400);
    }

    foreach ($cartItems as $cartItem) {
        $cartItem->update(['status' => 'Pending']);
    }

    return response()->json(['success' => true, 'message' => 'Order successful', 'data' => $cartItems], 200);
}


    public function viewOrdered($tableId)
    {

        // $tableId = session('tableId');
        $cartItems = Orders::where('fkTableId', '=', $tableId)
            ->pending()
            ->with('product', 'table')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Cart is empty', 'data' => []], 200);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved orders',
            'data' => $cartItems,
            'total' => $total,
        ], 200);

    }

    public function adminViewOrdered()
    {
        $cartItems = Orders::pending()
            ->with(['table', 'product'])
            ->get();

        if ($cartItems->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Retrieved all Pending Orders',
                'data' => $cartItems,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'data' => [],
            'message' => 'No pending orders found',
        ], 200);
    }

    public function payment()
    {
        $tableId = session('tableId');

        $table = Table::find($tableId);
        $table->update(['status' => 'Inactive']);

    }
}
