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
use Carbon\Carbon;

class UsersReservationsController extends Controller
{
    public function show(Request $request)
    {
        try {
            \Log::info("UsersReservationsController@show called");

            if ($request->ajax()) {
                \Log::info("Processing AJAX request");
                $reservations = Reservations::with(['events', 'requestors', 'reservation_vehicles.vehicles', 'reservation_vehicles.drivers', 'office'])
                    ->select('reservations.*')
                    ->get();

                \Log::info("Fetched " . $reservations->count() . " reservations");

                if ($reservations->isEmpty()) {
                    \Log::info("No reservations found");
                } else {
                    \Log::info("First reservation: " . json_encode($reservations->first()));
                }

                return DataTables::of($reservations)
                    ->addColumn('ev_name', function ($reservation) {
                        return $reservation->events ? $reservation->events->ev_name : 'N/A';
                    })
                    ->addColumn('vehicles', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            return $rv->vehicles ? $rv->vehicles->vh_plate : 'N/A';
                        })->implode(', ');
                    })
                    ->addColumn('drivers', function ($reservation) {
                        return $reservation->reservation_vehicles->map(function ($rv) {
                            return $rv->drivers ? $rv->drivers->dr_fname . ' ' . $rv->drivers->dr_lname : 'N/A';
                        })->implode(', ');
                    })
                    ->addColumn('requestor', function ($reservation) {
                        return $reservation->requestors ? $reservation->requestors->rq_full_name : 'N/A';
                    })
                    ->rawColumns(['vehicles', 'drivers'])
                    ->make(true);
            }

            $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
                ->orderBy('dr_fname')
                ->get();

            $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
                ->orderBy('vh_brand')
                ->get();

            $offices = Offices::select('off_id', 'off_acr', 'off_name')->get();

            // Add this line to fetch requestors
            $requestors = Requestors::all();

            return view('users.reservations', compact('drivers', 'vehicles', 'offices', 'requestors'));
        } catch (\Exception $e) {
            \Log::error('Error in UsersReservationsController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Incoming reservation request data:', $request->all());

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'event_name' => 'required|string|max:255',
                'rs_from' => 'required|string|max:255',
                'rs_date_start' => 'required|date',
                'rs_time_start' => 'required',
                'rs_date_end' => 'required|date|after_or_equal:rs_date_start',
                'rs_time_end' => 'required',
                'off_id' => 'required|exists:offices,off_id',
                'rs_passengers' => 'required|integer|min:1',
                'rs_travel_type' => 'required|string',
                'rs_voucher' => 'required|string|max:255',
                'driver_id' => 'required|array',
                'driver_id.*' => 'exists:drivers,driver_id',
                'vehicle_id' => 'required|array',
                'vehicle_id.*' => 'exists:vehicles,vehicle_id',
                'requestor_id' => 'required|exists:requestors,requestor_id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            // Use the selected requestor_id from the form instead of the authenticated user
            $requestorId = $request->input('requestor_id');
            
            $eventData = [
                'ev_name' => $request->input('event_name'),
                'ev_venue' => $request->input('rs_from'),
                'ev_date_start' => $request->input('rs_date_start'),
                'ev_date_end' => $request->input('rs_date_end'),
                'ev_time_start' => $request->input('rs_time_start'),
                'ev_time_end' => $request->input('rs_time_end'),
            ];

            \Log::info('Event data to be saved:', $eventData);
            $event = Events::create($eventData);

            $reservationData = [
                'event_id' => $event->event_id,
                'requestor_id' => $requestorId, // Use the selected requestor_id
                'off_id' => $request->input('off_id'),
                'rs_from' => $request->input('rs_from'),
                'rs_date_start' => $request->input('rs_date_start'),
                'rs_time_start' => $request->input('rs_time_start'),
                'rs_date_end' => $request->input('rs_date_end'),
                'rs_time_end' => $request->input('rs_time_end'),
                'rs_passengers' => $request->input('rs_passengers'),
                'rs_travel_type' => $request->input('rs_travel_type'),
                'rs_voucher' => $request->input('rs_voucher'),
                'rs_approval_status' => 'Pending',
                'rs_status' => 'Active',
            ];

            \Log::info('Reservation data to be saved:', $reservationData);
            $reservation = Reservations::create($reservationData);

            foreach ($request->driver_id as $index => $driverId) {
                ReservationVehicle::create([
                    'reservation_id' => $reservation->reservation_id,
                    'driver_id' => $driverId,
                    'vehicle_id' => $request->vehicle_id[$index] ?? null,
                ]);
            }

            DB::commit();

            return response()->json(['success' => 'Reservation created successfully', 'reservation' => $reservation]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in UsersReservationsController@store: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while creating the reservation: ' . $e->getMessage()], 500);
        }
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

            return response()->json([
                'drivers' => $drivers->map(function($driver) {
                    return ['id' => $driver->driver_id, 'name' => $driver->dr_fname . ' ' . $driver->dr_lname];
                }),
                'vehicles' => $vehicles->map(function($vehicle) {
                    return ['id' => $vehicle->vehicle_id, 'name' => $vehicle->vh_brand . ' - ' . $vehicle->vh_plate];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching drivers and vehicles: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch drivers and vehicles'], 500);
        }
    }
}