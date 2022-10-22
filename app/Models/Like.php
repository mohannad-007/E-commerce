<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $primaryKey = 'id';

    public $timestamps = true;


    protected $fillable = [
        'account_id',
        'product_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
