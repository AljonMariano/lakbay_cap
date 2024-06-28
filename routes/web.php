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
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/compute', [AdminPageController::class, 'compute'])->name('admin.compute');    
    Route::get('/admin/drivers_schedule', [AdminPageController::class, 'drivers_schedule'])->name('admin.drivers_schedule');
    Route::get('/admin/drivers', [AdminPageController::class, 'drivers'])->name('admin.drivers');
    Route::get('/admin/event_calendar', [AdminPageController::class, 'event_calendar'])->name('admin.event_calendar');
    Route::get('/admin/events', [AdminPageController::class, 'events'])->name('admin.events');    
    Route::get('/admin/navigation-menu', [AdminPageController::class, 'navigation_menu'])->name('admin.navigation-menu');
    Route::get('/admin/offices', [AdminPageController::class, 'offices'])->name('admin.offices');
    Route::get('/admin/policy', [AdminPageController::class, 'policy'])->name('admin.policy');
    Route::get('/admin/vehicles', [AdminPageController::class, 'vehicles'])->name('admin.vehicles'); 
    Route::get('/admin/reservations', [AdminPageController::class, 'reservations'])->name('admin.reservations');
    Route::get('/admin/statistics', [AdminPageController::class, 'statistics'])->name('admin.statistics');
    Route::get('/admin/terms', [AdminPageController::class, 'terms'])->name('admin.terms');
    Route::get('/admin/test_select', [AdminPageController::class, 'test_select'])->name('admin.test_select');
    Route::get('/admin/test_word', [AdminPageController::class, 'test_word'])->name('admin.test_word');
    Route::get('/admin/welcome', [AdminPageController::class, 'welcome'])->name('admin.welcome');
    Route::get('/admin/worker', [AdminPageController::class, 'worker'])->name('admin.worker');
    Route::get('/admin/requestor/requestors', [AdminPageController::class, 'requestors'])->name('admin.requestor.requestors');
    Route::get('/admin/navigation-menu', [AdminPageController::class, 'navigation_menu'])->name('admin.navigation-menu');

});
    Route::middleware(['role:user'])->group(function () {
        Route::get('/user/dashboard', [DashboardController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/user/compute', [UserPageController::class, 'compute'])->name('user.compute');        
        Route::get('/user/drivers_schedule', [UserPageController::class, 'drivers_schedule'])->name('user.drivers_schedule');
        Route::get('/user/drivers', [UserPageController::class, 'drivers'])->name('user.drivers');
        Route::get('/user/event_calendar', [UserPageController::class, 'event_calendar'])->name('user.event_calendar');
        Route::get('/user/events', [UserPageController::class, 'events'])->name('user.events');   
        Route::get('/user/vehicles', [UserPageController::class, 'vehicles'])->name('user.vehicles');     
        Route::get('/user/navigation-menu', [UserPageController::class, 'navigation_menu'])->name('user.navigation-menu');
        Route::get('/user/offices', [UserPageController::class, 'offices'])->name('user.offices');
        Route::get('/user/policy', [UserPageController::class, 'policy'])->name('user.policy');
        Route::get('/user/reservations', [UserPageController::class, 'reservations'])->name('user.reservations');
        Route::get('/user/statistics', [UserPageController::class, 'statistics'])->name('user.statistics');
        Route::get('/user/terms', [UserPageController::class, 'terms'])->name('user.terms');
        Route::get('/user/test_select', [UserPageController::class, 'test_select'])->name('user.test_select');
        Route::get('/user/test_word', [UserPageController::class, 'test_word'])->name('user.test_word');
        Route::get('/user/welcome', [UserPageController::class, 'welcome'])->name('user.welcome');
        Route::get('/user/worker', [UserPageController::class, 'worker'])->name('user.worker');
        Route::get('/user/requestor/requestors', [UserPageController::class, 'requestors'])->name('user.requestor.requestors');

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

    // // Requestors
    // Route::get('admin/requestor/requestors', [RequestorsController::class, 'index']);
    // Route::post('store-requestor', [RequestorsController::class, 'store']);
    // Route::post('edit-requestor', [RequestorsController::class, 'edit']);
    // Route::post('delete-requestor', [RequestorsController::class, 'destroy']);

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
    Route::post('/insert-reservation', [ReservationsController::class, 'store']);
    Route::post('/update-reservation', [ReservationsController::class, 'update']);
    Route::get('/edit-reservation/{reservation_id}', [ReservationsController::class, 'edit']);
    Route::get('/cancel-reservation/{reservation_id}', [ReservationsController::class, 'cancel']);
    Route::get('/delete-reservation/{reservation_id}', [ReservationsController::class, 'delete']);

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

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');