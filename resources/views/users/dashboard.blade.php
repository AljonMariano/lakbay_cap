<?php
    $title_page = 'LAKBAY Reservation System';
?>
@include('includes.user_header')
<div class="container">
    <h2>Dashboard</h2>
    
    <div class="row mb-4">
        <div class="col">
            <h6 class="text-uppercase">Quick Counts</h6>
            <div class="card-group mb-3">
                <div class="card bg-danger text-white rounded-0" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $total_reservations }}</h1>
                        <div class="text-start">Total Reservations</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $ongoing_travel }}</h1>
                        <div class="text-start">On-going Travel</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $queued_for_travel }}</h1>
                        <div class="text-start">Queued for travel</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $finished_reservations }}</h1>
                        <div class="text-start">Finished vehicle reservations</div>
                    </div>
                </div>
            </div>
            <div class="card-group mb-3">
                <div class="card rounded-0 bg-danger text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $approved_reservations }}</h1>
                        <div class="text-start">Approved reservation</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $rejected_reservations }}</h1>
                        <div class="text-start">Rejected reservation</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $daily_transport_requests }}</h1>
                        <div class="text-start">Daily transport request</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $outside_province_travel }}</h1>
                        <div class="text-start">Outside province travel</div>
                    </div>
                </div>
            </div>
            <div class="card-group ">
                <div class="card rounded-0 bg-danger text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $events_count }}</h1>
                        <div class="text-start">Events</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $drivers_count }}</h1>
                        <div class="text-start">Drivers</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $vehicles_count }}</h1>
                        <div class="text-start">Vehicles</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4">{{ $requestors_count }}</h1>
                        <div class="text-start">Requestors</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-uppercase">Reservations per Month</h6>
            <div id="reservationsChartContainer" style="position: relative; height: 300px; overflow-x: auto;">
                <canvas id="reservationsChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <h6 class="text-uppercase">Travel Types</h6>
            <canvas id="travelTypesChart"></canvas>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-uppercase">Available Drivers</h6>
            <input type="date" id="driverDate" onchange="fetchAvailableDrivers()">
            <div id="availableDriversTable"></div>
        </div>
        <div class="col-md-6">
            <h6 class="text-uppercase">Available Vehicles</h6>
            <input type="date" id="vehicleDate" onchange="fetchAvailableVehicles()">
            <div id="availableVehiclesTable"></div>
        </div>
    </div>
</div>

<style>
    #availableDriversTable, #availableVehiclesTable {
        max-height: 300px;
        overflow: hidden;
    }
    .table-body-scroll {
        max-height: 250px;
        overflow-y: auto;
    }
    #availableDriversTable table, #availableVehiclesTable table {
        width: 100%;
        table-layout: fixed;
    }
    #availableDriversTable th, #availableVehiclesTable th,
    #availableDriversTable td, #availableVehiclesTable td {
        width: 50%;
    }
</style>

@include("includes/footer");

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/dashboard-charts.js') }}"></script>