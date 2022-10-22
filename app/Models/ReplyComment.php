<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyComment extends Model
{
    use HasFactory;

    protected $table = 'reply_comments';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id', 'comment_id', 'content'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }
}
