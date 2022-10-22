<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'product_id',
        'name_of_img'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
