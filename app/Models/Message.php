<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id', 'subject', 'content'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
