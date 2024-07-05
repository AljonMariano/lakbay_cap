<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservations;
use App\Models\Requestors;

class UpdateReservationsOfficeId extends Command
{
    protected $signature = 'reservations:update-office-id';
    protected $description = 'Update reservations with office id from requestors';

    public function handle()
    {
        $reservations = Reservations::whereNull('off_id')->get();
        $this->info("Found " . $reservations->count() . " reservations with null off_id");

        $updated = 0;
        foreach ($reservations as $reservation) {
            $requestor = Requestors::find($reservation->requestor_id);
            if ($requestor && $requestor->off_id) {
                $reservation->off_id = $requestor->off_id;
                $reservation->save();
                $updated++;
            } else {
                $this->warn("Reservation ID " . $reservation->reservation_id . " could not be updated. Requestor ID: " . $reservation->requestor_id . ", Requestor off_id: " . ($requestor ? $requestor->off_id : 'N/A'));
            }
        }

        $this->info("Updated $updated reservations successfully.");
    }
}
