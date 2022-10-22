<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponGift extends Model
{
    use HasFactory;

    protected $table = 'coupon_gifts';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'money',
        'account_id'
        ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id','id');
    }
}
