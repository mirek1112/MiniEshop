<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class CartController extends Controller
{
    public function show()
    {
        $cart = Cart::where('user_id', Auth::id())->first();


        $items = CartItem::where('cart_id', $cart->id)->with("product.category")->get();


        return Inertia::render('Cart', [
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'name' => $item->product->name,
                        'product_id' => $item->product_id,
                        'category'=> $item->product->category->name,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,

                ];
            }),
        ]);
    }

    public function add(Request $request, $productId)
    {
        $cart = Cart::withCount('items')->firstOrCreate(['user_id' => Auth::id()]);

        $product = Product::findOrFail($productId);
        if($product->quantity<1)
        return response()->json(['success' => 'noquantity']);


        $existingItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();


        if ($existingItem ) {
            $existingItem->increment('quantity', 1);
            $existingItem->increment('price', $product->price);
            $product->decrement('quantity',1);


        } else {

         $cartItem =  CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' =>  $product->price
            ]);
            if($cartItem){
                $cart->items_count ++;
                $product->decrement('quantity',1);
            }

        }


        return response()->json(['success' => true,
        'product_id' => $productId,
        'quantity'=>$product->quantity,
        'cartitems' =>$cart->items_count

    ]);
    }

    public function update(Request $request, $itemId)
    {

        $item = CartItem::with('product')->findOrFail($itemId);
         $actualQuantity =  $request->quantity;
         $quantityForDecrement = $item->quantity - $actualQuantity;

        $item->update(['quantity' => $request->quantity, 'price' =>  $request->quantity * $item->product->price]);

        if ($quantityForDecrement > 0) {
            $item->product->increment("quantity",$quantityForDecrement);
        }
        else{
            $item->product->decrement('quantity', abs($quantityForDecrement));
        }



        return response()->json(['success' => true ]);
    }

    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $item->delete();

        return response()->json(['success' => true ]);
    }


    public function purchase (Request $request,$id_user)
    {
            $cart = Cart::where('user_id', Auth::id())->first();
            $currency = $request->query('currency');
            $price = $request->query('price');
            $items = CartItem::where('cart_id', $cart->id)->get();

            return Inertia::render('CartSummary', [
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'name' => $item->product->name,
                            'product_id' => $item->product_id,
                        ],
                        'quantity' => $item->quantity,
                        'price' => $item->price,

                    ];
                }),
                'totalPrice'=> $price,
                'currency'=>$currency
            ]);




    }


}
