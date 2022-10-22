<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'id';

    public $timestamps = false;


    protected $fillable = [
        'name_of_the_company',
        'type_of_the_company',
        'color',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function electricals()
    {
        return $this->hasMany(Electrical::class, 'category_id', 'id');
    }

    public function mobiles()
    {
        return $this->hasMany(Mobile::class, 'category_id', 'id');
    }

    public function computers()
    {
        return $this->hasMany(Computer::class, 'category_id', 'id');
    }

    public function cloths()
    {
        return $this->hasMany(Cloth::class, 'category_id', 'id');
    }
}
