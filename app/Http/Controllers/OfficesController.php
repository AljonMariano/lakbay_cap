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
class OfficesController extends Controller
{
    public function show(Request $request){
        if ($request->ajax()) {
            $data = Offices::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $button= '<button type="button" name="edit" id="' . $data->off_id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button.= '<button type="button" name="delete" id="' . $data->off_id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('offices');

    }
    // public function fetch(){
    //     $data = Offices::onlyTrashed()->get();
    //     foreach($data as $data){
    //         echo $data->off_name;
    //     }
    // }
    public function store(Request $request)
    { 
        $validator = $request->validate([
            'off_acr'=>'required',
            'off_name'=>'required|unique:offices',
            'off_head'=>'required',
        ],
        [
         'off_acr.required'=>'Office Accronym is required',   
         'off_name.required'=>'Office Name is required', 
         'off_head.required'=>"Office's Head is required" 
        ]);



        $offices  = new Offices();

        $acr = $request->off_acr;
        $name = $request->off_name;
        $head = $request->off_head;

        $offices->off_acr =$acr;
        $offices->off_name = $name;
        $offices->off_head = $head;


        $offices->save();
        return response()->json(['success' => 'Office successfully stored']);
        
    }
    public function delete($off_id){
        $data = Offices::findOrFail($off_id);
        $data->delete();
    }
  
    public function edit($off_id)
    {
        if (request()->ajax()) {
            $data = Offices::findOrFail($off_id);
            return response()->json(['result' => $data]);
        }
    }
    public function restore($id) 
    {
        Offices::where('off_id', $id)->withTrashed()->restore();

        return redirect()->route('offices.show', ['status' => 'archived'])
            ->withSuccess(__('Record restored successfully.'));
    }
    public function update(Request $request)
    { 
        $acr = $request->off_acr;
        $name = $request->off_name;
        $head = $request->off_head;
        $id = $request->hidden_id;
        Offices::where('off_id', $id)
        ->update([
            'off_acr' => $acr,
            'off_name'=>$name,
            'off_head'=>$head,
        ]); 
        return response()->json(['success' => 'Data is successfully updated']);
        
    }
    public function offices_word(Request $request){

        $offices = DB::table('offices')
            ->select('offices.*');
        
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $offices->where(function ($query) use ($searchValue) {
                $query->where('off_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_acr', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_head', 'like', '%' . $searchValue . '%');
            });
        }
        
        $filteredOffices = $offices->get();
        $rows=$filteredOffices->count();


        $templateProcessor = new TemplateProcessor(public_path().'\\'."Offices.docx");
        $templateProcessor->cloneRow('off_id', $rows);
        // dd($rows);

        for($i=0;$i<$rows;$i++){
            $offices=$filteredOffices[$i];
            $templateProcessor->setValue("off_id#".($i+1), $offices->off_id);
            $templateProcessor->setValue("off_acr#".($i+1), $offices->off_acr);
            $templateProcessor->setValue("off_name#".($i+1), $offices->off_name);
            $templateProcessor->setValue("off_head#".($i+1), $offices->off_head);
        }
        
        $templateProcessor->saveAs(public_path().'\\'."WordDownloads\\offices_list.docx");
        return response()->download(public_path().'\\'."WordDownloads\\offices_list.docx", "VehiclesList.docx")->deleteFileAfterSend(true);
    }
    public function offices_excel(Request $request)
    {
        $templateFilePath = 'Offices.xlsx';
        $spreadsheet = new Spreadsheet();
        
        // Retrieve filtered reservations based on the search value
        $filteredOfficesQuery = DB::table('offices')
            ->select('offices.*');
    

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $filteredOfficesQuery->where(function ($query) use ($searchValue) {
                $query->where('off_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_acr', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_head', 'like', '%' . $searchValue . '%');
            });
        }
    
        // Execute the query to get filtered reservations
        $filterOffices = $filteredOfficesQuery->get();
        // dd($filtereOffices);
        $spreadsheet = IOFactory::load($templateFilePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        // Populate spreadsheet with filtered reservation data
        foreach ($filterOffices as $index => $offices) {
            $rowIndex = $index + 3; 
            $formattedDate = Carbon::parse($offices->created_at)->format('F j, Y');
            $sheet->setCellValue('A' . $rowIndex, $offices->off_id);
            $sheet->setCellValue('B' . $rowIndex, $offices->off_acr);
            $sheet->setCellValue('C' . $rowIndex, $offices->off_name);
            $sheet->setCellValue('D' . $rowIndex, $offices->off_head);
        }
    
        // Save and download the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'OfficesList.xlsx';
        $writer->save($fileName);
    
        return response()->download($fileName);
    }
    public function offices_pdf(Request $request){

        $offices = DB::table('offices')
            ->select('offices.*');
        
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $offices->where(function ($query) use ($searchValue) {
                $query->where('off_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_acr', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('off_head', 'like', '%' . $searchValue . '%');
            });
        }
        
        $filteredOffices = $offices->get();
        $rows=$filteredOffices->count();


        $templateProcessor = new TemplateProcessor(public_path().'\\'."Offices.docx");
        $templateProcessor->cloneRow('off_id', $rows);
        // dd($rows);

        for($i=0;$i<$rows;$i++){
            $offices=$filteredOffices[$i];
            $templateProcessor->setValue("off_id#".($i+1), $offices->off_id);
            $templateProcessor->setValue("off_acr#".($i+1), $offices->off_acr);
            $templateProcessor->setValue("off_name#".($i+1), $offices->off_name);
            $templateProcessor->setValue("off_head#".($i+1), $offices->off_head);
        }
        
        $wordFilePath = public_path().'\\'."WordDownloads\\offices_list.docx";
        $pdfFilePath = public_path().'\\'."PdfDownloads\\offices_list.pdf";
    
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
        return response()->download($pdfFilePath, "OfficesList.pdf")->deleteFileAfterSend(true);
    }
    

}
