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
            'medicine_price' => 'required',
            'file' => 'mimes:jpg,jpeg,png|max:2048'
        ]);
        $med = new Medicine;

        if ($request->file()) {
            $file_name = time() . '_' . $request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

            $med->medicine_photo_name = time() . '_' . $request->file->getClientOriginalName();
            $med->medicine_photo_path = '/storage/' . $file_path;
            $med->medicine_name = $request->get('medicine_name');
            $med->medicine_desc = $request->get('medicine_desc');
            $med->medicine_price = $request->get('medicine_price');
            $med->save();

            return response()->json(['success' => 'Medicine created successfully.']);
        } else {

            $medicine = Medicine::create([
                'medicine_name' => $request->get('medicine_name'),
                'medicine_desc' => $request->get('medicine_desc'),
                // 'medicine_photo' => $request->get('medicine_photo'),
                'medicine_price' => $request->get('medicine_price')
            ]);

            return $medicine;
        }
    }

    public function update(Request $request, $medicine_id)
    {
        // $request->validate([
        //     'medicine_name' => 'required',
        //     'medicine_desc' => 'required',
        //     'medicine_price' => 'required',
        //     'file' => 'mimes:jpg,jpeg,png|max:2048'
        // ]);

        $medicine = Medicine::find($medicine_id);

        if ($request->file()) {
            $file_name = time() . '_' . $request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

            // $medicine->medicine_photo_name = time() . '_' . $request->file->getClientOriginalName();
            // $medicine->medicine_photo_path = '/storage/' . $file_path;
            // $medicine->medicine_name = $request->get('medicine_name');
            // $medicine->medicine_desc = $request->get('medicine_desc');
            // $medicine->medicine_price = $request->get('medicine_price');
            // $medicine->save();
            $med['medicine_photo_name'] = time() . '_' . $request->file->getClientOriginalName();
            $med['medicine_photo_path'] = '/storage/' . $file_path;
            $med['medicine_name'] = $request->get('medicine_name');
            $med['medicine_desc'] = $request->get('medicine_desc');
            $med['medicine_price'] = $request->get('medicine_price');

            $medicine->update($med);

            return response()->json(['success' => 'Medicine updated successfully.']);
        } else {
            $medicine->update([
                'medicine_name' => $request->get('medicine_name'),
                'medicine_desc' => $request->get('medicine_desc'),
                // 'medicine_photo' => $request->get('medicine_photo'),
                'medicine_price' => $request->get('medicine_price')
            ]);
            return $medicine;
        }
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
