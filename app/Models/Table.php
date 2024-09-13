<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';
    protected $primaryKey = 'pkTableId';


    protected $fillable = [
        'tableNumber',
        'customerName',
        'status'
    ];

    public function order()
    {

        return $this->hasMany(Orders::class, 'fkTableId');
    }

    public function scopeActive($query)
    {

        return $query->where('status', '=', 'Active');
    }

    public function scopeInactive($query)
    {

        return $query->where('status', '=', 'Inactive');
    }
}
