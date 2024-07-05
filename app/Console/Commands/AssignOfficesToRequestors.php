<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Requestors;
use App\Models\Offices;

class AssignOfficesToRequestors extends Command
{
    protected $signature = 'requestors:assign-offices';
    protected $description = 'Assign offices to requestors';

    public function handle()
    {
        $requestors = Requestors::whereNull('off_id')->get();
        $offices = Offices::all();

        if ($offices->isEmpty()) {
            $this->error('No offices found. Please add offices first.');
            return;
        }

        foreach ($requestors as $requestor) {
            // Randomly assign an office. Replace this logic with your business rules.
            $office = $offices->random();
            $requestor->off_id = $office->off_id;
            $requestor->save();

            $this->info("Assigned office {$office->off_name} to requestor {$requestor->rq_full_name}");
        }

        $this->info('All requestors have been assigned an office.');
    }
}
