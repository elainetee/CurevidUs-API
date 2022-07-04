<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $primaryKey = 'condition_id';

    protected $fillable = [
        'user_id',
        'condition_date',
        'condition_symptoms',
        'temperature',
        'oxygen_lvl',
        'condition_summary'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
