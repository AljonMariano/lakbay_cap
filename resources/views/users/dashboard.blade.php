<?php
    $title_page = 'LAKBAY Reservation System';
?>
@include('includes.user_header')
    <div class="row mb-4">
        <div class="col">
            <h6 class="text-uppercase">Quick Counts</h6>
            <div class="card-group mb-3">
                <div class="card bg-danger text-white rounded-0" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $all_reservations['count']; ?></h1>
                        <div class="text-start">Total Reservations</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $ongoing_reservations['count']; ?></h1>
                        <div class="text-start">On-going Travel</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $queued_reservations['count']; ?></h1>
                        <div class="text-start">Queued for travel</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $done_reservations['count']; ?></h1>
                        <div class="text-start">Finished vehicle reservations</div>
                    </div>
                </div>
            </div>
            <div class="card-group mb-3">
                <div class="card rounded-0 bg-danger text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $approved_reservations['count']; ?></h1>
                        <div class="text-start">Approved reservation</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $rejected_reservations['count']; ?></h1>
                        <div class="text-start">Rejected reservation</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $daily_transport_reservations['count']; ?></h1>
                        <div class="text-start">Daily transport request</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $outside_province_reservations['count']; ?></h1>
                        <div class="text-start">Outside province travel</div>
                    </div>
                </div>
            </div>
            <div class="card-group ">
                <div class="card rounded-0 bg-danger text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $events['count']; ?></h1>
                        <div class="text-start">Events</div>
                    </div>
                </div>
                <div class="card bg-success text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $drivers['count']; ?></h1>
                        <div class="text-start">Drivers</div>
                    </div>
                </div>
                <div class="card bg-warning" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $vehicles['count']; ?></h1>
                        <div class="text-start">Vehicles</div>
                    </div>
                </div>
                <div class="card rounded-0 bg-info text-white" style="max-width: 18rem;">
                    <div class="card-body py-0 my-0 d-flex align-items-center">
                        <h1 class="card-title text-start pt-2 mx-4"><?php //echo $requestors['count']; ?></h1>
                        <div class="text-start">Requestors</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include("includes/footer");
