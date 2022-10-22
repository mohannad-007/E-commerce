<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'category_id',
        'account_id',
        'name_of_product',
        'quantity',
        'price',
        'likes',
        'rate',
        'date_of_production',
        'discount',
        'number_of_sales',
        'booked',
        'viewer',
        'new_or_old',
        'booked_at',
        'buy_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(View::class, 'product_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'product_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'product_id', 'id');
    }

    public function carts()
    {
        return $this->belongsToMany
        (Cart::class, 'cart_product', 'product_id', 'cart_id')
            ->withTimestamps();
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'product_id', 'id');
    }

    public function wishLists()
    {
        return $this->belongsToMany(WishList::class, 'wish_list_product', 'product_id', 'wish_list_id')
            ->withPivot('product_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'product_id', 'id');
    }
    public $withCount = ['comments', 'likes'];

    public function shipping()
    {
        return $this->belongsToMany
        (Shipping::class, 'shipping_products', 'product_id', 'shipping_id')
            ->withTimestamps();
    }

}
