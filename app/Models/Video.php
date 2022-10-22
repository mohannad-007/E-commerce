<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['add_id', 'name'];

    public function add()
    {
        return $this->belongsTo(Add::class, 'add_id', 'id');
    }
}
