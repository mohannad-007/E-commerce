<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchShipping extends Model
{
    use HasFactory;

    protected $table = 'branch_shippings';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = ['branch_id','shipping_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id','id');
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class, 'shipping_id','id');
    }
}
