<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'pkProductId';


    protected $fillable = [
        'name',
        'price',
        'type',
        'size',
        'availability',
    ];

    public function product()
    {

        return $this->hasMany(Orders::class, 'fkProductId');
    }
    public function scopeAvailable($query)
    {

        return $query->where('availability', '=', 'Available');
    }
}
