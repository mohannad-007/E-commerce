<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id', 'product_id', 'content'];

    public function replycomments()
    {
        return $this->hasMany(ReplyComment::class, 'comment_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
