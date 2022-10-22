<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Branch extends Model
{
    use  HasFactory, Notifiable;

    protected $table = 'branches';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['nameOfBranch'];

    public function branchShippings()
    {
        return $this->hasMany(BranchShipping::class, 'branch_id','id');
    }
}
