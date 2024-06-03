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

class EventsController extends Controller
{
    public function store(Request $request){
        $events = new Events();
        
        $ev_name = $request->ev_name;
        $ev_venue = $request->ev_venue;
        $ev_date_start = $request->ev_date_start;
        $ev_time_start = $request->ev_time_start;
        $ev_date_end =$request->ev_date_end;
        $ev_time_end =$request->ev_time_end;
        $ev_date_added =$request->ev_date_added;
        
        $request->validate([
            'ev_name'=>'required',
            'ev_venue'=>'required',
            'ev_date_start'=>'required',
            'ev_time_start'=>'required',
            'ev_date_end'=>'required',
            'ev_time_end'=>'required',
        ],[
            'required'=>'This field is required'
        ]);

            $events->ev_name=$ev_name;
            $events->ev_venue=$ev_venue;
            $events->ev_date_start=$ev_date_start;
            $events->ev_time_start=$ev_time_start;
            $events->ev_date_end=$ev_date_end;
            $events->ev_time_end=$ev_time_end;
            $events->ev_date_added=$ev_date_added;

        $events->save();
        return response()->json(['success' => 'Event successfully recorded']);
    }
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = Events::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="'.$data->event_id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '<button type="button" name="delete" id="' . $data->event_id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('events');
}
public function edit($event_id)
{
    if (request()->ajax()) {
        $data = Events::findOrFail($event_id);
        return response()->json(['result' => $data]);
    }
}
public function update(Request $request)
{ 
    $ev_name = $request->ev_name_modal;
    $ev_venue = $request->ev_venue_modal;
    $ev_date_start = $request->ev_date_start_modal;
    $ev_time_start=$request->ev_time_start_modal;
    $ev_date_end=$request->ev_date_end_modal;
    $ev_time_end=$request->ev_time_end_modal;
    $id = $request->hidden_id;
    Events::where('event_id', $id)
    ->update([
        'ev_name' => $ev_name,
        'ev_venue'=>$ev_venue,
        'ev_date_start'=>$ev_date_start,
        'ev_time_start' => $ev_time_start,
        'ev_date_end'=>$ev_date_end,
        'ev_time_end'=>$ev_time_end,
    ]); 
    return response()->json(['success' => 'Event successfully updated']);
    
}
public function events_word(Request $request){

    $events = DB::table('events')
    ->select('events.*');

if ($request->has('search')) {
    $searchValue = $request->input('search');
    $events->where(function ($query) use ($searchValue) {
        $query->where('event_id', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_name', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_venue', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_date_start', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_time_start', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_date_end', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_time_end', 'like', '%' . $searchValue . '%');
    });
}

$filteredEvents = $events->get();
$rows=$filteredEvents->count();


$templateProcessor = new TemplateProcessor(public_path().'\\'."Events.docx");
$templateProcessor->cloneRow('event_id', $rows);
// dd($rows);

for($i=0;$i<$rows;$i++){
    $vehicle=$filteredEvents[$i];
    $templateProcessor->setValue("event_id#".($i+1), $vehicle->event_id);
    $templateProcessor->setValue("ev_name#".($i+1), $vehicle->ev_name);
    $templateProcessor->setValue("ev_venue#".($i+1), $vehicle->ev_venue);
    $templateProcessor->setValue("ev_date_start#".($i+1), $vehicle->ev_date_start);
    $templateProcessor->setValue("ev_time_start#".($i+1), $vehicle->ev_time_start);
    $templateProcessor->setValue("ev_date_end#".($i+1), $vehicle->ev_date_end);
    $templateProcessor->setValue("ev_time_end#".($i+1), $vehicle->ev_time_end);
}

$templateProcessor->saveAs(public_path().'\\'."WordDownloads\\events_list.docx");
return response()->download(public_path().'\\'."WordDownloads\\events_list.docx", "EventsList.docx")->deleteFileAfterSend(true);
}
public function delete($event_id){
    $data = Events::findOrFail($event_id);
    $data->delete();
    return response()->json(['success' => 'Event successfully Deleted']);
}
public function events_excel (Request $request)
{
    $templateFilePath = 'Events.xlsx';
    $spreadsheet = new Spreadsheet();
    
    // Retrieve filtered reservations based on the search value
    $filteredOfficesQuery = DB::table('events')
        ->select('events.*');


    if ($request->has('search')) {
        $searchValue = $request->input('search');
        $filteredOfficesQuery->where(function ($query) use ($searchValue) {
            $query->where('event_id', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_name', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_venue', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_date_start', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_time_start', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_date_end', 'like', '%' . $searchValue . '%')
                ->orWhere('ev_time_end', 'like', '%' . $searchValue . '%');
        });
    }

    // Execute the query to get filtered reservations
    $filterOffices = $filteredOfficesQuery->get();
    // dd($filtereOffices);
    $spreadsheet = IOFactory::load($templateFilePath);
    $sheet = $spreadsheet->getActiveSheet();

    // Populate spreadsheet with filtered reservation data
    foreach ($filterOffices as $index => $events) {
        $rowIndex = $index + 3; 
        // $formattedDate = Carbon::parse($events->created_at)->format('F j, Y');
        $sheet->setCellValue('A' . $rowIndex, $events->event_id);
        $sheet->setCellValue('B' . $rowIndex, $events->ev_name);
        $sheet->setCellValue('C' . $rowIndex, $events->ev_venue);
        $sheet->setCellValue('D' . $rowIndex, $events->ev_date_start);
        $sheet->setCellValue('E' . $rowIndex, $events->ev_time_start);
        $sheet->setCellValue('F' . $rowIndex, $events->ev_date_end);
        $sheet->setCellValue('G' . $rowIndex, $events->ev_time_end);

    }

    // Save and download the spreadsheet
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $fileName = 'EventsList.xlsx';
    $writer->save($fileName);

    return response()->download($fileName);
}
public function events_pdf(Request $request){

    $events = DB::table('events')
    ->select('events.*');

if ($request->has('search')) {
    $searchValue = $request->input('search');
    $events->where(function ($query) use ($searchValue) {
        $query->where('event_id', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_name', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_venue', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_date_start', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_time_start', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_date_end', 'like', '%' . $searchValue . '%')
            ->orWhere('ev_time_end', 'like', '%' . $searchValue . '%');
    });
}

$filteredEvents = $events->get();
$rows=$filteredEvents->count();


$templateProcessor = new TemplateProcessor(public_path().'\\'."Events.docx");
$templateProcessor->cloneRow('event_id', $rows);
// dd($rows);

for($i=0;$i<$rows;$i++){
    $vehicle=$filteredEvents[$i];
    $templateProcessor->setValue("event_id#".($i+1), $vehicle->event_id);
    $templateProcessor->setValue("ev_name#".($i+1), $vehicle->ev_name);
    $templateProcessor->setValue("ev_venue#".($i+1), $vehicle->ev_venue);
    $templateProcessor->setValue("ev_date_start#".($i+1), $vehicle->ev_date_start);
    $templateProcessor->setValue("ev_time_start#".($i+1), $vehicle->ev_time_start);
    $templateProcessor->setValue("ev_date_end#".($i+1), $vehicle->ev_date_end);
    $templateProcessor->setValue("ev_time_end#".($i+1), $vehicle->ev_time_end);
}
$wordFilePath = public_path().'\\'."WordDownloads\\events_list.docx";
$pdfFilePath = public_path().'\\'."PdfDownloads\\events_list.pdf";

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
return response()->download($pdfFilePath, "EventsList.pdf")->deleteFileAfterSend(true);

}
}
