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
    protected OrderService $orderService;


    public function __construct(OrderService $orderService)
    {

        $this->orderService = $orderService;
    }

    public function addToCart(OrderRequest $orderRequest)
    {

        $tableId = session('tableId');
        if (!$tableId) {
            return response()->json(['success' => false, 'message' => 'No table ID found in session'], 400);
        }


        $productId = $orderRequest->fkProductId;
        $quantity = $orderRequest->quantity;


        $table = Table::find($tableId);
        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Check for existing cart item for the current table and customer
        // $cartItem = Orders::whereHas('table', function ($query) use ($table) {
        //     $query->where('tableNumber', $table->tableNumber)
        //         ->where('customerName', $table->customerName)
        //         ->active();
        // })->where('fkProductId', $productId)
        //     ->active()
        //     ->first();

        $cartItem = Orders::where('fkTableId', '=', $tableId)
            ->where('fkProductId', '=', $productId)
            ->whereHas('product', function ($query) {
                $query->available();
            })
            ->first();


        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
            return response()->json(['success' => true, 'message' => 'Quantity updated']);
        }

        Orders::create([
            'fkTableId' => $tableId,
            'fkProductId' => $productId,
            'quantity' => $quantity,
            'status' => 'Active',
        ]);

        return response()->json(['success' => true, 'message' => 'Item added to cart']);
    }

    public function viewCart()
    {
        $tableId = session('tableId');

        $cartItems = Orders::where('fkTableId', '=', $tableId)
            ->active()
            ->with(['table', 'product'])
            ->get();


        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 200);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'total' => $total,
        ], 200);
    }

    public function removeFromCart(OrderRequest $request)
    {
        $tableId = session('tableId');

        $cartItem = Orders::where('fkProductId', '=', $tableId)
            ->where('fkProductId', $request->fkProductId)
            ->active()
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'error' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json(['success' => true, 'message' => 'Item removed from cart'], 200);
    }

    public function adminViewCart()
    {
        $cartItems = Orders::active()
            ->with(['table', 'product'])
            ->get();

        if ($cartItems->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Retrieved all Orders',
                'data' => $cartItems,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No orders found',
        ], 200);
    }


    public function checkout(Request $request)
    {
        if (!session()->has('tableid')) {
            return response()->json(['success' => false, 'message' => 'Please sign in first'], 403);
        }

        $tableId = session('tableId');

        $cartItems = Orders::where('fkTableId', $tableId)
            ->active()
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Cart is empty'], 400);
        }

        foreach ($cartItems as $cartItem) {
            $cartItem->update(['status' => 'Pending']);
        }

        return response()->json(['success' => true, 'message' => 'Order successful', 'order' => $cartItems], 200);
    }

    public function viewOrdered()
    {

        $tableId = session('tableId');

        $cartItems = Orders::where('fkTableId', '=', $tableId)
            ->pending()
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 200);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'ordered_items' => $cartItems,
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
