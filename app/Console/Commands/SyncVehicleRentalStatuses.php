<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class SyncVehicleRentalStatuses extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vehicles:sync-rental-statuses';

    /**
     * The console command description.
     */
    protected $description = 'Sync vehicle rented/available status based on confirmed paid bookings that have started';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $vehicles = Vehicle::query()
            ->where('status', '!=', 'maintenance')
            ->get();

        $updated = 0;
        $rented = 0;
        $available = 0;

        foreach ($vehicles as $vehicle) {
            if (! $vehicle->syncRentalStatus()) {
                continue;
            }

            $updated++;

            if ($vehicle->status === 'rented') {
                $rented++;
            } else {
                $available++;
            }

            $this->line("Vehicle #{$vehicle->id} synced to {$vehicle->status}.");
        }

        if ($updated === 0) {
            $this->info('Vehicle statuses are already in sync.');

            return self::SUCCESS;
        }

        $this->info("Updated {$updated} vehicle(s). Rented: {$rented}. Available: {$available}.");

        return self::SUCCESS;
    }
}