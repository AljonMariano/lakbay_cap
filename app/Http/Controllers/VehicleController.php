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

class VehicleController extends Controller
{
    public function show(Request $request){
        if ($request->ajax()) {
            $data = Vehicles::select('vehicles.*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="'.$data->vehicle_id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '<button type="button" name="delete" id="' . $data->vehicle_id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        } 
        $vehicles = DB::table('vehicles')->select('vehicles.*')->get();
        return view('vehicles')->with(compact('vehicles'));
    }

    public function store(Request $request){
        $vehicles = new Vehicles();
        $validate = $request->validate([
            'vh_plate'=>'required',
            'vh_type'=>'required',
            'vh_brand'=>'required',
            'vh_year'=>'required',
            'vh_fuel_type'=>'required',
            'vh_condition'=>'required',
            'vh_status'=>'required'
        ],[
            'required'=>'This field is required'
        ]
    );  
        $vehicles->vh_plate = $request->vh_plate;
        $vehicles->vh_type = $request->vh_type;
        $vehicles->vh_brand = $request->vh_brand;
        $vehicles->vh_year = $request->vh_year;
        $vehicles->vh_fuel_type = $request->vh_fuel_type;
        $vehicles->vh_condition = $request->vh_condition;
        $vehicles->vh_status = $request->vh_status;
        $vehicles->save();
        return response()->json(['success' => 'Vehicle successfully registered']);
    }
    public function update(Request $request)
    { 

        $id = $request->hidden_id;
        $vehicles = Vehicles::findOrFail($id);
        $vehicles->vh_plate = $request->vh_plate_modal;
        $vehicles->vh_type = $request->vh_type_modal;
        $vehicles->vh_brand = $request->vh_brand_modal;
        $vehicles->vh_year = $request->vh_year_modal;
        $vehicles->vh_fuel_type = $request->vh_fuel_type_modal;
        $vehicles->vh_condition = $request->vh_condition_modal;
        $vehicles->vh_status = $request->vh_status_modal;
        $vehicles->save();
        return response()->json(['success' => 'Vehicle successfully registered']);
    }
    public function edit($vehicle_id)   
    {   
        if (request()->ajax()) {
            $data = Vehicles::select('vehicles.*')
                ->findOrFail($vehicle_id);
                
            
            return response()->json(['result' => $data]);
        }
    }
    public function delete($vehicle_id){
        $data = Vehicles::findOrFail($vehicle_id);
        $data->delete();
        return response()->json(['success' => 'Vehicle successfully Deleted']);
    }

    public function vehicles_word(Request $request){

        $vehicles = DB::table('vehicles')
            ->select('vehicles.*');
        
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $vehicles->where(function ($query) use ($searchValue) {
                $query->where('vehicle_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_plate', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_brand', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_year', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_fuel_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_condition', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_status', 'like', '%' . $searchValue . '%');
            });
        }
        
        $filteredVehicles = $vehicles->get();
        $rows=$filteredVehicles->count();


        $templateProcessor = new TemplateProcessor(public_path().'\\'."Vehicles.docx");
        $templateProcessor->cloneRow('vehicle_id', $rows);
        // dd($rows);

        for($i=0;$i<$rows;$i++){
            $vehicle=$filteredVehicles[$i];
            $templateProcessor->setValue("vehicle_id#".($i+1), $vehicle->vehicle_id);
            $templateProcessor->setValue("vh_plate#".($i+1), $vehicle->vh_plate);
            $templateProcessor->setValue("vh_type#".($i+1), $vehicle->vh_type);
            $templateProcessor->setValue("vh_brand#".($i+1), $vehicle->vh_brand);
            $templateProcessor->setValue("vh_year#".($i+1), $vehicle->vh_year);
            $templateProcessor->setValue("vh_fuel_type#".($i+1), $vehicle->vh_fuel_type);
            $templateProcessor->setValue("vh_condition#".($i+1), $vehicle->vh_condition);
            $templateProcessor->setValue("vh_status#".($i+1), $vehicle->vh_status);
        }
        
        $templateProcessor->saveAs(public_path().'\\'."WordDownloads\\vehicles_list.docx");
        return response()->download(public_path().'\\'."WordDownloads\\vehicles_list.docx", "VehiclesList.docx")->deleteFileAfterSend(true);
    }
    

