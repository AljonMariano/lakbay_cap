<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drivers;
use App\Models\Offices;
use App\Models\Vehicles;
use App\Models\Reservations;
use App\Models\ReservationVehicle;
use App\Models\Requestors;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UsersReservationsController extends Controller
{
    public function index()
    {
        $offices = Offices::all();
        $requestors = Requestors::all();
        return view('users.reservations', compact('offices', 'requestors'));
    }

    public function getData()
    {
        $reservations = Reservations::with(['requestors', 'office', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers'])
            ->where('rs_status', '!=', 'Deleted')
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
                    $vehicle = $rv->vehicles;
                    return $vehicle ? "{$vehicle->vh_brand} - {$vehicle->vh_type} ({$vehicle->vh_plate})" : 'N/A';
                })->filter()->implode(', ') ?: 'N/A';
            })
            ->addColumn('driver_name', function ($reservation) {
                return $reservation->reservation_vehicles->map(function ($rv) {
                    return $rv->drivers ? ($rv->drivers->dr_fname . ' ' . $rv->drivers->dr_lname) : 'N/A';
                })->filter()->implode(', ') ?: 'N/A';
            })
            ->addColumn('action', function ($reservation) {
                
                return '<button class="btn btn-sm btn-primary view-btn" data-id="'.$reservation->reservation_id.'">View</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
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

        $vehicles = Vehicles::select('vehicle_id as id', DB::raw("CONCAT(vh_brand, ' (', vh_plate, ')') as text"), 'vh_status')
            ->get()
            ->map(function ($vehicle) use ($reservedVehicleIds) {
                $vehicle->is_reserved = in_array($vehicle->id, $reservedVehicleIds) || $vehicle->vh_status !== 'Available' ? 1 : 0;
                return $vehicle;
            });

        return response()->json([
            'drivers' => $drivers,
            'vehicles' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Reservation store request:', $request->all());

        try {
            DB::beginTransaction();

            $reservationData = $request->all();

            // Convert time to 24-hour format
            $reservationData['rs_time_start'] = $this->convertTo24HourFormat($request->rs_time_start);
            $reservationData['rs_time_end'] = $this->convertTo24HourFormat($request->rs_time_end);

            // Set default status values
            $reservationData['rs_approval_status'] = 'Pending';
            $reservationData['rs_status'] = 'Pending';

            Log::info('Reservation data after conversion:', $reservationData);

            $reservation = Reservations::create($reservationData);

            Log::info('New reservation created:', $reservation->toArray());

            // Handle driver and vehicle assignments
            if ($request->has('driver_id') && $request->has('vehicle_id')) {
                $driverIds = $request->input('driver_id');
                $vehicleIds = $request->input('vehicle_id');
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

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'reservation' => $reservation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating reservation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    private function convertTo24HourFormat($time)
    {
        if (empty($time)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('h:i A', $time)->format('H:i:s');
        } catch (\Exception $e) {
            Log::error('Error converting time format: ' . $e->getMessage());
            return null;
        }
    }
}



