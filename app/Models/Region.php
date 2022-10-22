<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'regions';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['governorate_id', 'name'];

    public function users()
    {
        return $this->hasMany(User::class, 'region_id', 'id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'region_id', 'id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id', 'id');
    }
}
