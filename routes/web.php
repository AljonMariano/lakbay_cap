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


Route::get('/',[Controller::class,'redirect']);

//Requestors
Route::get('requestors', [RequestorsController::class, 'index']);
Route::post('store-requestor', [RequestorsController::class, 'store']);
Route::post('edit-requestor', [RequestorsController::class, 'edit']);
Route::post('delete-requestor', [RequestorsController::class, 'destroy']);
//End-Requestor
//Driver Section
Route::post('/insert-driver',[DriversController::class,'store']);
Route::get('/drivers', [DriversController::class, 'show'])->name('drivers.show');
Route::get('/delete-driver/{driver_id}', [DriversController::class, 'delete']);
Route::get('/edit-driver/{driver_id}', [DriversController::class, 'edit']);
Route::post('/update-driver', [DriversController::class, 'update']);
Route::get('/driver-word', [DriversController::class, 'driver_word']);
Route::get('/driver-excel', [DriversController::class, 'driver_excel']);
Route::get('/driver-pdf', [DriversController::class, 'driver_pdf']);
//End
//Vehicles Section
Route::post('/insert-vehicle',[VehiclesController::class,'store']);
Route::get('/vehicles', [VehiclesController::class, 'show'])->name('vehicles.show');
Route::get('/delete-vehicle/{vehicle_id}', [VehiclesController::class, 'delete']);
Route::get('/vehicle-word', [VehiclesController::class, 'vehicles_word']);
Route::get('/vehicle-excel', [VehiclesController::class, 'vehicles_excel']);
Route::get('/vehicle-pdf', [VehiclesController::class, 'vehicles_pdf']);
Route::get('/edit-vehicle/{vehicle_id}', [VehiclesController::class, 'edit']);
Route::post('/update-vehicle', [VehiclesController::class, 'update']);

//End
//Event Section
Route::post('/insert-event', [EventsController::class, 'store']);
Route::get('/events',[EventsController::class,'show'])->name('events.show');
Route::get('/edit-event/{event_id}', [EventsController::class, 'edit']);
Route::post('/update-event', [EventsController::class, 'update']);
Route::get('/delete-event/{event_id}', [EventsController::class, 'delete']);
Route::get('/events-word', [EventsController::class, 'events_word']);
Route::get('/events-excel', [EventsController::class, 'events_excel']);
Route::get('/events-pdf', [EventsController::class, 'events_pdf']);

//End
//Offices Section
Route::get('/offices', [OfficesController::class, 'show'])->name('offices.show');
Route::post('/insert-office', [OfficesController::class, 'store']);
Route::get('/delete-office/{off_id}', [OfficesController::class, 'delete']);
Route::get('/edit-office/{off_id}', [OfficesController::class, 'edit']);
Route::post('/update-office', [OfficesController::class, 'update']);
Route::get('/offices-word', [OfficesController::class, 'offices_word']);
Route::get('/offices-excel', [OfficesController::class, 'offices_excel']);
Route::get('/offices-pdf', [OfficesController::class, 'offices_pdf']);
//End
//Reservation Section
Route::get('/reservations', [ReservationsController::class,'show'])->name('reservations.show');
Route::get('/event-calendar', [ReservationsController::class, 'event_calendar']);
Route::get('/driver-schedules', [ReservationsController::class, 'drivers_schedules']);
Route::get('/get-events', [ReservationsController::class,'events'])->name('reservations.getEvents');
Route::get('/get-edit-events', [ReservationsController::class, 'events_edit'])->name('reservations.getEditEvents');
Route::get('/reservations-archive', [ReservationsController::class, 'reservations_archive']);
Route::get('/reservations-word', [ReservationsController::class, 'reservations_word']);
Route::get('/reservations-excel', [ReservationsController::class, 'reservations_excel']);
Route::get('/reservations-pdf', [ReservationsController::class, 'reservations_pdf']);
Route::post('/insert-reservation', [ReservationsController::class, 'store']);
Route::post('/update-reservation', [ReservationsController::class, 'update']);
Route::get('/edit-reservation/{reservation_id}', [ReservationsController::class, 'edit']);
Route::get('/cancel-reservation/{reservation_id}', [ReservationsController::class, 'cancel']);
Route::get('/delete-reservation/{reservation_id}', [ReservationsController::class, 'delete']);
// Route::get('/get-events', [ReservationsController::class, 'events']);
//End

//Test Section
Route::get('/test-select', [ReservationsController::class, 'test_select'])->name('reservations.testSelect');
Route::get('/test-return', [ReservationsController::class, 'test_return'])->name('reservations.testReturn');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
