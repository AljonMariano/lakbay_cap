<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicles;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
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

        // Fetch all vehicles if not AJAX request
        $vehicles = DB::table('vehicles')->select('vehicles.*')->get();
        return view('vehicles')->with(compact('vehicles'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validate = $request->validate([
            'vh_plate' => 'required',
            'vh_type' => 'required',
            'vh_brand' => 'required',
            'vh_year' => 'required',
            'vh_fuel_type' => 'required',
            'vh_condition' => 'required',
            'vh_status' => 'required',
            'vh_capacity' => 'required',
        ], [
            'required' => 'This field is required',
        ]);

        // Create a new vehicle instance and save it
        $vehicle = new Vehicles();
        $vehicle->fill($request->all());
        $vehicle->save();

        // Return a JSON response indicating success
        return response()->json(['success' => 'Vehicle successfully registered']);
    }

    public function update(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'vh_plate_modal' => 'required',
            'vh_type_modal' => 'required',
            'vh_brand_modal' => 'required',
            'vh_year_modal' => 'required',
            'vh_fuel_type_modal' => 'required',
            'vh_condition_modal' => 'required',
            'vh_status_modal' => 'required',
        ]);

        // Retrieve the ID of the record to be updated
        $id = $request->hidden_id;

        // Find the corresponding record in the database
        $vehicle = Vehicles::findOrFail($id);

        // Update the record with the data from the request
        $vehicle->fill($request->all());

        // Save the updated record
        $vehicle->save();

        // Return a response indicating success
        return response()->json(['success' => 'Vehicle successfully updated']);
    }

    public function edit($vehicle_id)
    {
        if (request()->ajax()) {
            $data = Vehicles::select('vehicles.*')
                ->findOrFail($vehicle_id);

            return response()->json(['result' => $data]);
        }

        // Load the view for editing a vehicle
        $vehicle = Vehicles::findOrFail($vehicle_id);
        return view('edit_vehicle')->with(compact('vehicle'));
    }

    public function delete($vehicle_id)
    {
        // Find the vehicle record by ID and delete it
        $vehicle = Vehicles::findOrFail($vehicle_id);
        $vehicle->delete();

        // Return a JSON response indicating success
        return response()->json(['success' => 'Vehicle successfully deleted']);
    }
}
