<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    protected $table = 'checks';

    protected $primaryKey = 'id';

    protected $fillable = [
        'account_id',
        'numberId',
        'location'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
