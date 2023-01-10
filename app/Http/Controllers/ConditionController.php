<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condition;

class ConditionController extends Controller
{
    public function index($user_id)
    {
        $conditions = Condition::where('user_id', $user_id)->get();
        return $conditions;
    }

    // public function create()
    // {

    // }

    public function store(Request $request, $user_id)
    {
        $request->validate([
            'condition_date' => 'required',
            'condition_symptoms' => 'required',
            'temperature' => 'required',
            // 'oxygen_lvl' => 'required',
            'condition_summary' => 'required'
        ]);
        
        $condition = Condition::create([
            'condition_date' => $request->get('condition_date'),
            'condition_symptoms' => $request->get('condition_symptoms'),
            'temperature' => $request->get('temperature'),
            'oxygen_lvl' => $request->get('oxygen_lvl'),
            'condition_summary' => $request->get('condition_summary'),
            'user_id' => $user_id,
        ]);

        return $condition;
    }
}
