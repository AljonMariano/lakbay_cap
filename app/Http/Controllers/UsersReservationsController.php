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
                        return $reservation->events ? $reservation->events->ev_name : 'N/A';
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
    
            $drivers = Drivers::select('driver_id', 'dr_fname', 'dr_mname', 'dr_lname')
                ->orderBy('dr_fname')
                ->get();
    
            $vehicles = Vehicles::select('vehicle_id', 'vh_plate', 'vh_brand', 'vh_type', 'vh_capacity')
                ->orderBy('vh_brand')
                ->get();
    
            $requestors = Requestors::all();
            $offices = Offices::select('off_id', 'off_acr', 'off_name')->get();
    
            return view('users.reservations')->with(compact('drivers', 'vehicles', 'requestors', 'offices'));
        } catch (\Exception $e) {
            \Log::error('Error in UsersReservationsController@show: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('UsersReservationsController@store method called');
            \Log::info('Request data:', $request->all());
    
            $validator = Validator::make($request->all(), [
                'event_name' => 'required|string|max:255',
                'driver_id' => 'required|array',
                'driver_id.*' => 'exists:drivers,driver_id',
                'vehicle_id' => 'required|array',
                'vehicle_id.*' => 'exists:vehicles,vehicle_id',
                'requestor_id' => 'required|exists:requestors,requestor_id',
                'off_id' => 'required|exists:offices,off_id',
                'rs_voucher' => 'nullable|string|max:255',
                'rs_passengers' => 'required|integer|min:1',
                'rs_travel_type' => 'required|string|in:Outside Province Transport,Within Province Transport,Daily Transport',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            DB::beginTransaction();
    
            $event = Events::create([
                'ev_name' => $request->event_name,
                'ev_venue' => '',
                'ev_date_start' => now(),
                'ev_date_end' => now(),
            ]);
    
            $reservation = new Reservations;
            $reservation->event_id = $event->event_id;
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