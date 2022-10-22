<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['account_id','content'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
