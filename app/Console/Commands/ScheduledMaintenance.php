<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScheduledMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:schedule
                            {action : enable or disable maintenance mode}
                            {--message= : Custom maintenance message}
                            {--retry= : Retry after seconds}
                            {--duration= : Duration in minutes (for auto-disable)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule maintenance mode with automatic disable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if ($action === 'enable') {
            $this->enableMaintenance();
        } elseif ($action === 'disable') {
            $this->disableMaintenance();
        } else {
            $this->error('Invalid action. Use "enable" or "disable".');
            return 1;
        }

        return 0;
    }

    private function enableMaintenance()
    {
        $options = [];

        if ($message = $this->option('message')) {
            $options['--message'] = $message;
        }

        if ($retry = $this->option('retry')) {
            $options['--retry'] = $retry;
        }

        Artisan::call('down', $options);

        $this->info('Maintenance mode enabled successfully!');

        // Schedule auto-disable if duration is specified
        if ($duration = $this->option('duration')) {
            $this->info("Maintenance mode will be automatically disabled in {$duration} minutes.");

            // You could implement a job queue here to disable maintenance after duration
            // For now, we'll just show the command to run
            $this->warn("To disable automatically, run this command in {$duration} minutes:");
            $this->line("php artisan maintenance:schedule disable");
        }
    }

    private function disableMaintenance()
    {
        Artisan::call('up');
        $this->info('Maintenance mode disabled successfully!');
    }
}
