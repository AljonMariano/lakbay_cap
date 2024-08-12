<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Drivers;
use App\Models\Offices;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


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
        $reservations = Reservations::with(['requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers'])
            ->select('reservations.*');

        return DataTables::of($reservations)
            ->addColumn('requestor', function ($reservation) {
                return $reservation->is_outsider ? ($reservation->outside_requestor ?? 'N/A') : ($reservation->requestors->rq_full_name ?? 'N/A');
            })
            ->addColumn('office', function ($reservation) {
                return $reservation->is_outsider ? ($reservation->outside_office ?? 'N/A') : ($reservation->office->off_name ?? 'N/A');
            })
            ->addColumn('vehicle_name', function ($reservation) {
                return $reservation->reservation_vehicles->map(function ($rv) {
                    return $rv->vehicles->vh_plate ?? 'N/A';
                })->filter()->implode(', ') ?: 'N/A';
            })
            ->addColumn('driver_name', function ($reservation) {
                return $reservation->reservation_vehicles->map(function ($rv) {
                    return $rv->drivers ? ($rv->drivers->dr_fname . ' ' . $rv->drivers->dr_lname) : 'N/A';
                })->filter()->implode(', ') ?: 'N/A';
            })
            ->addColumn('created_at', function ($reservation) {
                return $reservation->created_at;
            })
            ->addColumn('reason', function ($reservation) {
                return $reservation->reason ?? '';
            })
            ->addColumn('action', function ($reservation) {
                $buttons = '
                    <button class="btn btn-sm btn-success approve-btn" data-id="'.$reservation->reservation_id.'">Approve</button>
                    <button class="btn btn-sm btn-danger reject-btn" data-id="'.$reservation->reservation_id.'">Reject</button>
                    <button class="btn btn-sm btn-warning cancel-btn" data-id="'.$reservation->reservation_id.'">Cancel</button>
                    <button class="btn btn-sm btn-primary edit-btn" data-id="'.$reservation->reservation_id.'">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="'.$reservation->reservation_id.'">Delete</button>
                    <button class="btn btn-sm btn-info done-btn" data-id="'.$reservation->reservation_id.'">Done</button>
                ';
                return $buttons;
            })
            ->editColumn('rs_time_start', function ($reservation) {
                return date('h:i A', strtotime($reservation->rs_time_start));
            })
            ->editColumn('rs_time_end', function ($reservation) {
                return date('h:i A', strtotime($reservation->rs_time_end));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show(Request $request)
    {
        try {
            \Log::info("AdminReservationsController@show called");

            if ($request->ajax()) {
                \Log::info("Processing AJAX request");
                $reservations = Reservations::with(['requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
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

    public function update(Request $request, $id)
    {
        \Log::info('Update method called for reservation ID: ' . $id);

        try {
            $reservation = Reservations::findOrFail($id);
            
            $validatedData = $request->validate([
                'destination_activity' => 'required|string',
                'rs_from' => 'required|string',
                'rs_date_start' => 'required|date',
                'rs_time_start' => 'required',
                'rs_date_end' => 'required|date',
                'rs_time_end' => 'required',
                'rs_passengers' => 'required|integer',
                'rs_travel_type' => 'required|string',
                'rs_purpose' => 'required|string',
                'off_id' => 'required_without:outside_office',
                'outside_office' => 'required_if:is_outsider,1',
                'requestor_id' => 'required_without:outside_requestor',
                'outside_requestor' => 'required_if:is_outsider,1',
                'driver_id' => 'required|array',
                'vehicle_id' => 'required|array',
                'is_outsider' => 'boolean',
                'rs_reason' => 'nullable|string',
            ]);

            // Check for conflicting reservations
            $conflictingReservations = Reservations::where(function ($query) use ($request, $id) {
                $query->whereRaw("CONCAT(rs_date_start, ' ', rs_time_start) < ?", [$request->rs_date_end . ' ' . $request->rs_time_end])
                      ->whereRaw("CONCAT(rs_date_end, ' ', rs_time_end) > ?", [$request->rs_date_start . ' ' . $request->rs_time_start]);
                
                if ($id) {
                    $query->where('reservation_id', '!=', $id);
                }
            })
            ->whereIn('rs_status', ['Pending', 'Approved', 'On-Going'])
            ->whereHas('reservation_vehicles', function ($query) use ($request) {
                $query->whereIn('driver_id', $request->driver_id)
                      ->orWhereIn('vehicle_id', $request->vehicle_id);
            })
            ->exists();

            if ($conflictingReservations) {
                return response()->json(['error' => 'The selected driver(s) or vehicle(s) are not available for the specified time range.'], 422);
            }

            DB::beginTransaction();

            $validatedData['rs_time_start'] = $this->convertTo24HourFormat($request->rs_time_start);
            $validatedData['rs_time_end'] = $this->convertTo24HourFormat($request->rs_time_end);

            $reservation->update($validatedData);

            // Handle drivers and vehicles
            $reservation->reservation_vehicles()->delete();
            foreach ($request->driver_id as $index => $driverId) {
                $reservation->reservation_vehicles()->create([
                    'driver_id' => $driverId,
                    'vehicle_id' => $request->vehicle_id[$index],
                ]);
            }

            DB::commit();

            $updatedReservation = $reservation->fresh()->load('requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers');
            
            // Log the updated reservation data
            Log::info('Updated reservation data', ['reservation' => $updatedReservation]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation updated successfully',
                'reservation' => $updatedReservation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating reservation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $id,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        \Log::info('Store method called');
        Log::info('Reservation store request:', $request->all());

        try {
            DB::beginTransaction();

            // Create a new reservation with the request data
            $reservationData = $request->all();
            
            // Set default status values
            $reservationData['rs_approval_status'] = 'Pending';
            $reservationData['rs_status'] = 'Pending';

            // Check for conflicting reservations
            $conflictingReservations = Reservations::where(function ($query) use ($request) {
                $query->whereRaw("CONCAT(rs_date_start, ' ', rs_time_start) < ?", [$request->rs_date_end . ' ' . $request->rs_time_end])
                      ->whereRaw("CONCAT(rs_date_end, ' ', rs_time_end) > ?", [$request->rs_date_start . ' ' . $request->rs_time_start]);
            })
            ->whereIn('rs_status', ['Pending', 'Approved', 'On-Going'])
            ->whereHas('reservation_vehicles', function ($query) use ($request) {
                $query->whereIn('driver_id', $request->driver_id)
                      ->orWhereIn('vehicle_id', $request->vehicle_id);
            })
            ->exists();

            if ($conflictingReservations) {
                return response()->json(['error' => 'The selected driver(s) or vehicle(s) are not available for the specified time range.'], 422);
            }

            $reservationData['rs_time_start'] = $this->convertTo24HourFormat($request->rs_time_start);
            $reservationData['rs_time_end'] = $this->convertTo24HourFormat($request->rs_time_end);

            $reservation = Reservations::create($reservationData);

            // Handle drivers and vehicles
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $driverIds = $request->input('driver_id');
                $vehicleIds = $request->input('vehicle_id');

                // Ensure both arrays have the same length
                $count = min(count($driverIds), count($vehicleIds));

                for ($i = 0; $i < $count; $i++) {
                    DB::table('reservation_vehicles')->insert([
                        'reservation_id' => $reservation->reservation_id,
                        'driver_id' => $driverIds[$i],
                        'vehicle_id' => $vehicleIds[$i],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            $reservation = $reservation->fresh()->load('requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers');

            Log::info('Reservation created successfully:', $reservation->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating reservation:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function reservations_word(Request $request)
    {
        $reservations = Reservations::with("reservation_vehicles", "reservation_vehicles.vehicles", "reservation_vehicles.drivers")
            ->select('reservations.*', 'drivers.dr_fname', 'dr_mname', 'dr_lname', 'vehicles.vh_brand', 'vh_type', 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('requestors.rq_full_name', 'like', '%' . $searchValue . '%')
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
            ->select('reservations.*', 'drivers.dr_fname', 'vehicles.vh_brand', 'vh_type' ,'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');

        // Apply search filter if a search value is provided
        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('dr_fname', 'like', '%' . $searchValue . '%')
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
            ->select('reservations.*', 'drivers.dr_fname', 'vehicles.vh_brand', 'vh_type', 'vehicles.vh_plate', 'requestors.rq_full_name', 'reservations.created_at', 'reservations.rs_approval_status', 'reservations.rs_status')
            ->join('requestors', 'reservations.requestor_id', '=', 'requestors.requestor_id')
            ->leftJoin('reservation_vehicles', 'reservations.reservation_id', '=', 'reservation_vehicles.reservation_id')
            ->leftJoin('vehicles', 'reservation_vehicles.vehicle_id', '=', 'vehicles.vehicle_id')
            ->leftJoin('drivers', 'reservation_vehicles.driver_id', '=', 'drivers.driver_id');

        if ($request->has('search')) {
            $searchValue = $request->input('search');
            $reservations->where(function ($query) use ($searchValue) {
                $query->where('dr_fname', 'like', '%' . $searchValue . '%')
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
                  ->where('reservation_id', '!=', $reservationId)
                  ->where(function ($q) use ($startDateTime, $endDateTime) {
                      $q->where(function ($q2) use ($startDateTime, $endDateTime) {
                          $q2->where('rs_date_start', '<=', $endDateTime)
                             ->where('rs_date_end', '>=', $startDateTime);
                      });
                  });
        })->pluck('driver_id')->toArray();

        $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
            ->orderBy('dr_fname')
            ->get()
            ->map(function ($driver) use ($unavailableDriverIds) {
                return [
                    'id' => $driver->driver_id,
                    'name' => $driver->dr_fname . ' ' . $driver->dr_lname,
                    'available' => !in_array($driver->driver_id, $unavailableDriverIds)
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
                  ->where('reservation_id', '!=', $reservationId)
                  ->where(function ($q) use ($startDateTime, $endDateTime) {
                      $q->where(function ($q2) use ($startDateTime, $endDateTime) {
                          $q2->where('rs_date_start', '<=', $endDateTime)
                             ->where('rs_date_end', '>=', $startDateTime);
                      });
                  });
        })->pluck('vehicle_id')->toArray();

        $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
            ->orderBy('vh_brand')
            ->get()
            ->map(function ($vehicle) use ($unavailableVehicleIds) {
                return [
                    'id' => $vehicle->vehicle_id,
                    'name' => $vehicle->vh_brand . ' (' . $vehicle->vh_plate . ') - ' . $vehicle->vh_type,
                    'available' => !in_array($vehicle->vehicle_id, $unavailableVehicleIds)
                ];
            });

        return response()->json(['vehicles' => $vehicles]);
    }

    public function approve($id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_approval_status = 'Approved';
        $reservation->rs_status = 'On-Going';
        $reservation->save();

        return response()->json(['success' => 'Reservation approved successfully']);
    }

    public function reject(Request $request, $id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_approval_status = 'Rejected';
        $reservation->rs_status = 'Rejected';
        $reservation->reason = $request->input('reason');
        $reservation->save();

        return response()->json(['success' => 'Reservation rejected successfully']);
    }

    public function cancel(Request $request, $id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_approval_status = 'Cancelled';
        $reservation->rs_status = 'Cancelled';
        $reservation->reason = $request->input('reason');
        $reservation->save();

        return response()->json(['success' => 'Reservation cancelled successfully']);
    }

    public function destroy($id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->delete();

        return response()->json(['success' => 'Reservation deleted successfully']);
    }

    public function markAsDone($id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->rs_approval_status = 'Done';
        $reservation->rs_status = 'Done';
        $reservation->save();

        return response()->json(['success' => 'Reservation marked as done successfully']);
    }

    public function edit($id)
    {
        $reservation = Reservations::with(['requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers'])->findOrFail($id);
        $drivers = Drivers::all();
        $vehicles = Vehicles::all();
        $offices = Offices::all();
        $requestors = Requestors::all();

        return response()->json([
            'reservation' => $reservation,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'offices' => $offices,
            'requestors' => $requestors,
            'is_outsider' => $reservation->is_outsider
        ]);
    }

    public function getDriversAndVehicles(Request $request)
    {
        $startDateTime = $request->input('start_date') . ' ' . $request->input('start_time');
        $endDateTime = $request->input('end_date') . ' ' . $request->input('end_time');
        $currentReservationId = $request->input('current_reservation_id');

        \Log::info('Checking availability for: ' . $startDateTime . ' to ' . $endDateTime);
        \Log::info('Current Reservation ID: ' . $currentReservationId);

        $conflictingReservations = Reservations::with(['reservation_vehicles'])
            ->where(function ($query) use ($startDateTime, $endDateTime, $currentReservationId) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where(function ($innerQ) use ($startDateTime, $endDateTime) {
                        $innerQ->whereRaw("CONCAT(rs_date_start, ' ', rs_time_start) < ?", [$endDateTime])
                               ->whereRaw("CONCAT(rs_date_end, ' ', rs_time_end) > ?", [$startDateTime]);
                    });
                })
                ->when($currentReservationId, function ($q) use ($currentReservationId) {
                    return $q->where('reservation_id', '!=', $currentReservationId);
                });
            })
            ->whereIn('rs_status', ['Pending', 'Approved', 'On-Going'])
            ->get();

        \Log::info('Conflicting Reservations: ' . $conflictingReservations->toJson());

        $reservedDriverIds = $conflictingReservations->flatMap(function ($reservation) {
            return $reservation->reservation_vehicles->pluck('driver_id');
        })->unique()->values()->toArray();

        $reservedVehicleIds = $conflictingReservations->flatMap(function ($reservation) {
            return $reservation->reservation_vehicles->pluck('vehicle_id');
        })->unique()->values()->toArray();

        \Log::info('Reserved Driver IDs: ' . implode(', ', $reservedDriverIds));
        \Log::info('Reserved Vehicle IDs: ' . implode(', ', $reservedVehicleIds));

        $drivers = Drivers::select('driver_id as id', DB::raw("CONCAT(dr_fname, ' ', dr_lname) as text"))
            ->addSelect(DB::raw('CASE WHEN driver_id IN (' . implode(',', $reservedDriverIds ?: [0]) . ') THEN 1 ELSE 0 END as is_reserved'))
            ->get();

        $vehicles = Vehicles::select('vehicle_id as id', DB::raw("CONCAT(vh_brand, ' (', vh_plate, ')') as text"))
            ->addSelect(DB::raw('CASE WHEN vehicle_id IN (' . implode(',', $reservedVehicleIds ?: [0]) . ') THEN 1 ELSE 0 END as is_reserved'))
            ->get();

        return response()->json([
            'drivers' => $drivers,
            'vehicles' => $vehicles
        ]);
    }

    public function printReservation($id)
{
    try {
        $reservation = Reservations::with(['requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers'])->findOrFail($id);
        
        $templatePath = storage_path('app/public/Gas_Slip.html');
        $html = file_get_contents($templatePath);

        // Replace placeholders with actual data
        $replacements = [
            '$rs_date_start' => $reservation->rs_date_start,
            '$rs_date_end' => $reservation->rs_date_end,
            '$destination' => $reservation->destination_activity,
            '$purpose' => $reservation->rs_purpose,
            '$vh_brand' => $reservation->reservation_vehicles->first()->vehicles->vh_brand ?? 'N/A',
            '$vh_plate' => $reservation->reservation_vehicles->first()->vehicles->vh_plate ?? 'N/A',
            '$dr_fname' => $reservation->reservation_vehicles->first()->drivers->dr_fname ?? 'N/A',
            '$dr_lname' => $reservation->reservation_vehicles->first()->drivers->dr_lname ?? 'N/A'
        ];

        foreach ($replacements as $placeholder => $value) {
            $html = str_replace($placeholder, $value, $html);
        }

        // Add print script
        $html .= '<script>window.onload = function() { window.print(); }</script>';

        return response($html)->header('Content-Type', 'text/html');

    } catch (\Exception $e) {
        \Log::error('Error in printReservation: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

private function convertTo24HourFormat($time)
{
    return date("H:i", strtotime($time));
}

}