    public function vehicles_excel(Request $request)
    {
        $templateFilePath = 'Vehicles.xlsx';
        $spreadsheet = new Spreadsheet();
        
        // Retrieve filtered reservations based on the search value
        $filteredVehiclesQuery = DB::table('vehicles')
            ->select('vehicles.*');
    

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $filteredVehiclesQuery->where(function ($query) use ($searchValue) {
                $query->where('vehicle_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_plate', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_brand', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_year', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_fuel_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_condition', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_status', 'like', '%' . $searchValue . '%');
            });
        }
    
        // Execute the query to get filtered reservations
        $filteredVehicles = $filteredVehiclesQuery->get();
        // dd($filteredVehicles);
        $spreadsheet = IOFactory::load($templateFilePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        // Populate spreadsheet with filtered reservation data
        foreach ($filteredVehicles as $index => $vehicles) {
            $rowIndex = $index + 2; 
            $formattedDate = Carbon::parse($vehicles->created_at)->format('F j, Y');
            $sheet->setCellValue('A' . $rowIndex, $vehicles->vehicle_id);
            $sheet->setCellValue('B' . $rowIndex, $vehicles->vh_plate);
            $sheet->setCellValue('C' . $rowIndex, $vehicles->vh_type);
            $sheet->setCellValue('D' . $rowIndex, $vehicles->vh_brand);
            $sheet->setCellValue('E' . $rowIndex, $vehicles->vh_year);
            $sheet->setCellValue('F' . $rowIndex, $vehicles->vh_fuel_type);
            $sheet->setCellValue('G' . $rowIndex, $vehicles->vh_condition);
            $sheet->setCellValue('H' . $rowIndex, $vehicles->vh_status);
        }
    
        // Save and download the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'VehiclesList.xlsx';
        $writer->save($fileName);
    
        return response()->download($fileName);
    }
    public function vehicles_pdf(Request $request){

        $vehicles = DB::table('vehicles')
            ->select('vehicles.*');
        
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $vehicles->where(function ($query) use ($searchValue) {
                $query->where('vehicle_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_plate', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_brand', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_year', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_fuel_type', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_condition', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_status', 'like', '%' . $searchValue . '%');
            });
        }
        
        $filteredVehicles = $vehicles->get();
        $rows = $filteredVehicles->count();
    
    
        $templateProcessor = new TemplateProcessor(public_path().'\\'."Vehicles.docx");
        $templateProcessor->cloneRow('vehicle_id', $rows);
    
        for ($i = 0; $i < $rows; $i++) {
            $vehicle = $filteredVehicles[$i];
            $templateProcessor->setValue("vehicle_id#".($i+1), $vehicle->vehicle_id);
            $templateProcessor->setValue("vh_plate#".($i+1), $vehicle->vh_plate);
            $templateProcessor->setValue("vh_type#".($i+1), $vehicle->vh_type);
            $templateProcessor->setValue("vh_brand#".($i+1), $vehicle->vh_brand);
            $templateProcessor->setValue("vh_year#".($i+1), $vehicle->vh_year);
            $templateProcessor->setValue("vh_fuel_type#".($i+1), $vehicle->vh_fuel_type);
            $templateProcessor->setValue("vh_condition#".($i+1), $vehicle->vh_condition);
            $templateProcessor->setValue("vh_status#".($i+1), $vehicle->vh_status);
        }
        
        $wordFilePath = public_path().'\\'."WordDownloads\\vehicles_list.docx";
        $pdfFilePath = public_path().'\\'."PdfDownloads\\vehicles_list.pdf";
    
        $templateProcessor->saveAs($wordFilePath);
    
        // Load Word document
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordFilePath);
    
        // Set up Dompdf renderer
        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
        Settings::setPdfRendererName('DomPDF');
    
        // Save as PDF
        $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfFilePath);
    
        // Delete the Word file
        unlink($wordFilePath);
    
        // Download the PDF file
        return response()->download($pdfFilePath, "VehiclesList.pdf")->deleteFileAfterSend(true);
    }

}
