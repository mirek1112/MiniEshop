<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
     public function index(){

        $products = Product::with('category', 'ratings')
        ->get()
        ->map(function ($product) {

            $averageRating = $product->calculateAverageRating();
            $userRating = round((int)$product->getUserRating(),1);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'average_rating' => $averageRating,
                'user_rating' => $userRating,
                'price' => $product->price,
                'image' =>$product->image,
                'quantity' =>$product->quantity,
            ];
        });



      return Inertia::render('Products', [
        'products' => $products,
        ]);

    }

    public function dataFetch(): JsonResponse{

        $products = Product::with('category', 'ratings')
        ->get()
        ->map(function ($product) {

            $averageRating = $product->calculateAverageRating();
            $userRating = round((int)$product->getUserRating(),1);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'average_rating' => $averageRating,
                'user_rating' => $userRating,
                'price' => $product->price,
                'image' =>$product->image,
                'quantity' =>$product->quantity,
            ];
        });

        return response()->json(['products' => $products], 200);

    }


    public function rateProduct(Request $request, Product $product)
    {

        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        ProductRating::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => Auth::id()],
            ['rating' => $request->rating]
        );
        $product = Product::with('ratings')->find($product->id);
        $averageRating = $product->calculateAverageRating();
        $userRating = $product->getUserRating();

        return response()->json([
            'success' => true,
            'average_rating' => $averageRating,
            'user_rating' => $userRating,
        ]);
    }


      public function show($id)
      {

        $product = Product::with('category', 'ratings')->find($id);

        if ($product) {
            $averageRating = $product->calculateAverageRating();
            $userRating = round((int)$product->getUserRating(), 1);

            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'average_rating' => $averageRating,
                'user_rating' => $userRating,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $product->quantity,
            ];
        }

          if (!$product) {
              return response()->json(['error' => 'PRODUKT SE NEPODAŘILO NAJÍT'], 404);
          }

          return Inertia::render('Product', [
            'info' =>  $productData,
            ]);
      }
}
