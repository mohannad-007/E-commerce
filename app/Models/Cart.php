<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id'];

    public function products()
    {
        return $this->belongsToMany
        (Product::class, 'cart_product', 'cart_id', 'product_id')
            ->withTimestamps();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
