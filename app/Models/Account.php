<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'accounts';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['FullName', 'email', 'password', 'owner_id', 'owner_type'];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'account_id', 'id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'account_id', 'id');
    }

    public function wishlist()
    {
        return $this->hasOne(WishList::class, 'account_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(View::class, 'account_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'account_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'account_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'account_id', 'id');
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'account_id', 'id');
    }

    public function replycomments()
    {
        return $this->hasMany(ReplyComment::class, 'account_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'account_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'account_id', 'id');
    }

    public function accountable()
    {
        return $this->morphTo(__FUNCTION__, 'owner_type', 'owner_id');
    }
    public function requests()
    {
        return $this->hasMany(Request::class, 'account_id','id');
    }
    public function shippings()
    {
        return $this->hasMany(Shipping::class, 'account_id','id');
    }
    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'account_id','id');
    }
    public function couponGifts()
    {
        return $this->hasMany(CouponGift::class, 'account_id','id');
    }
}
