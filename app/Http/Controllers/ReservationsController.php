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


class ReservationsController extends Controller
{
    public function index()
    {
        $offices = Offices::all();
        $requestors = Requestors::all();
        return view('admin.reservations', compact('offices', 'requestors'));
    }

    public function getData()
    {
        try {
            $reservations = Reservations::with(['events', 'requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
                ->select('reservations.*')
                ->get();

            \Log::info('Reservations data:', $reservations->toArray());

            $datatablesData = DataTables::of($reservations)
                ->addColumn('event_name', function ($reservation) {
                    \Log::info('Event data:', ['event' => $reservation->events]);
                    return $reservation->events ? $reservation->events->ev_name : 'N/A';
                })
                ->addColumn('action', function ($reservation) {
                    return '
                        <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="'.$reservation->reservation_id.'">Edit</button>
                        <button type="button" class="btn btn-sm btn-success approve-btn" data-id="'.$reservation->reservation_id.'">Approve</button>
                        <button type="button" class="btn btn-sm btn-warning cancel-btn" data-id="'.$reservation->reservation_id.'">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="'.$reservation->reservation_id.'">Reject</button>
                        <button type="button" class="btn btn-sm btn-info done-btn" data-id="'.$reservation->reservation_id.'">Done</button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$reservation->reservation_id.'">Delete</button>
                    ';
                })
                ->addColumn('vehicles', function ($reservation) {
                    return $reservation->reservation_vehicles->map(function ($rv) {
                        $vehicle = $rv->vehicles;
                        return [
                            'vh_brand' => $vehicle->vh_brand,
                            'vh_model' => $vehicle->vh_model,
                            'vh_type' => $vehicle->vh_type,
                            'vh_plate' => $vehicle->vh_plate
                        ];
                    })->toArray();
                })
                ->addColumn('drivers', function ($reservation) {
                    $drivers = $reservation->reservation_vehicles->map(function ($rv) {
                        return $rv->drivers ? $rv->drivers->dr_fname . ' ' . $rv->drivers->dr_lname : 'N/A';
                    })->filter()->implode(', ');
                    \Log::info("Drivers for reservation {$reservation->reservation_id}: " . $drivers);
                    return $drivers;
                })
                ->addColumn('requestor', function ($reservation) {
                    return $reservation->requestors->rq_full_name ?? 'N/A';
                })
                ->addColumn('rs_purpose', function ($reservation) {
                    return $reservation->rs_purpose ?? 'N/A';
                })
                ->rawColumns(['vehicles', 'drivers', 'action'])
                ->toJson();

            \Log::info('DataTables response:', ['response' => $datatablesData]);

            return $datatablesData;
        } catch (\Exception $e) {
            \Log::error('Error in ReservationsController@getData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            \Log::info("AdminReservationsController@show called");

            if ($request->ajax()) {
                \Log::info("Processing AJAX request");
                $reservations = Reservations::with(['events', 'requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
                    ->select('reservations.*');

                return DataTables::of($reservations)
                    ->addColumn('action', function ($reservation) {
                        return '
                            <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="'.$reservation->reservation_id.'">Edit</button>
                            <button type="button" class="btn btn-sm btn-success approve-btn" data-id="'.$reservation->reservation_id.'">Approve</button>
                            <button type="button" class="btn btn-sm btn-warning cancel-btn" data-id="'.$reservation->reservation_id.'">Cancel</button>
                            <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="'.$reservation->reservation_id.'">Reject</button>
                            <button type="button" class="btn btn-sm btn-info done-btn" data-id="'.$reservation->reservation_id.'">Done</button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$reservation->reservation_id.'">Delete</button>
                        ';
                    })
                    ->addColumn('vehicles', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            $vehicle = $rv->vehicles;
                            return [
                                'vh_brand' => $vehicle->vh_brand,
                                'vh_model' => $vehicle->vh_model,
                                'vh_type' => $vehicle->vh_type,
                                'vh_plate' => $vehicle->vh_plate
                            ];
                        })->toArray();
                    })
                    ->addColumn('drivers', function ($reservation) {
                        $drivers = $reservation->reservation_vehicles->map(function ($rv) {
                            return $rv->drivers ? $rv->drivers->dr_fname . ' ' . $rv->drivers->dr_lname : 'N/A';
                        })->filter()->implode(', ');
                        \Log::info("Drivers for reservation {$reservation->reservation_id}: " . $drivers);
                        return $drivers;
                    })
                    ->addColumn('requestor', function ($reservation) {
                        return $reservation->requestors->rq_full_name ?? 'N/A';
                    })
                    ->rawColumns(['vehicles', 'drivers', 'action'])
                    ->make(true);
            }

            $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
                ->orderBy('dr_fname')
                ->get();

            $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
                ->orderBy('vh_brand')
                ->get();

            $offices = Offices::select('off_id', 'off_acr', 'off_name')->get();
            $requestors = Requestors::all();

            return view('admin.reservations', compact('drivers', 'vehicles', 'offices', 'requestors'));
        } catch (\Exception $e) {
            \Log::error('Error in AdminReservationsController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }


    public function event_calendar()
    {
        $colors = ['#d5c94c', '#4522ea', '#45a240', '#7c655a', '#cf4c11'];

        $events = Events::all()->map(function ($event) use ($colors) {
            return [
                'title' => $event->ev_name,
                'start' => $event->ev_date_start,
                'end' => $event->ev_date_end,
                'color' => $colors[array_rand($colors)],
            ];
        });

        return view('event_calendar')->with(compact('events'));
    }
    public function drivers_schedules()
    {
        // $reservations = Reservations::with("events")
        //     ->select('reservations.*', 'events.ev_name', 'events.ev_date_start','events.event_id')
        //     ->join('events', 'reservations.event_id', '=', 'events.event_id')
        //     ->get();


        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers", "events")
            ->select('reservations.*', 'events.ev_name', 'events.ev_date_start', 'drivers.dr_fname', 'vehicles.vh_brand', 'vh_type' , 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('events', 'reservations.event_id', '=', 'events.event_id')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id')
            ->get();


        $drivers = Drivers::all();
        $existingVehicleIds = ReservationVehicle::pluck('driver_id')->toArray();
        return view('drivers_schedule')->with(compact('drivers', 'reservations'));
    }

    public function events()
    {
        try {
            $events = Events::select('event_id', 'ev_name', 'ev_venue')
                ->orderBy('ev_name')
                ->get();

            \Log::channel('custom')->info('Events fetched:', ['count' => $events->count(), 'events' => $events->toArray()]);

            return response()->json($events);
        } catch (\Exception $e) {
            \Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }
    }

    public function events_edit()
    {
        $eventsInsert = Events::leftJoin('reservations', 'events.event_id', 'reservations.event_id')
            ->whereNull('reservations.reservation_id')
            ->orWhere([
                ['reservations.rs_status', 'Cancelled'],
                ['rs_cancelled', 0]
            ])
            ->select('events.event_id', 'ev_name', 'ev_venue')
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
            ->select('vehicles.vehicle_id', 'vh_capacity', 'vh_brand', 'vh_type')
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
        DB::beginTransaction();
        try {
            $data = $request->all();
            
            if ($request->has('is_outsider') && $request->is_outsider == 'on') {
                // If it's an outsider, create a new Office
                $office = Offices::create([
                    'off_name' => $request->outside_office,
                    'off_acr' => substr($request->outside_office, 0, 5), // Generate an acronym
                    'off_head' => 'Unknown', // Set a default value for off_head
                ]);
                $data['off_id'] = $office->off_id;

                // Create a new Requestor
                $requestor = Requestors::create([
                    'rq_full_name' => $request->outside_requestor,
                    // Add any other necessary fields for Requestor
                ]);
                $data['requestor_id'] = $requestor->requestor_id;
            }

            // Create the event
            $event = Events::create(['ev_name' => $data['event_name']]);
            $data['event_id'] = $event->event_id;

            // Remove these fields as they're not in the reservations table
            unset($data['is_outsider']);
            unset($data['outside_requestor']);
            unset($data['outside_office']);
            unset($data['event_name']);

            $reservation = Reservations::create($data);

            // Handle drivers and vehicles
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $driverIds = $request->input('driver_id');
                $vehicleIds = $request->input('vehicle_id');
                foreach ($driverIds as $index => $driverId) {
                    if (isset($vehicleIds[$index])) {
                        $reservation->reservation_vehicles()->create([
                            'driver_id' => $driverId,
                            'vehicle_id' => $vehicleIds[$index],
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Reservation created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating reservation: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating reservation: ' . $e->getMessage()], 500);
        }
    }




public function edit($id)
{
    try {
        $reservation = Reservations::with(['events', 'reservation_vehicles.drivers', 'reservation_vehicles.vehicles', 'requestors', 'office'])
            ->findOrFail($id);
        
        \Log::info('Reservation data:', $reservation->toArray());
        
        return response()->json(['reservation' => $reservation]);
    } catch (\Exception $e) {
        \Log::error('Error fetching reservation: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch reservation'], 500);
    }
}

    public function delete($reservation_id)
{
    try {
        // Delete related records in reservation_vehicles
        ReservationVehicle::where('reservation_id', $reservation_id)->delete();

        // Delete the reservation
        $reservation = Reservations::findOrFail($reservation_id);
        $reservation->delete();

        return response()->json(['success' => 'Reservation deleted successfully.']);
    } catch (\Exception $e) {
        \Log::error('Error deleting reservation: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to delete reservation'], 500);
    }
}
    public function cancel($id)
{
    try {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_approval_status = 'Cancelled';
        $reservation->rs_status = 'Cancelled';
        $reservation->save();

        // Free up the reserved driver and vehicle
        $reservation->reservation_vehicles()->update(['driver_id' => null, 'vehicle_id' => null]);

        return response()->json(['success' => 'Reservation successfully cancelled']);
    } catch (\Exception $e) {
        \Log::error('Error cancelling reservation: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to cancel reservation'], 500);
    }
}

    public function reservations_word(Request $request)
    {

        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'dr_mname', 'dr_lname', 'vehicles.vh_brand', 'vh_type', 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
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
                    ->orWhere('reservations.rs_purpose', 'like', '%' . $searchValue . '%')
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
            $templateProcessor->setValue("requestor_id#" . ($i + 1),  $reservation->rq_full_name);
            $templateProcessor->setValue("rs_purpose#" . ($i + 1), $reservation->rs_purpose);
            $templateProcessor->setValue("rs_travel_type#" . ($i + 1), $reservation->rs_travel_type);
            $templateProcessor->setValue("created_at#" . ($i + 1), $formattedDate);
            $templateProcessor->setValue("rs_approval_status#" . ($i + 1), $reservation->rs_approval_status);
            $templateProcessor->setValue("rs_status#" . ($i + 1), $reservation->rs_status);
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

    public function reservations_excel(Request $request)
    {
        $templateFilePath = 'Reservations.xlsx';
        $spreadsheet = new Spreadsheet();

        // Retrieve filtered reservations based on the search value
        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'vehicles.vh_brand', 'vh_type' ,'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
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
                    ->orWhere('rs_purpose', 'like', '%' . $searchValue . '%')
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
            $sheet->setCellValue('F' . $rowIndex, $reservation->rs_purpose);
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
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'vehicles.vh_brand', 'vh_type', 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
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
                    ->orWhere('rs_purpose', 'like', '%' . $searchValue . '%')
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
            $templateProcessor->setValue("vehicle_id#" . ($index + 1), $reservation->vh_brand . ' - ' . $reservation->vh_plate);
            $templateProcessor->setValue("requestor_id#" . ($index + 1), $reservation->rq_full_name);
            $templateProcessor->setValue("rs_purpose#" . ($index + 1), $reservation->rs_purpose);
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

    public function showForm()
{
    $offices = Office::all();
    
    return view('users.reservations', ['offices' => $offices]);
}

public function getDrivers(Request $request)
{
    $startDateTime = $request->input('start_datetime');
    $endDateTime = $request->input('end_datetime');
    $reservationId = $request->input('reservation_id');

    $unavailableDriverIds = ReservationVehicle::whereHas('reservation', function ($query) use ($startDateTime, $endDateTime, $reservationId) {
        $query->where('rs_status', '!=', 'Done')
              ->where('reservation_id', '!=', $reservationId);
        
        if ($startDateTime && $endDateTime) {
            $query->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('rs_date_start', [$startDateTime, $endDateTime])
                  ->orWhereBetween('rs_date_end', [$startDateTime, $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('rs_date_start', '<=', $startDateTime)
                         ->where('rs_date_end', '>=', $endDateTime);
                  });
            });
        }
    })->pluck('driver_id')->toArray();

    $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
        ->orderBy('dr_fname')
        ->get()
        ->map(function ($driver) use ($unavailableDriverIds) {
            return [
                'id' => $driver->driver_id,
                'name' => $driver->dr_fname . ' ' . $driver->dr_lname,
                'reserved' => in_array($driver->driver_id, $unavailableDriverIds)
            ];
        });

    return response()->json(['drivers' => $drivers]);
}

public function getVehicles(Request $request)
{
    $startDateTime = $request->input('start_datetime');
    $endDateTime = $request->input('end_datetime');
    $reservationId = $request->input('reservation_id');

    $unavailableVehicleIds = ReservationVehicle::whereHas('reservation', function ($query) use ($startDateTime, $endDateTime, $reservationId) {
        $query->where('rs_status', '!=', 'Done')
              ->where('reservation_id', '!=', $reservationId);
        
        if ($startDateTime && $endDateTime) {
            $query->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('rs_date_start', [$startDateTime, $endDateTime])
                  ->orWhereBetween('rs_date_end', [$startDateTime, $endDateTime])
                  ->orWhere(function ($q2) use ($startDateTime, $endDateTime) {
                      $q2->where('rs_date_start', '<=', $startDateTime)
                         ->where('rs_date_end', '>=', $endDateTime);
                  });
            });
        }
    })->pluck('vehicle_id')->toArray();

    $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
        ->orderBy('vh_brand')
        ->get()
        ->map(function ($vehicle) use ($unavailableVehicleIds) {
            return [
                'id' => $vehicle->vehicle_id,
                'name' => $vehicle->vh_brand . ' (' . $vehicle->vh_type . ') - ' . $vehicle->vh_plate,
                'reserved' => in_array($vehicle->vehicle_id, $unavailableVehicleIds)
            ];
        });

    return response()->json(['vehicles' => $vehicles]);
}

public function markAsDone($id)
{
    try {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_status = 'Done';
        $reservation->save();

        return response()->json(['success' => 'Reservation marked as done successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error marking reservation as done: ' . $e->getMessage()], 500);
    }
}

public function approve($id)
{
    return $this->updateStatus($id, 'Approved', 'Ongoing');
}

public function reject($id)
{
    return $this->updateStatus($id, 'Rejected', 'Rejected');
}

private function updateStatus($id, $approvalStatus, $reservationStatus)
{
    $reservation = Reservations::findOrFail($id);
    $reservation->rs_approval_status = $approvalStatus;
    $reservation->rs_status = $reservationStatus;
    $reservation->save();

    return response()->json(['success' => 'Reservation status updated successfully']);
}



public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        \Log::info('Update request received', $request->all());

        $reservation = Reservations::with('events')->findOrFail($id);

        \Log::info('Reservation found', $reservation->toArray());

        // Update the reservation
        $reservation->fill($request->only([
            'rs_passengers', 'rs_travel_type',
            'rs_purpose', 'rs_approval_status', 'rs_status', 'rs_from',
            'rs_date_start', 'rs_time_start', 'rs_date_end', 'rs_time_end',
            'reason'
        ]));

        // Handle outsider data
        if ($request->input('is_outsider') === 'true') {
            // Create a new Office
            $office = Offices::create([
                'off_name' => $request->input('outside_office'),
                'off_acr' => substr($request->input('outside_office'), 0, 5), // Generate an acronym
                'off_head' => 'Unknown', // Set a default value for off_head
            ]);
            $reservation->off_id = $office->off_id;

            // Create a new Requestor
            $requestor = Requestors::create([
                'rq_full_name' => $request->input('outside_requestor'),
                // Add any other necessary fields for Requestor
            ]);
            $reservation->requestor_id = $requestor->requestor_id;
        } else {
            $reservation->off_id = $request->input('off_id');
            $reservation->requestor_id = $request->input('requestor_id');
        }

        // Ensure requestor_id and off_id are not null
        if (!$reservation->requestor_id) {
            throw new \Exception('Requestor ID cannot be null');
        }
        if (!$reservation->off_id) {
            throw new \Exception('Office ID cannot be null');
        }

        // Update the associated event
        if ($request->has('event_name')) {
            $reservation->events()->update([
                'ev_name' => $request->input('event_name')
            ]);
        }

        // If the reservation is cancelled or rejected, delete the reservation_vehicles
        if (in_array($request->rs_status, ['Cancelled', 'Rejected'])) {
            $reservation->reservation_vehicles()->delete();
        } else {
            // Update reservation vehicles
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $reservation->reservation_vehicles()->delete();

                $driverIds = $request->input('driver_id', []);
                $vehicleIds = $request->input('vehicle_id', []);

                if (is_array($driverIds) && is_array($vehicleIds)) {
                    foreach ($driverIds as $index => $driverId) {
                        if (isset($vehicleIds[$index])) {
                            $reservation->reservation_vehicles()->create([
                                'driver_id' => $driverId,
                                'vehicle_id' => $vehicleIds[$index],
                            ]);
                        }
                    }
                } else {
                    \Log::warning('Driver IDs or Vehicle IDs are not arrays');
                }
            }
        }

        $reservation->save();

        DB::commit();

        \Log::info('Reservation updated successfully');

        return response()->json([
            'success' => 'Reservation updated successfully',
            'reservation' => $reservation->load('events', 'reservation_vehicles.drivers', 'reservation_vehicles.vehicles', 'requestors', 'office'),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating reservation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'Error updating reservation: ' . $e->getMessage()], 500);
    }
}
}