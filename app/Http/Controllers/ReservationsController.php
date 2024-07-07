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
    public function show(Request $request)
    {
        try {
            if ($request->ajax()) {
                $reservations = Reservations::with(['events', 'requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
                    ->select('reservations.*');

                // If the user is not an admin, filter the reservations
                if (!auth()->user()->isAdmin()) {
                    $reservations->where('requestor_id', auth()->id());
                }

                // Log the SQL query
                \Log::info($reservations->toSql());
                \Log::info($reservations->getBindings());

                $data = DataTables::of($reservations)
                    ->addColumn('ev_name', function ($reservation) {
                        return $reservation->events ? $reservation->events->ev_name : 'N/A';
                    })
                    ->addColumn('vehicles', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            return $rv->vehicles ? $rv->vehicles->vh_brand : 'N/A';
                        })->implode(', ');
                    })
                    ->addColumn('drivers', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            return $rv->drivers ? $rv->drivers->dr_fname : 'N/A';
                        })->filter()->implode(', ');
                    })
                    ->addColumn('rq_full_name', function ($reservation) {
                        return $reservation->requestors ? $reservation->requestors->rq_full_name : 'N/A';
                    })
                    ->addColumn('office', function ($reservation) {
                        return $reservation->office ? $reservation->office->off_name: 'N/A';
                    })
                    ->editColumn('created_at', function ($reservation) {
                        return $reservation->created_at->format('F d, Y');
                    })
                    ->addColumn('action', function ($reservation) {
                        return '<a href="'.route('reservations.edit', $reservation->reservation_id).'" class="btn btn-sm btn-primary">Edit</a>
                                <a href="'.route('reservations.delete', $reservation->reservation_id).'" class="btn btn-sm btn-danger">Delete</a>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);

                // Log the final data being returned
                \Log::info('DataTables data:', $data->getData(true));

                return $data;
            }
        } catch (\Exception $e) {
            \Log::error('Error in ReservationsController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
        
        $existingDriverIds = ReservationVehicle::whereNotNull('driver_id')->distinct('driver_id')->pluck('driver_id')->toArray();
        $existingVehicleIds = ReservationVehicle::pluck('vehicle_id')->toArray();

        $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
            ->orderBy('dr_fname')
            ->get();

        $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
            ->orderBy('vh_brand')
            ->get();

        $events = Events::select('event_id', 'ev_name', 'ev_venue')
            ->orderBy('ev_name')
            ->get();

        // Add this line to log the events
        \Log::info('Events:', $events->toArray());

        $requestors = DB::table('requestors')->select('requestor_id', 'rq_full_name')->get();
        
        $offices = DB::table('offices')->select('off_id', 'off_acr', 'off_name')->get();
        
        if (auth()->user()->isAdmin()) {
            return view('admin/reservations')->with(compact('events', 'drivers', 'vehicles', 'requestors', 'offices'));
        } else {
            return view('users/reservations')->with(compact('events', 'drivers', 'vehicles', 'requestors', 'offices'));
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

            \Log::info('Events fetched:', ['count' => $events->count(), 'events' => $events->toArray()]);

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
        \Log::info('Received reservation data:', $request->all());
        \Log::info('off_id value:', [$request->input('off_id')]);

        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'driver_id' => 'required|array',
            'driver_id.*' => 'exists:drivers,driver_id',
            'vehicle_id' => 'required|array',
            'vehicle_id.*' => 'exists:vehicles,vehicle_id',
            'requestor_id' => 'required|exists:requestors,requestor_id',
            'off_id' => 'required|exists:offices,off_id',
            'rs_passengers' => 'required|integer',
            'rs_travel_type' => 'required|string',
            'rs_voucher' => 'required|string',
            'rs_approval_status' => 'required|string',
            'rs_status' => 'required|string',
        ]);

        \Log::info('Validated reservation data:', $validatedData);

        try {
            DB::beginTransaction();

            $reservation = new Reservations();
            $reservation->event_id = $validatedData['event_id'];
            $reservation->requestor_id = $validatedData['requestor_id'];
            $reservation->off_id = $validatedData['off_id'];
            $reservation->rs_passengers = $validatedData['rs_passengers'];
            $reservation->rs_travel_type = $validatedData['rs_travel_type'];
            $reservation->rs_voucher = $validatedData['rs_voucher'];
            $reservation->rs_approval_status = $validatedData['rs_approval_status'];
            $reservation->rs_status = $validatedData['rs_status'];
            $reservation->save();

            \Log::info('Reservation saved:', $reservation->toArray());

            // Save driver and vehicle associations
            foreach ($validatedData['driver_id'] as $index => $driverId) {
                $vehicleId = $validatedData['vehicle_id'][$index] ?? null;
                if ($vehicleId) {
                    ReservationVehicles::create([
                        'reservation_id' => $reservation->reservation_id,
                        'driver_id' => $driverId,
                        'vehicle_id' => $vehicleId,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => 'Reservation created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating reservation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create reservation.'], 500);
        }
    }


    public function update(Request $request)
    {
        \Log::info('Update method called with data:', $request->all());
        $id = $request->input('hidden_id');
        \Log::info('Attempting to find reservation with ID: ' . $id);

        if (!$id) {
            return response()->json(['error' => 'No reservation ID provided'], 400);
        }

        try {
            $reservation = Reservations::findOrFail($id);
            $reservation->update($request->except(['_token', 'hidden_id']));

            return response()->json(['success' => 'Reservation updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating reservation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update reservation: ' . $e->getMessage()], 500);
        }
    }
    public function edit($reservation_id)
    {
        if (request()->ajax()) {
            \Log::info('Edit method called with reservation_id: ' . $reservation_id);
            
            try {
                $data = Reservations::with(['reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'events', 'requestors', 'office'])
                    ->select('reservations.*', 'events.ev_name', 'requestors.rq_full_name', 'offices.off_name')
                    ->join('events', 'reservations.event_id', '=', 'events.event_id')
                    ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
                    ->leftJoin('offices', 'reservations.off_id', '=', 'offices.off_id')
                    ->where('reservations.reservation_id', $reservation_id)
                    ->firstOrFail();
    
                \Log::info('Fetched reservation data:', $data->toArray());
    
                return response()->json(['result' => $data]);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                \Log::error('Reservation not found: ' . $reservation_id);
                return response()->json(['error' => 'Reservation not found'], 404);
            } catch (\Exception $e) {
                \Log::error('Error in edit method: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                return response()->json(['error' => 'An error occurred while fetching the reservation'], 500);
            }
        }
    }
    public function delete($reservation_id)
{
    // Delete related records in reservation_vehicles
    ReservationVehicle::where('reservation_id', $reservation_id)->delete();

    // Delete the reservation
    $reservation = Reservations::findOrFail($reservation_id);
    $reservation->delete();

    return response()->json(['success' => 'Reservation deleted successfully.']);
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
            ->select('reservations.*', 'events.ev_name', 'drivers.dr_fname', 'drivers.dr_mname', 'drivers.dr_lname', 'vehicles.vh_brand', 'vh_type', 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
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
            $templateProcessor->setValue("requestor_id#" . ($i + 1),  $reservation->rq_full_name);
            $templateProcessor->setValue("rs_voucher#" . ($i + 1), $reservation->rs_voucher);
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

    public function showForm()
{
    $offices = Office::all();
    
    return view('users.reservations', ['offices' => $offices]);
}

public function getDriversAndVehicles()
{
    try {
        $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
            ->orderBy('dr_fname')
            ->get();

        $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
            ->orderBy('vh_brand')
            ->get();

        \Log::info('Drivers and Vehicles fetched:', [
            'drivers_count' => $drivers->count(),
            'vehicles_count' => $vehicles->count()
        ]);

        return response()->json([
            'drivers' => $drivers,
            'vehicles' => $vehicles
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching drivers and vehicles: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch drivers and vehicles'], 500);
    }
}
}

