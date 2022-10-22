<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $table = 'governorates';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['name'];

    public function regions()
    {
        return $this->hasMany(Region::class, 'governorate_id', 'id');
    }
}
