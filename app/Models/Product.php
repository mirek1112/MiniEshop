<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'quantity', 'image', 'category_id'];


    public function getUserRating()
    {
        if (Auth::check()) {
            $userRating = $this->ratings()->where('user_id', Auth::id())->first();
            return $userRating ? $userRating->rating : null;
        }
        return null;
    }
    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }
    public function calculateAverageRating()
    {
        return round($this->ratings()->average('rating') ?: 0.0,2);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function cartItems()
{
    return $this->hasMany(CartItem::class);
}
}
