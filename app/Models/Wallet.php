<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'value'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
