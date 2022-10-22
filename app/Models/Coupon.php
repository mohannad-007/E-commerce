<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'percent',
        'account_id'
        ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id','id');
    }
}
