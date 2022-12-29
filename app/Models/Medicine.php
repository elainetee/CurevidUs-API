<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $primaryKey = 'medicine_id';
    protected $table = 'medicines';

    protected $fillable = [
        'medicine_name',
        'medicine_desc',
        'medicine_photo_name',
        'medicine_photo_path',
        'medicine_price'
    ];

    public function order()
    {
        return $this->belongsToMany(Order::class
        , 'order_medicine', 'medicine_id', 'order_id'
        );
    }
}
