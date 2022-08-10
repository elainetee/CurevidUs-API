<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function index()
    {
        $medicine = Medicine::all();
        return $medicine;
    }

    public function index1($medicine_id)
    {
        $medicine = Medicine::find($medicine_id);
        return $medicine;
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required',
            'medicine_desc' => 'required',
            'medicine_price' => 'required'
        ]);
        
        $medicine = Medicine::create([
            'medicine_name' => $request->get('medicine_name'),
            'medicine_desc' => $request->get('medicine_desc'),
            'medicine_photo' => $request->get('medicine_photo'),
            'medicine_price' => $request->get('medicine_price')
        ]);

        return $medicine;
    }

    public function update(Request $request, $medicine_id)
    {
        $medicine = Medicine::find($medicine_id);
        $medicine->update($request->all());
        return $medicine;

    }

    public function delete($medicine_id)
    {
        $medicine = Medicine::find($medicine_id);

        if (isset($medicine)) {
            $medicine->delete();

            return response()->json(['success' => true, 'message' => 'Medicine deleted successfully']);
        } else

            return response()->json('Cannot find selected medicine', 400);
    }
}
