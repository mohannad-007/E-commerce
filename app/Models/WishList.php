<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $table = 'wish_lists';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id'];

    public function products()
    {
        return $this->belongsToMany
        (Product::class, 'wish_list_product', 'wish_list_id', 'product_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
