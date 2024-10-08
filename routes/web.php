<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\OfficesController;
use App\Http\Controllers\RequestorsController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataTableAjaxCRUDController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\UserPageController;
use App\Http\Controllers\UsersReservationsController;
use App\Http\Controllers\AdminProfileController;
use Illuminate\Support\Facades\Log;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/', [Controller::class, 'redirect']);


// AUTH route
Route::prefix('admin')->middleware(['role:admin', 'auth'])->group(function () {
    Route::post('/reservations/{id}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::get('/reservations/{id}/edit', [ReservationsController::class, 'edit'])->name('reservations.edit');
    Route::post('/reservations/{id}/done', [ReservationsController::class, 'markAsDone'])->name('reservations.done');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', function () {
        return view('admin.profile.show');
    })->name('admin.profile.show');

    


    // Offices Section
    Route::get('/offices', [OfficesController::class, 'show'])->name('offices.show');
    Route::post('/insert-office', [OfficesController::class, 'store'])->name('offices.store');
    Route::get('/delete-office/{off_id}', [OfficesController::class, 'delete']);
    Route::get('/edit-office/{off_id}', [OfficesController::class, 'edit']);
    Route::post('/update-office', [OfficesController::class, 'update']);
    Route::get('/offices-word', [OfficesController::class, 'offices_word']);
    Route::get('/offices-excel', [OfficesController::class, 'offices_excel']);
    Route::get('/offices-pdf', [OfficesController::class, 'offices_pdf']);

    // Vehicles Section
    Route::get('/vehicles', [VehicleController::class, 'show'])->name('vehicles.index');
    Route::get('/vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::post('/insert-vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/edit-vehicle/{id}', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::post('/update-vehicle', [VehicleController::class, 'update'])->name('update-vehicle');
    Route::get('/delete-vehicle/{vehicle_id}', [VehicleController::class, 'delete']);
    Route::get('/vehicle-word', [VehicleController::class, 'vehicles_word']);
    Route::get('/vehicle-excel', [VehicleController::class, 'vehicles_excel']);
    Route::get('/vehicle-pdf', [VehicleController::class, 'vehicles_pdf']);

    // // Requestors
    Route::get('/requestors', [RequestorsController::class, 'index'])->name('requestor.requestors');
    Route::post('/store-requestor', [RequestorsController::class, 'store'])->name('requestor.requestors');
    Route::post('/edit-requestor', [RequestorsController::class, 'edit'])->name('requestor.requestors');
    Route::post('/delete-requestor', [RequestorsController::class, 'destroy'])->name('requestor.requestors');

    // Driver Section
    Route::post('/insert-driver', [DriversController::class, 'store']);
    Route::get('/drivers', [DriversController::class, 'show'])->name('drivers.show');
    Route::get('/delete-driver/{driver_id}', [DriversController::class, 'delete']);
    Route::get('/edit-driver/{driver_id}', [DriversController::class, 'edit']);
    Route::post('/update-driver', [DriversController::class, 'update']);
    Route::get('/driver-word', [DriversController::class, 'driver_word']);
    Route::get('/driver-excel', [DriversController::class, 'driver_excel']);
    Route::get('/driver-pdf', [DriversController::class, 'driver_pdf']);

    // Event Section
    Route::post('/insert-event', [EventsController::class, 'store']);
    Route::get('/events', [EventsController::class, 'show'])->name('events.show');
    Route::get('/edit-event/{event_id}', [EventsController::class, 'edit']);
    Route::post('/update-event', [EventsController::class, 'update']);
    Route::get('/delete-event/{event_id}', [EventsController::class, 'delete']);
    Route::get('/events-word', [EventsController::class, 'events_word']);
    Route::get('/events-excel', [EventsController::class, 'events_excel']);
    Route::get('/events-pdf', [EventsController::class, 'events_pdf']);



    // Reservation Section
    Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/data', [ReservationsController::class, 'getData'])->name('reservations.data');
    Route::get('/reservations/{id}', [ReservationsController::class, 'show'])->name('reservations.show');
    Route::post('/reservations', [ReservationsController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{id}/edit', [ReservationsController::class, 'edit'])->name('reservations.edit');
    Route::post('/reservations/{id}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{id}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');
    Route::post('/reservations/{id}/done', [ReservationsController::class, 'markAsDone'])->name('reservations.done');
    Route::get('/event-calendar', [ReservationsController::class, 'eventCalendar'])->name('reservations.calendar');
    Route::get('/driver-schedules', [ReservationsController::class, 'driverSchedules'])->name('reservations.driver-schedules');
    Route::get('/get-events', [ReservationsController::class, 'getEvents'])->name('reservations.getEvents');
    Route::get('/get-edit-events', [ReservationsController::class, 'getEditEvents'])->name('reservations.getEditEvents');
    Route::get('/reservations-archive', [ReservationsController::class, 'archive'])->name('reservations.archive');
    Route::get('/reservations-word', [ReservationsController::class, 'exportWord'])->name('reservations.word');
    Route::get('/reservations-excel', [ReservationsController::class, 'exportExcel'])->name('reservations.excel');
    Route::get('/reservations-pdf', [ReservationsController::class, 'exportPdf'])->name('reservations.pdf');
    Route::get('/get-drivers', [ReservationsController::class, 'getDrivers'])->name('get.drivers');
    Route::get('/get-vehicles', [ReservationsController::class, 'getVehicles'])->name('get.vehicles');
    Route::post('/admin/reservations/{id}/approve', [ReservationsController::class, 'approve'])->name('reservations.approve');
    Route::post('/admin/reservations/{id}/reject', [ReservationsController::class, 'reject'])->name('reservations.reject');
    Route::post('/admin/reservations/{id}/cancel', [ReservationsController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{id}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::get('/get-drivers-vehicles', [ReservationsController::class, 'getDriversAndVehicles'])->name('get.drivers.vehicles');

    Route::get('/reservations/{id}/print', [ReservationsController::class, 'printReservation'])->name('reservations.print');







    // Test Section
    Route::get('/test-select', [ReservationsController::class, 'test_select'])->name('reservations.testSelect');
    Route::get('/test-return', [ReservationsController::class, 'test_return'])->name('reservations.testReturn');

});


Route::middleware(['auth'])->prefix('users')->group(function () {
  

        Route::get('/user/dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
       

        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');


        // Offices Section
        Route::get('/offices', [OfficesController::class, 'index'])->name('admin.offices.index');
        Route::post('/insert-office', [OfficesController::class, 'store'])->name('offices.store');
        Route::get('/delete-office/{off_id}', [OfficesController::class, 'delete']);
        Route::get('/edit-office/{off_id}', [OfficesController::class, 'edit']);
        Route::post('/update-office', [OfficesController::class, 'update']);
        Route::get('/offices-word', [OfficesController::class, 'offices_word']);
        Route::get('/offices-excel', [OfficesController::class, 'offices_excel']);
        Route::get('/offices-pdf', [OfficesController::class, 'offices_pdf']);
    
        // Vehicles Section
        Route::get('/vehicles', [VehicleController::class, 'show'])->name('vehicles.index');
        Route::get('/vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show');
        Route::post('/insert-vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/edit-vehicle/{id}', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::post('/update-vehicle', [VehicleController::class, 'update'])->name('update-vehicle');
        Route::get('/delete-vehicle/{vehicle_id}', [VehicleController::class, 'delete']);
        Route::get('/vehicle-word', [VehicleController::class, 'vehicles_word']);
        Route::get('/vehicle-excel', [VehicleController::class, 'vehicles_excel']);
        Route::get('/vehicle-pdf', [VehicleController::class, 'vehicles_pdf']);
    
        // // Requestors
        Route::get('/requestors', [RequestorsController::class, 'index'])->name('requestor.requestors');

    
        // Driver Section
        Route::post('/insert-driver', [DriversController::class, 'store']);
        Route::get('/drivers', [DriversController::class, 'show'])->name('drivers.show');
        Route::get('/delete-driver/{driver_id}', [DriversController::class, 'delete']);
        Route::get('/edit-driver/{driver_id}', [DriversController::class, 'edit']);
        Route::post('/update-driver', [DriversController::class, 'update']);
        Route::get('/driver-word', [DriversController::class, 'driver_word']);
        Route::get('/driver-excel', [DriversController::class, 'driver_excel']);
        Route::get('/driver-pdf', [DriversController::class, 'driver_pdf']);
    
        // Event Section
        Route::post('/insert-event', [EventsController::class, 'store']);
        Route::get('/events', [EventsController::class, 'show'])->name('events.show');
        Route::get('/edit-event/{event_id}', [EventsController::class, 'edit']);
        Route::post('/update-event', [EventsController::class, 'update']);
        Route::get('/delete-event/{event_id}', [EventsController::class, 'delete']);
        Route::get('/events-word', [EventsController::class, 'events_word']);
        Route::get('/events-excel', [EventsController::class, 'events_excel']);
        Route::get('/events-pdf', [EventsController::class, 'events_pdf']);
    
    
    
        // Reservation Section
        Route::get('/reservations', [UsersReservationsController::class, 'index'])->name('users.reservations.show');
        Route::get('/reservations/data', [UsersReservationsController::class, 'getData'])->name('users.reservations.getData');
        Route::get('/event-calendar', [UsersReservationsController::class, 'users.event_calendar']);
        Route::get('/driver-schedules', [UsersReservationsController::class, 'users.drivers_schedules']);
        Route::get('/get-events', [UsersReservationsController::class, 'events'])->name('users.reservations.getEvents');
        Route::get('/get-edit-events', [UsersReservationsController::class, 'events_edit'])->name('users.reservations.getEditEvents');
        Route::get('/get-drivers-vehicles', [UsersReservationsController::class, 'getDriversAndVehicles'])->name('users.reservations.getDriversAndVehicles');
        Route::post('/insert-reservation', [UsersReservationsController::class, 'stores']);
        Route::get('/users/reservations/get-drivers-vehicles', [UsersReservationsController::class, 'getDriversAndVehicles'])->name('users.reservations.getDriversAndVehicles');

        Route::post('/user/reservations', [UsersReservationsController::class, 'store'])->name('users.reservations.store');
        Route::get('/users/reservations/data', [UsersReservationsController::class, 'getData'])->name('users.reservations.getData');
        Route::get('/users/reservations/getDriversAndVehicles', [UsersReservationsController::class, 'getDriversAndVehicles'])->name('users.reservations.getDriversAndVehicles');
        Route::post('/users/reservations', [UsersReservationsController::class, 'store'])->name('users.reservations.store');


        
        // Test Section
        Route::get('/test-select', [UsersReservationsController::class, 'test_select'])->name('user.reservations.testSelect');
        Route::get('/test-return', [UsersReservationsController::class, 'test_return'])->name('user.reservations.testReturn');


    });


    Route::middleware(['auth'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Offices Section
    Route::get('/offices', [OfficesController::class, 'show'])->name('offices.show');
    Route::post('/insert-office', [OfficesController::class, 'store'])->name('offices.store');
    Route::get('/delete-office/{off_id}', [OfficesController::class, 'delete']);
    Route::get('/edit-office/{off_id}', [OfficesController::class, 'edit']);
    Route::post('/update-office', [OfficesController::class, 'update']);
    Route::get('/offices-word', [OfficesController::class, 'offices_word']);
    Route::get('/offices-excel', [OfficesController::class, 'offices_excel']);
    Route::get('/offices-pdf', [OfficesController::class, 'offices_pdf']);

    // Vehicles Section
    Route::get('/vehicles', [VehicleController::class, 'show'])->name('vehicles.index');
    Route::get('/vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show'); 
    Route::post('/insert-vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/edit-vehicle/{id}', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::post('/update-vehicle', [VehicleController::class, 'update'])->name('update-vehicle');
    Route::get('/delete-vehicle/{vehicle_id}', [VehicleController::class, 'delete']);
    Route::get('/vehicle-word', [VehicleController::class, 'vehicles_word']);
    Route::get('/vehicle-excel', [VehicleController::class, 'vehicles_excel']);
    Route::get('/vehicle-pdf', [VehicleController::class, 'vehicles_pdf']);

    // Requestors
    Route::get('/requestor/requestors', [RequestorsController::class, 'index']);
    Route::post('store-requestor', [RequestorsController::class, 'store']);
    Route::post('edit-requestor', [RequestorsController::class, 'edit']);
    Route::post('delete-requestor', [RequestorsController::class, 'destroy']);

    // Driver Section
    Route::post('/insert-driver', [DriversController::class, 'store']);
    Route::get('/drivers', [DriversController::class, 'show'])->name('drivers.show');
    Route::get('/delete-driver/{driver_id}', [DriversController::class, 'delete']);
    Route::get('/edit-driver/{driver_id}', [DriversController::class, 'edit']);
    Route::post('/update-driver', [DriversController::class, 'update']);
    Route::get('/driver-word', [DriversController::class, 'driver_word']);
    Route::get('/driver-excel', [DriversController::class, 'driver_excel']);
    Route::get('/driver-pdf', [DriversController::class, 'driver_pdf']);

    // Event Section
    Route::post('/insert-event', [EventsController::class, 'store']);
    Route::get('/events', [EventsController::class, 'show'])->name('events.show');
    Route::get('/edit-event/{event_id}', [EventsController::class, 'edit']);
    Route::post('/update-event', [EventsController::class, 'update']);
    Route::get('/delete-event/{event_id}', [EventsController::class, 'delete']);
    Route::get('/events-word', [EventsController::class, 'events_word']);
    Route::get('/events-excel', [EventsController::class, 'events_excel']);
    Route::get('/events-pdf', [EventsController::class, 'events_pdf']);



    // Reservation Section
    Route::get('/reservations', [ReservationsController::class, 'show'])->name('reservations.show');
    Route::get('/event-calendar', [ReservationsController::class, 'event_calendar']);
    Route::get('/driver-schedules', [ReservationsController::class, 'drivers_schedules']);
    Route::get('/get-events', [ReservationsController::class, 'events'])->name('reservations.getEvents');
    Route::get('/get-edit-events', [ReservationsController::class, 'events_edit'])->name('reservations.getEditEvents');
    Route::get('/reservations-archive', [ReservationsController::class, 'reservations_archive']);
    Route::get('/reservations-word', [ReservationsController::class, 'reservations_word']);
    Route::get('/reservations-excel', [ReservationsController::class, 'reservations_excel']);
    Route::get('/reservations-pdf', [ReservationsController::class, 'reservations_pdf']);
    Route::post('/insert-reservation', [UsersReservationsController::class, 'store']);
    Route::post('/update-reservation', [ReservationsController::class, 'update']);
    Route::get('/edit-reservation/{reservation_id}', [ReservationsController::class, 'edit']);
    Route::get('/cancel-reservation/{reservation_id}', [ReservationsController::class, 'cancel']);
    Route::get('/delete-reservation/{reservation_id}', [ReservationsController::class, 'delete']);
    Route::get('/reservations', [UsersReservationsController::class, 'show'])->name('users.reservations.show');
    Route::post('/reservations', [UsersReservationsController::class, 'store'])->name('users.reservations.store');
    Route::get('/get-drivers-vehicles', [ReservationsController::class, 'getDriversAndVehicles'])->name('get.drivers.vehicles');

    // Test Section
    Route::get('/test-select', [ReservationsController::class, 'test_select'])->name('reservations.testSelect');
    Route::get('/test-return', [ReservationsController::class, 'test_return'])->name('reservations.testReturn');



});







Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

//Route::post('/login', [AuthController::class, 'login'])->name('login');




Route::get('/check-all-reservations', function() {
    $reservations = \App\Models\Reservations::with(['requestors', 'events'])
        ->select('reservations.*')
        ->get();

    $summary = $reservations->groupBy('requestor_id')
        ->map(function ($group) {
            return [
                'requestor_id' => $group->first()->requestor_id,
                'requestor_name' => $group->first()->requestors->rq_full_name ?? 'N/A',
                'count' => $group->count()
            ];
        });

    dd([
        'total_reservations' => $reservations->count(),
        'reservations_by_requestor' => $summary->toArray(),
        'sample_reservation' => $reservations->first()->toArray()
    ]);
});

Route::get('/js/admin/reservations.js', function () {
    try {
        return response()->file(resource_path('views/admin/reservations.js'));
    } catch (\Exception $e) {
        Log::error('Error serving reservations.js: ' . $e->getMessage());
        abort(404);
    }
})->name('admin.reservations.js');
Route::get('/test-log', function() {
    \Log::info('Test log entry');
    return 'Log test complete';

    
});
Route::get('js/users/reservations.js', function () {
    return response()->file(resource_path('views/users/reservations.js'), [
        'Content-Type' => 'application/javascript'
    ]);
});

// Route::any('{any}', function($any) {
//     \Log::info('Catch-all route hit: ' . $any);
//     return 'Catch-all route: ' . $any;
// })->where('any', '.*');

Route::get('/admin/test-print', function() {
    return response()->json(['message' => 'Test print route works']);
})->middleware(['role:admin', 'auth']);

// API Routes
Route::get('/api/reservations-per-month', [DashboardController::class, 'getReservationsPerMonth']);
Route::get('/api/travel-types', [DashboardController::class, 'getTravelTypes']);
Route::get('/api/available-drivers', [DashboardController::class, 'getAvailableDrivers']);
Route::get('/api/available-vehicles', [DashboardController::class, 'getAvailableVehicles']);