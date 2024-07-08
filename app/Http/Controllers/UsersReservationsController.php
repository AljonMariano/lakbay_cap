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
use Illuminate\Support\Facades\Validator;

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




class UsersReservationsController extends Controller
{
    public function show(Request $request)
    {
        try {
            \Log::info('UsersReservationsController@show method called');
            $userId = auth()->id();
            \Log::info("Authenticated user ID: $userId");
    
            if ($request->ajax()) {
                \Log::info('AJAX request received');
    
                $reservations = Reservations::with(['events', 'requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
                    ->select('reservations.*');
    
                \Log::info('Reservations query:', [
                    'sql' => $reservations->toSql(),
                    'bindings' => $reservations->getBindings()
                ]);
    
                $count = $reservations->count();
                \Log::info("Total number of reservations: $count");
    
                return DataTables::of($reservations)
                    ->addColumn('ev_name', function ($reservation) {
                        return $reservation->events ? $reservation->events->ev_name . ' - ' . $reservation->events->ev_venue : 'N/A';
                    })
                    ->addColumn('vehicles', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            $vehicle = $rv->vehicles;
                            return $vehicle
                                ? "{$vehicle->vh_brand} - {$vehicle->vh_type} - {$vehicle->vh_plate} - {$vehicle->vh_capacity}"
                                : 'N/A';
                        })->implode('<br>');
                    })
                    ->addColumn('drivers', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            $driver = $rv->drivers;
                            return $driver
                                ? "{$driver->dr_fname} {$driver->dr_mname} {$driver->dr_lname}"
                                : 'N/A';
                        })->implode('<br>');
                    })
                    ->addColumn('rq_full_name', function ($reservation) {
                        return $reservation->requestors ? $reservation->requestors->rq_full_name : 'N/A';
                    })
                    ->addColumn('office', function ($reservation) {
                        return $reservation->office ? $reservation->office->off_acr . ' - ' . $reservation->office->off_name : 'N/A';
                    })
                    ->editColumn('created_at', function ($reservation) {
                        return $reservation->created_at->format('F d, Y');
                    })
                    ->rawColumns(['vehicles', 'drivers'])
                    ->make(true);
            }
    
            $events = Events::select('event_id', 'ev_name', 'ev_venue')
                ->orderBy('ev_name')
                ->get();
    
            $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
                ->orderBy('dr_fname')
                ->get();
    
            $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
                ->orderBy('vh_brand')
                ->get();
    
            $requestors = Requestors::all();
            $offices = Offices::select('off_id', 'off_acr', 'off_name')->get();
    
            return view('users.reservations')->with(compact('events', 'drivers', 'vehicles', 'requestors', 'offices'));
        } catch (\Exception $e) {
            \Log::error('Error in UsersReservationsController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }


    public function events()
    {
        $eventsInsert = Events::leftJoin('reservations', 'events.event_id', 'reservations.event_id')
            ->whereNull('reservations.reservation_id')
            ->orWhere([
                ['reservations.rs_status', 'Cancelled'],
                ['rs_cancelled', 0]
            ])
            ->select('events.event_id', 'ev_name')
            ->orderBy('ev_name')
            ->get();
        $existingDriverIds = ReservationVehicle::whereNotNull('driver_id')->distinct('driver_id')->pluck('driver_id')->toArray();
        $existingVehicleIds = ReservationVehicle::pluck('vehicle_id')->toArray();
        $driversInsert = DB::table('drivers')
            ->whereNotIn('driver_id', $existingDriverIds)
            ->select('driver_id', 'dr_fname')
            ->get();

        $vehiclesInsert = DB::table('vehicles')
            ->whereNotIn('vehicle_id', $existingVehicleIds)
            ->select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_capacity', 'vh_type')
            ->get();
        $requestorsInsert = DB::table('requestors')->select('requestor_id', 'rq_full_name')->get();

        return response()->json($eventsInsert);
    }

    public function events_edit()
    {
        $eventsInsert = Events::leftJoin('reservations', 'events.event_id', 'reservations.event_id')
            ->whereNull('reservations.reservation_id')
            ->orWhere([
                ['reservations.rs_status', 'Cancelled'],
                ['rs_cancelled', 0]
            ])
            ->select('events.event_id', 'ev_name')
            ->orderBy('ev_name')
            ->get();

        $existingDriverIds = ReservationVehicle::whereNotNull('driver_id')->distinct('driver_id')->pluck('driver_id')->toArray();
        $existingVehicleIds = ReservationVehicle::pluck('vehicle_id')->toArray();

        $driversInsert = DB::table('drivers')
            ->leftJoin('reservation_vehicles', 'drivers.driver_id', 'reservation_vehicles.driver_id')
            ->whereNull('reservation_vehicles.driver_id')
            ->select('drivers.driver_id', 'dr_fname')
            ->get();


        $vehiclesInsert = DB::table('vehicles')
            ->leftJoin('reservation_vehicles', 'vehicles.vehicle_id', 'reservation_vehicles.vehicle_id')
            ->whereNull('reservation_vehicles.vehicle_id')
            ->select('vehicles.vehicle_id', 'vh_capacity', 'vh_brand')
            ->get();


        $array = [
            'events' => $eventsInsert,
            'drivers' => $driversInsert,
            'vehicles' => $vehiclesInsert
        ];


        return response()->json($array);
    }




    public function store(Request $request)
    {
        try {
            \Log::info('UsersReservationsController@store method called');
            \Log::info('Request data:', $request->all());
    
            $validator = Validator::make($request->all(), [
                'event_id' => 'required|exists:events,event_id',
                'driver_id' => 'required|array',
                'driver_id.*' => 'exists:drivers,driver_id',
                'vehicle_id' => 'required|array',
                'vehicle_id.*' => 'exists:vehicles,vehicle_id',
                'requestor_id' => 'required|exists:requestors,requestor_id',
                'off_id' => 'required|exists:offices,off_id',
                'rs_voucher' => 'nullable|string|max:255',
                'rs_passengers' => 'required|integer|min:1',
                'rs_travel_type' => 'required|string|in:Outside Province Transport,Daily Transport',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            DB::beginTransaction();
    
            $reservation = new Reservations;
            $reservation->event_id = $request->event_id;
            $reservation->requestor_id = $request->requestor_id;
            $reservation->off_id = $request->off_id;
            $reservation->rs_voucher = $request->rs_voucher;
            $reservation->rs_passengers = $request->rs_passengers;
            $reservation->rs_travel_type = $request->rs_travel_type;
            $reservation->rs_approval_status = 'Pending';
            $reservation->rs_status = 'Queued';
            $reservation->save();
    
            foreach ($request->vehicle_id as $key => $vehicleId) {
                $reservationVehicle = new ReservationVehicle;
                $reservationVehicle->reservation_id = $reservation->reservation_id;
                $reservationVehicle->vehicle_id = $vehicleId;
                $reservationVehicle->driver_id = $request->driver_id[$key] ?? null;
                $reservationVehicle->save();
            }
    
            DB::commit();
    
            \Log::info('Reservation created successfully', ['reservation_id' => $reservation->reservation_id]);
    
            return response()->json(['success' => 'Reservation created successfully', 'reservation' => $reservation]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in UsersReservationsController@store: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while creating the reservation'], 500);
        }
    }


    
    public function update(Request $request)
    {
        $id = $request->hidden_id;
        $reservations = Reservations::findOrFail($id);
        // dd($reservations);
        $reservations->event_id = $request->event_edit;
        $reservations->requestor_id = $request->requestor_edit;
        $reservations->rs_voucher = $request->voucher_edit;
        $reservations->rs_travel_type = $request->travel_edit;
        
        $cancelled =  Reservations::where([['rs_cancelled', 0], ['event_id', $request->event_edit]])->latest()->first();
        // dd($cancelled);
        if ($cancelled != null) {
            $cancelled_reservation = Reservations::find($cancelled->reservation_id);
            $cancelled_reservation->rs_cancelled = True;
            $cancelled_reservation->save();
            // dd($cancelled_reservation);
        }
        $reservations->save();
        $reservation_id = $reservations->reservation_id;
        $vehicle_id_edit = $request->vehicle_edit;
        $driver_id_edit = $request->driver_edit;

        $driver_id_count = ($driver_id_edit === null) ? 0 : (count($driver_id_edit));

        foreach ($vehicle_id_edit as $index => $vehicle_id) {
            $exist = ReservationVehicle::where([['vehicle_id', $vehicle_id], ['reservation_id', $id]])->exists();

            if ($exist) {
                $reservation_vh_id = ReservationVehicle::where([['vehicle_id', $vehicle_id], ['reservation_id', $id]])->first()->id;
                $reservation_vh = ReservationVehicle::find($reservation_vh_id);

                if ($index < $driver_id_count) {
                    $reservation_vh->driver_id = $driver_id_edit[$index];
                } else {
                    $reservation_vh->driver_id = null;
                }
                $reservation_vh->save();
            } else {
                $driver_id = null;
                if ($index < $driver_id_count) {
                    $driver_id = $driver_id_edit[$index];
                }
                ReservationVehicle::create([
                    "reservation_id" => $id,
                    "vehicle_id" => $vehicle_id,
                    "driver_id" => $driver_id

                ]);
            }
        }

        return response()->json(['success' => 'Reservation successfully updated']);
    }

    public function edit($reservation_id)
    {
        if (request()->ajax()) {
            $data = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")->select('reservations.*', 'events.ev_name', 'requestors.rq_full_name')
                ->join('events', 'reservations.event_id', '=', 'events.event_id')
                ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
                ->findOrFail($reservation_id);
            return response()->json(['result' => $data]);
        }
    }
    public function delete($reservation_id)
    {
        $data = Reservations::findOrFail($reservation_id);
        $data->delete();
        return response()->json(['success' => 'Vehicle successfully Deleted']);
    }
    public function cancel($reservation_id)
    {
        $reservation = Reservations::findOrFail($reservation_id);
        $reservation->rs_status = 'Cancelled';
        $reservation->save();
        return response()->json(['success' => 'Reservation successfully Cancelled']);
    }

    public function reservations_word(Request $request)
    {

        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'drivers.dr_mname', 'drivers.dr_lname', 'vehicles.vh_brand', 'vehicles.vh_plate', 'vh_type', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('events', 'reservations.event_id', '=', 'events.event_id')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');




        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('events.ev_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('requestors.rq_full_name', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('reservation_vehicles.vehicles', function ($query) use ($searchValue) {
                        $query->where('vh_brand', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('reservation_vehicles.drivers', function ($query) use ($searchValue) {
                        $query->where('dr_fname', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhere('reservations.rs_voucher', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.rs_approval_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.rs_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.rs_travel_type', 'like', '%' . $searchValue . '%');
            });
        }
        $filteredReservations = $reservations->get();
        $rows = $filteredReservations->count();

        $templateProcessor = new TemplateProcessor(public_path() . '\\' . "Reservations.docx");

        $templateProcessor->cloneRow('reservation_id', $rows);

        for ($i = 0; $i < $rows; $i++) {
            $reservation = $filteredReservations[$i];
            $formattedDate = Carbon::parse($reservation->created_at)->format('F j, Y');
            $templateProcessor->setValue("reservation_id#" . ($i + 1), $reservation->reservation_id);
            $templateProcessor->setValue("event_id#" . ($i + 1), $reservation->ev_name);
            $templateProcessor->setValue("driver_id#" . ($i + 1), $reservation->dr_fname . " " . $reservation->dr_lname);
            $templateProcessor->setValue("vehicle_id#" . ($i + 1), $reservation->vh_brand);
            $templateProcessor->setValue("vehicle_type#" . ($i + 1), $reservation->vh_type);
            $templateProcessor->setValue("requestor_id#" . ($i + 1),  $reservation->rq_full_name);
            $templateProcessor->setValue("rs_voucher#" . ($i + 1), $reservation->rs_voucher);
            $templateProcessor->setValue("rs_travel_type#" . ($i + 1), $reservation->rs_travel_type);
            $templateProcessor->setValue("created_at#" . ($i + 1), $formattedDate);
            $templateProcessor->setValue("rs_approval_status#" . ($i + 1), $reservation->rs_approval_status);
            $templateProcessor->setValue("rs_status#" . ($i + 1), $reservation->rs_status);
        }

        $templateProcessor->saveAs(public_path() . '\\' . "WordDownloads\sample_downloads.docx");
        return response()->download(public_path() . '\\' . "WordDownloads\sample_downloads.docx", "ReservationsList.docx")->deleteFileAfterSend(true);
    }

    public function reservations_excel(Request $request)
    {
        $templateFilePath = 'Reservations.xlsx';
        $spreadsheet = new Spreadsheet();

        // Retrieve filtered reservations based on the search value
        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'vehicles.vh_brand', 'vehicles.vh_plate', 'vh_type', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('events', 'reservations.event_id', '=', 'events.event_id')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');

        // Apply search filter if a search value is provided
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('ev_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('dr_fname', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_brand', 'like', '%' . $searchValue . '%')
                    ->orWhere('rq_full_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_voucher', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_approval_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_travel_type', 'like', '%' . $searchValue . '%');
            });
        }

        // Execute the query to get filtered reservations
        $filteredReservations = $reservations->get();
        // dd($filteredReservations);
        $spreadsheet = IOFactory::load($templateFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Populate spreadsheet with filtered reservation data
        foreach ($filteredReservations as $index => $reservation) {
            $rowIndex = $index + 2;
            $formattedDate = Carbon::parse($reservation->created_at)->format('F j, Y');
            $sheet->setCellValue('A' . $rowIndex, $reservation->reservation_id);
            $sheet->setCellValue('B' . $rowIndex, $reservation->ev_name);
            $sheet->setCellValue('C' . $rowIndex, $reservation->dr_fname);
            $sheet->setCellValue('D' . $rowIndex, $reservation->vh_brand);
            $sheet->setCellValue('E' . $rowIndex, $reservation->rq_full_name);
            $sheet->setCellValue('F' . $rowIndex, $reservation->rs_voucher);
            $sheet->setCellValue('G' . $rowIndex, $reservation->rs_travel_type);
            $sheet->setCellValue('H' . $rowIndex, $formattedDate);
            $sheet->setCellValue('I' . $rowIndex, $reservation->rs_approval_status);
            $sheet->setCellValue('J' . $rowIndex, $reservation->rs_status);
        }

        // Save and download the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Lakbay_Reservations.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
    public function reservations_pdf(Request $request)
    {
        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'vehicles.vh_brand', 'vehicles.vh_plate', 'requestors.rq_full_name', 'vh_type', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('events', 'reservations.event_id', '=', 'events.event_id')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('ev_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('dr_fname', 'like', '%' . $searchValue . '%')
                    ->orWhere('vh_brand', 'like', '%' . $searchValue . '%')
                    ->orWhere('rq_full_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('reservations.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_voucher', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_approval_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_status', 'like', '%' . $searchValue . '%')
                    ->orWhere('rs_travel_type', 'like', '%' . $searchValue . '%');
            });
        }

        $filteredReservations = $reservations->get();
        $phpWord = new PhpWord();

        // Load the template
        $templateProcessor = new TemplateProcessor(public_path('Reservations.docx'));

        $rows = $filteredReservations->count();

        $templateProcessor->cloneRow('reservation_id', $rows);
        foreach ($filteredReservations as $index => $reservation) {
            $formattedDate = Carbon::parse($reservation->created_at)->format('F j, Y');
            $templateProcessor->setValue("reservation_id#" . ($index + 1), $reservation->reservation_id);
            $templateProcessor->setValue("event_id#" . ($index + 1), $reservation->ev_name);
            $templateProcessor->setValue("driver_id#" . ($index + 1), $reservation->dr_fname);
            $templateProcessor->setValue("vehicle_id#" . ($index + 1), $reservation->vh_brand . '-' . $reservation->vh_plate);
            $templateProcessor->setValue("vehicle_type#" . ($i + 1), $reservation->vh_type);
            $templateProcessor->setValue("requestor_id#" . ($index + 1), $reservation->rq_full_name);
            $templateProcessor->setValue("rs_voucher#" . ($index + 1), $reservation->rs_voucher);
            $templateProcessor->setValue("rs_travel_type#" . ($index + 1), $reservation->rs_travel_type);
            $templateProcessor->setValue("created_at#" . ($index + 1), $formattedDate);
            $templateProcessor->setValue("rs_approval_status#" . ($index + 1), $reservation->rs_approval_status);
            $templateProcessor->setValue("rs_status#" . ($index + 1), $reservation->rs_status);
        }

        $wordFilePath = public_path() . '\\' . "WordDownloads\\reservations_list.docx";
        $pdfFilePath = public_path() . '\\' . "PdfDownloads\\reservations_list.pdf";

        $templateProcessor->saveAs($wordFilePath);

        // Load Word document
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordFilePath);

        // Set up Dompdf renderer
        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
        Settings::setPdfRendererName('DomPDF');

        // Save PDF file
        $pdfWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($pdfFilePath);

        // Delete the Word file
        unlink($wordFilePath);

        // Return response for PDF download
        return response()->download($pdfFilePath, "ReservationsList.pdf")->deleteFileAfterSend(true);
    }
    public function reservations_archive()
    {
    }
    public function test_select()
    {

        return view('test_select');
    }
    public function test_return()
    {
        $driversInsert = DB::table('drivers')
            ->leftJoin('reservation_vehicles', 'drivers.driver_id', 'reservation_vehicles.driver_id')
            ->whereNull('reservation_vehicles.driver_id')
            ->select('drivers.driver_id', 'dr_fname')
            ->get();
        return response()->json($driversInsert);
    }

}
