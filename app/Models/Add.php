<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add extends Model
{
    use HasFactory;

    protected $table = 'adds';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['company_id', 'email', 'details', 'company_name'];


    public function video()
    {
        return $this->hasOne(Video::class, 'add_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
