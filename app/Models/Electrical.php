<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electrical extends Model
{
    use HasFactory;

    protected $table = 'electricals';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'details',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
