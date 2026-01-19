<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Incident;
use App\Models\IncidentEvent;
use App\Models\Monitor;
use App\Services\IncidentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckMonitorJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Monitor $monitor
    ) {}

    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->monitor->timeout)
                ->withHeaders($this->monitor->headers ?? [])
                ->send($this->monitor->method, $this->monitor->url, [
                    'body' => $this->monitor->body,
                ]);

            $responseTime = (microtime(true) - $startTime) * 1000;

            $isSuccess = $response->successful();
            $statusCode = $response->status();
            $errorMessage = $isSuccess ? null : $response->body();

            // Check SSL if enabled
            $sslDaysLeft = null;
            if ($this->monitor->check_ssl && $isSuccess) {
                $sslDaysLeft = $this->checkSslExpiry($this->monitor->url);
            }

            // Create check record
            $check = Check::create([
                'monitor_id' => $this->monitor->id,
                'status_code' => $statusCode,
                'response_time' => (int) $responseTime,
                'is_success' => $isSuccess,
                'error_message' => $errorMessage,
                'ssl_days_left' => $sslDaysLeft,
                'checked_at' => now(),
            ]);

            // Check for incident creation
            $this->checkForIncident($check);

        } catch (\Exception $e) {
            Log::error('Monitor check failed', [
                'monitor_id' => $this->monitor->id,
                'error' => $e->getMessage(),
            ]);

            Check::create([
                'monitor_id' => $this->monitor->id,
                'is_success' => false,
                'error_message' => $e->getMessage(),
                'checked_at' => now(),
            ]);

            // Check for incident
            $this->checkForIncident(null);
        }
    }

    private function checkSslExpiry(string $url): ?int
    {
        // Implement SSL expiry check
        // This is a placeholder
        return null;
    }

    private function checkForIncident(?Check $check): void
    {
        $recentChecks = Check::where('monitor_id', $this->monitor->id)
            ->where('checked_at', '>=', now()->subMinutes($this->monitor->interval * $this->monitor->failure_threshold))
            ->orderBy('checked_at', 'desc')
            ->take($this->monitor->failure_threshold)
            ->get();

        $failureCount = $recentChecks->where('is_success', false)->count();

        if ($failureCount >= $this->monitor->failure_threshold) {
            // Create incident if not exists
            $existingIncident = Incident::where('monitor_id', $this->monitor->id)
                ->where('status', '!=', 'resolved')
                ->first();

            if (!$existingIncident) {
                $incident = Incident::create([
                    'monitor_id' => $this->monitor->id,
                    'title' => "Monitor {$this->monitor->name} is down",
                    'description' => "Failed {$failureCount} consecutive checks",
                    'status' => 'open',
                    'severity' => 'high',
                    'started_at' => now(),
                ]);

                IncidentEvent::create([
                    'incident_id' => $incident->id,
                    'type' => 'created',
                    'message' => 'Incident created due to monitor failures',
                ]);

                // Trigger alerts
                // This will be handled by IncidentService
            }
        }
    }
}
