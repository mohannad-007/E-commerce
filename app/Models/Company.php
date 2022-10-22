<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'region_id',
        'level',
        'branch',
        'owner'
    ];

    public function phones()
    {
        return $this->hasMany(Phone::class, 'company_id','id');
    }

    public function adds()
    {
        return $this->hasMany(Add::class, 'company_id','id');
    }

    public function image()
    {
        return $this->hasOne(Image::class, 'company_id','id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id','id');
    }

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }
}
