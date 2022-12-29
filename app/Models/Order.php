<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_date',
        'order_price',
        'order_status',
        // 'medicine_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class
        , 'order_medicine', 'order_id', 'medicine_id'
    );
    }
}
