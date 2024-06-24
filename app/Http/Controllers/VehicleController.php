<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicles;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\Settings;
use Carbon\Carbon;

class VehicleController extends Controller
{
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = Vehicles::select('vehicles.*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->vehicle_id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '<button type="button" name="delete" id="' . $data->vehicle_id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $vehicles = Vehicles::all();
        return view('vehicles')->with(compact('vehicles'));
    }

    public function store(Request $request)
{
    $request->validate([
        'vh_plate' => 'required',
        'vh_type' => 'required',
        'vh_brand' => 'required',
        'vh_year' => 'required',
        'vh_fuel_type' => 'required',
        'vh_condition' => 'required',
        'vh_status' => 'required',
        'vh_capacity' => 'required',
        'vh_confirmation' => 'required',
    ], [
        'required' => 'This field is required',
    ]);

    $vehicle = new Vehicles();
    $vehicle->fill($request->all());
    $vehicle->save();

    return response()->json(['success' => 'Vehicle successfully registered']);
}

public function update(Request $request)
{
    $request->validate([
        'vh_plate_modal' => 'required',
        'vh_type_modal' => 'required',
        'vh_brand_modal' => 'required',
        'vh_year_modal' => 'required',
        'vh_fuel_type_modal' => 'required',
        'vh_condition_modal' => 'required',
        'vh_status_modal' => 'required',
        'vh_confirm_modal' => 'required',
    ]);

    $id = $request->hidden_id;

    try {
        $vehicle = Vehicles::findOrFail($id);
        $vehicle->vh_plate = $request->vh_plate_modal;
        $vehicle->vh_type = $request->vh_type_modal;
        $vehicle->vh_brand = $request->vh_brand_modal;
        $vehicle->vh_year = $request->vh_year_modal;
        $vehicle->vh_fuel_type = $request->vh_fuel_type_modal;
        $vehicle->vh_condition = $request->vh_condition_modal;
        $vehicle->vh_status = $request->vh_status_modal;
        $vehicle->vh_confirmation = $request->vh_confirm_modal;
        $vehicle->vh_capacity = $request->vh_capacity_modal;
        $vehicle->save();

        return response()->json(['success' => 'Vehicle successfully updated']);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Vehicle not found.'], 404);
    }
}

public function edit($vehicle_id)
{
    if (request()->ajax()) {
        try {
            $data = Vehicles::findOrFail($vehicle_id);
            return response()->json(['result' => $data]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vehicle not found.'], 404);
        }
    }

    try {
        $vehicle = Vehicles::findOrFail($vehicle_id);
        return view('edit_vehicle')->with(compact('vehicle'));
    } catch (ModelNotFoundException $e) {
        return redirect()->back()->withErrors(['error' => 'Vehicle not found.']);
    }
}

    public function delete($vehicle_id)
    {
        try {
            $vehicle = Vehicles::findOrFail($vehicle_id);
            $vehicle->delete();

            return response()->json(['success' => 'Vehicle successfully deleted']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vehicle not found.'], 404);
        }
    }
}
