<?php

namespace App\Console\Commands;

use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;
use Illuminate\Console\Command;

class ScheduleMonitorChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitors:check {--monitor= : Specific monitor ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule HTTP checks for active monitors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Monitor::where('is_active', true);

        if ($monitorId = $this->option('monitor')) {
            $query->where('id', $monitorId);
        }

        $monitors = $query->get();

        $this->info("Scheduling checks for {$monitors->count()} monitors");

        foreach ($monitors as $monitor) {
            CheckMonitorJob::dispatch($monitor);
        }

        $this->info('Checks scheduled successfully');
    }
}
