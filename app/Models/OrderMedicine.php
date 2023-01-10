<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

class OrderMedicine extends Pivot
{
    use HasFactory;

    protected $table = 'order_medicine';
}
