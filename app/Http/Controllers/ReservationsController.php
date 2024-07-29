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
                if ($reservation->is_outsider) {
                    return $reservation->outside_requestor ?? 'N/A';
                } else {
                    return $reservation->requestors->rq_full_name ?? 'N/A';
                }
            })
            ->addColumn('office', function ($reservation) {
                if ($reservation->is_outsider) {
                    return $reservation->outside_office ?? 'N/A';
                } else {
                    return $reservation->office->off_name ?? 'N/A';
                }
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
        \Log::info('Update method called', ['id' => $id, 'method' => $request->method()]);
        
        try {
            $reservation = Reservations::findOrFail($id);
            
            // Update reservation fields
            $reservation->update($request->except(['driver_id', 'vehicle_id']));

            // Update drivers and vehicles
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $reservation->reservation_vehicles()->delete(); // Remove old associations
                foreach ($request->driver_id as $index => $driverId) {
                    $reservation->reservation_vehicles()->create([
                        'driver_id' => $driverId,
                        'vehicle_id' => $request->vehicle_id[$index]
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Reservation updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating reservation: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        \Log::info('Reservation store request:', $request->all());

        $isOutsider = $request->boolean('is_outsider');

        $rules = [
            'rs_passengers' => 'required|integer',
            'rs_travel_type' => 'required|string',
            'rs_purpose' => 'required|string',
            'rs_from' => 'required|string',
            'rs_date_start' => 'required|date',
            'rs_time_start' => 'required',
            'rs_date_end' => 'required|date',
            'rs_time_end' => 'required',
            'destination_activity' => 'required|string',
            'is_outsider' => 'required|boolean',
            'off_id' => $isOutsider ? 'nullable' : 'required|exists:offices,off_id',
            'requestor_id' => $isOutsider ? 'nullable' : 'required|exists:requestors,requestor_id',
            'outside_office' => $isOutsider ? 'required|string' : 'nullable',
            'outside_requestor' => $isOutsider ? 'required|string' : 'nullable',
        ];

        try {
            $data = $request->validate($rules);

            $data['rs_approval_status'] = 'Pending';
            $data['rs_status'] = 'Pending';

            if ($isOutsider) {
                $data['off_id'] = null;
                $data['requestor_id'] = null;
            } else {
                $data['outside_office'] = null;
                $data['outside_requestor'] = null;
            }

            DB::beginTransaction();

            $reservation = Reservations::create($data);

            // Handle drivers and vehicles
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $driverIds = $request->input('driver_id');
                $vehicleIds = $request->input('vehicle_id');

                foreach ($driverIds as $index => $driverId) {
                    ReservationVehicle::create([
                        'reservation_id' => $reservation->reservation_id,
                        'driver_id' => $driverId,
                        'vehicle_id' => $vehicleIds[$index],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Reservation created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation failed:', $e->errors());
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating reservation:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error creating reservation: ' . $e->getMessage()], 500);
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
                    'name' => $vehicle->vh_brand . ' (' . $vehicle->vh_plate . ') - ' . $vehicle->vh_type,
                    'reserved' => in_array($vehicle->vehicle_id, $unavailableVehicleIds)
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
}

