<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $table = 'shippings';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'cost',
        'receive',
        'delivery'
    ];

    public function products()
    {
        return $this->belongsToMany
        (Product::class, 'shipping_products', 'shipping_id', 'product_id')
            ->withTimestamps();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id','id');
    }

    public function branchShipping()
    {
        return $this->hasOne(BranchShipping::class, 'shipping_id','id');
    }
}
