<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Drivers;
use App\Models\Vehicles;
use App\Models\Reservations;
use Illuminate\Support\Facades\Log;
use App\Models\Events;
use App\Models\Requestors;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            return $this->adminDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function adminDashboard()
    {
        $data = [
            'total_reservations' => Reservations::count(),
            'ongoing_travel' => Reservations::where('rs_status', 'On-going')->count(),
            'queued_for_travel' => Reservations::where('rs_status', 'Pending')->count(),
            'finished_reservations' => Reservations::where('rs_status', 'Done')->count(),
            'approved_reservations' => Reservations::where('rs_approval_status', 'Approved')->count(),
            'rejected_reservations' => Reservations::where('rs_approval_status', 'Rejected')->count(),
            'daily_transport_requests' => Reservations::where('rs_travel_type', 'Daily Transport')->count(),
            'outside_province_travel' => Reservations::where('rs_travel_type', 'Outside Province Transport')->count(),
            'events_count' => \App\Models\Events::count(),
            'events_count' => Events::count(),
            'drivers_count' => Drivers::count(),
            'vehicles_count' => Vehicles::count(),
            'requestors_count' => \App\Models\Requestors::count(),
            'requestors_count' => \App\Models\Requestors::count(),
        ];

        return view('admin.dashboard', $data);
    }

    private function userDashboard()
    {
        $user = Auth::user();
        $data = [
            'total_reservations' => Reservations::count(),
            'ongoing_travel' => Reservations::where('rs_status', 'On-going')->count(),
            'queued_for_travel' => Reservations::where('rs_status', 'Pending')->count(),
            'finished_reservations' => Reservations::where('rs_status', 'Done')->count(),
            'approved_reservations' => Reservations::where('rs_approval_status', 'Approved')->count(),
            'rejected_reservations' => Reservations::where('rs_approval_status', 'Rejected')->count(),
            'daily_transport_requests' => Reservations::where('rs_travel_type', 'Daily Transport')->count(),
            'outside_province_travel' => Reservations::where('rs_travel_type', 'Outside Province Transport')->count(),
            'events_count' => \App\Models\Events::count(),
            'events_count' => Events::count(),
            'drivers_count' => Drivers::count(),
            'vehicles_count' => Vehicles::count(),
            'requestors_count' => \App\Models\Requestors::count(),
            'requestors_count' => \App\Models\Requestors::count(),
        ];

        return view('users.dashboard', $data);
    }

    // Keep your existing methods for API routes
    public function getReservationsPerMonth()
    {
        $reservations = DB::table('reservations')
            ->select(DB::raw('YEAR(rs_date_start) as year'), DB::raw('MONTH(rs_date_start) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $values = [];

        foreach ($reservations as $reservation) {
            $labels[] = date('M Y', mktime(0, 0, 0, $reservation->month, 1, $reservation->year));
            $values[] = $reservation->count;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function getTravelTypes()
    {
        $daily = DB::table('reservations')->where('rs_travel_type', 'Daily Transport')->count();
        $outside = DB::table('reservations')->where('rs_travel_type', 'Outside Province Transport')->count();
        $within = DB::table('reservations')->where('rs_travel_type', 'Within Province Transport')->count();

        return response()->json([
            'daily' => $daily,
            'outside' => $outside,
            'within' => $within
        ]);
    }

    public function getAvailableDrivers(Request $request)
    {
        try {
            $date = $request->input('date') ?? now()->toDateString();
            
            $busyDriverIds = DB::table('reservation_vehicles')
                ->join('reservations', 'reservation_vehicles.reservation_id', '=', 'reservations.reservation_id')
                ->whereDate('reservations.rs_date_start', '<=', $date)
                ->whereDate('reservations.rs_date_end', '>=', $date)
                ->whereNotNull('reservation_vehicles.driver_id')
                ->pluck('reservation_vehicles.driver_id')
                ->unique();

            $availableDrivers = Drivers::whereNotIn('driver_id', $busyDriverIds)
                ->get()
                ->map(function ($driver) {
                    return [
                        'name' => trim($driver->dr_fname . ' ' . $driver->dr_mname . ' ' . $driver->dr_lname),
                        'status' => 'Available'
                    ];
                });

            // Log the first few drivers for debugging
            \Log::info('First few available drivers:', $availableDrivers->take(5)->toArray());

            return response()->json($availableDrivers);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableDrivers: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching available drivers'], 500);
        }
    }

    public function getAvailableVehicles(Request $request)
    {
        try {
            $date = $request->input('date') ?? now()->toDateString();
            
            $busyVehicleIds = DB::table('reservation_vehicles')
                ->join('reservations', 'reservation_vehicles.reservation_id', '=', 'reservations.reservation_id')
                ->whereDate('reservations.rs_date_start', '<=', $date)
                ->whereDate('reservations.rs_date_end', '>=', $date)
                ->whereNotNull('reservation_vehicles.vehicle_id')
                ->pluck('reservation_vehicles.vehicle_id')
                ->unique();

            $availableVehicles = Vehicles::whereNotIn('vehicle_id', $busyVehicleIds)
                ->get()
                ->map(function ($vehicle) {
                    return [
                        'name' => $vehicle->vh_plate ?? 'N/A',
                        'status' => 'Available'
                    ];
                })
                ->take(10); // Limit to 10 results

            return response()->json($availableVehicles);
        } catch (\Exception $e) {
            Log::error('Error in getAvailableVehicles: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching available vehicles'], 500);
        }
    }
}