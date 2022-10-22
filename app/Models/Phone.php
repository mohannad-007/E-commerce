<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $table = 'phones';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'number'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id','id');
    }
}
