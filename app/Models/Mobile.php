<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobile extends Model
{
    use HasFactory;

    protected $table = 'mobiles';

    protected $primaryKey = 'id';

    public $timestamps = true;


    protected $fillable = [
        'ram',
        'memory',
        'front_camera',
        'back_camera',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
