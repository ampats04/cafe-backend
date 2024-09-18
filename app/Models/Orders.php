<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'pkOrderId';


    protected $fillable = [
        'fkTableId',
        'fkProductId',
        'quantity',
        'status',
    ];

    public function table()
    {

        return $this->belongsTo(Table::class, 'fkTableId', 'pkTableId');
    }

    public function product()
    {

        return $this->belongsTo(Product::class, 'fkProductId', 'pkProductId');
    }


    public function scopeActive($query)
    {

        return $query->where('status', '=', 'Active');
    }

    public function scopePending($query)
    {

        return $query->where('status', '=', 'Pending');
    }

    public function scopeServed($query)
    {

        return $query->whre('status', '=', 'Served');
    }

    public function scopeCompleted($query)
    {

        return $query->where('status', '=', 'Completed');
    }
}
