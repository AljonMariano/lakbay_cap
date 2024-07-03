<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Drivers;
use App\Models\Offices;
use App\Models\Events;
use App\Models\Vehicles;
use App\Models\Reservations;
use App\Models\ReservationVehicle;
use App\Models\Requestors;
use Yajra\DataTables\DataTables; 
use PhpOffice\PhpWord\TemplateProcessor; 
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Reader\Word2007;
use Carbon\Carbon;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;


class RequestorsController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(request()->ajax()) {
                        return datatables()->of(Requestors::select('*'))
                        ->addColumn('action', 'requestor.requestor-action')
                        ->rawColumns(['action'])
                        ->addIndexColumn()
                        ->make(true);
                    }

    $offices = Offices::select('offices.*')->get();
    if (auth()->user()->isAdmin()) {
        return view('admin.requestor.requestors')->with(compact('offices'));
    } else {
        return view('users.requestor.requestors')->with(compact('offices'));
    }
            
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('requestor.requestors');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestor_id = $request->id;
        $requestor  = Requestors::updateOrCreate(
            [
                'requestor_id' => $requestor_id
            ],
            [
                'rq_full_name' => $request->rq_full_name,
                'rq_office' => $request->rq_office,
            ]);

            $validate = $request->validate([
                'rq_full_name'=>'required',
                'rq_office'=>'required',
            ]);
       return Response()->json($requestor);
    }

    
    public function show(Request $request)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        $where = array('requestor_id' => $request->requestor_id);
        $requestor = Requestors::where($where)->first();

        return Response()->json($requestor);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requestors $drivers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $requestor = Requestors::where('requestor_id',$request->requestor_id)->delete();

        return Response()->json($requestor);
    }
    
}
