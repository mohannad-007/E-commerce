<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'the_mission',
        'year_of_employment',
        'gender',
        'salary',
        'birthdate',
    ];

    public function account()
    {
        return $this->morphOne(Account::class, 'accountable');
    }
}
