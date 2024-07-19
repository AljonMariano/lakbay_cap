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

class DriversController extends Controller
{
    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'dr_emp_id' => 'required|unique:drivers',
                'dr_fname' => 'required',
                'dr_mname' => 'required',
                'dr_lname' => 'required',
                'dr_office' => 'required',
                'dr_status' => 'required',

            ],
            [
                'dr_emp_id.unique' => 'Driver with that EMP ID is already registered',
                'dr_emp_id.required' => 'Driver EMP ID is required',
                'dr_fname.required' => "Driver's Name is required",
                'dr_mname.required' => "Driver's Name is required",
                'dr_lname.required' => "Driver's Name is required",
                'dr_office.required' => "Choose the driver's office",
                'dr_status.required' => "Choose the driver's status",
            ]
        );
        $drivers = new Drivers();
        $emp_id = $request->dr_emp_id;
        $drivers->dr_emp_id = $emp_id;
        $drivers->dr_fname = $request->dr_fname;
        $drivers->dr_lname = $request->dr_lname;
        $drivers->dr_mname = $request->dr_mname;
        $drivers->off_id = $request->dr_office;
        $drivers->dr_status = $request->dr_status;
        $drivers->save();

        return response()->json(['success' => 'Driver successfully Stored']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = Drivers::select('drivers.*', 'offices.off_name')
            ->leftJoin('offices', 'drivers.off_id', '=', 'offices.off_id')
            ->get()
            ->map(function ($driver) {
                $driver->dr_full_name = $driver->dr_fname . ' ' . $driver->dr_mname . ' ' . $driver->dr_lname;
                return $driver;

            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->driver_id . '" class="edit btn btn-primary btn-sm">Edit</button>';
                    $button .= '<button type="button" name="delete" id="' . $data->driver_id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $offices = DB::table('offices')->select('off_id', 'off_acr')->get();
        
        
        if (auth()->user()->isAdmin()) {
            return view('admin.drivers')->with(compact('offices'));
        } else {
            return view('users.drivers')->with(compact('offices'));
        }
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit($driver_id)
    {
        if (request()->ajax()) {
            $data = Drivers::select('drivers.*', 'offices.off_acr')
                ->leftJoin('offices', 'drivers.off_id', '=', 'offices.off_id')
                ->findOrFail($driver_id);
            return response()->json(['result' => $data]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->hidden_id;

        $driver = Drivers::findOrFail($id);
        $off_id = $driver->off_id;
        $driver->dr_emp_id = $request->dr_emp_id_modal;
        $driver->dr_fname = $request->dr_fname_modal;
        $driver->dr_mname = $request->dr_mname_modal;
        $driver->dr_lname = $request->dr_lname_modal;
        $driver->dr_status = $request->dr_status_modal;
        $driver->off_id = $request->dr_office_modal;
        $driver->save();


        return response()->json(['success' => 'Driver successfully updated']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function delete($driver_id)
    {
        $data = Drivers::findOrFail($driver_id);
        $data->delete();
    }
    public function driver_word(Request $request)
    {
        $rows = Drivers::count();
        $drivers = DB::table('drivers')->select('driver_id', 'dr_emp_id', 'dr_fname', 'dr_mname', 'dr_lname', 'off_name')->join('offices', 'drivers.off_id', 'offices.off_id')->get();
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $drivers->where(function ($query) use ($searchValue) {
                $query->where('event_id', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_venue', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_date_start', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_time_start', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_date_end', 'like', '%' . $searchValue . '%')
                    ->orWhere('ev_time_end', 'like', '%' . $searchValue . '%');
            });
        }
        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "Drivers.docx");

        $templateProcessor->cloneRow('driver_id', $rows);
        for ($i = 0; $i < $rows; $i++) {
            $driver = $drivers[$i];
            $templateProcessor->setValue("driver_id#" . ($i + 1), $driver->driver_id);
            $templateProcessor->setValue("dr_emp_id#" . ($i + 1), $driver->dr_emp_id);
            $templateProcessor->setValue("dr_name#" . ($i + 1), $driver->dr_fname." ".$driver->dr_lname);
            $templateProcessor->setValue("dr_office#" . ($i + 1), $driver->off_name);
        }
        $templateProcessor->saveAs(public_path() . '\\' . "WordDownloads\sample_downloads.docx");
        return response()->download(public_path() . '\\' . "WordDownloads\sample_downloads.docx", "DriverList.docx")->deleteFileAfterSend(true);
    }
    public function driver_excel(Request $request)
    {
        $templateFilePath = 'Drivers.xlsx';
        $spreadsheet = new Spreadsheet();
        $rows = Drivers::count();
        $drivers = DB::table('drivers')->select('driver_id', 'dr_emp_id', 'dr_name', 'off_name')->join('offices', 'drivers.off_id', 'offices.off_id')->get();
        $spreadsheet = IOFactory::load($templateFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < $rows; $i++) {
            $driver = $drivers[$i];
            $rowIndex = $i + 2;

            $sheet->setCellValue('A' . $rowIndex, $driver->driver_id);
            $sheet->setCellValue('B' . $rowIndex, $driver->dr_emp_id);
            $sheet->setCellValue('C' . $rowIndex, $driver->dr_name);
            $sheet->setCellValue('D' . $rowIndex, $driver->off_name);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Lakbay_Drivers.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
    public function driver_pdf(Request $request)
    {
        $rows = Drivers::count();
        $drivers = DB::table('drivers')->select('driver_id', 'dr_emp_id', 'dr_name', 'off_name')->join('offices', 'drivers.off_id', 'offices.off_id')->get();
        // dd($drivers);
        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "Drivers.docx");

        $templateProcessor->cloneRow('driver_id', $rows);
        for ($i = 0; $i < $rows; $i++) {
            $driver = $drivers[$i];
            $templateProcessor->setValue("driver_id#" . ($i + 1), $driver->driver_id);
            $templateProcessor->setValue("dr_emp_id#" . ($i + 1), $driver->dr_emp_id);
            $templateProcessor->setValue("dr_name#" . ($i + 1), $driver->dr_name);
            $templateProcessor->setValue("dr_office#" . ($i + 1), $driver->off_name);
        }
        $wordFilePath = public_path() . '\\' . "WordDownloads\\drivers_list.docx";
        $pdfFilePath = public_path() . '\\' . "PdfDownloads\\drivers_list.pdf";
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
        return response()->download($pdfFilePath, "DriversList.pdf")->deleteFileAfterSend(true);
    }
    
    public function getDrivers(Request $request)
    {
        $drivers = Driver::all();
        return response()->json(['drivers' => $drivers]);
    }
}