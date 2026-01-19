<?php

namespace App\Services;

use App\Models\AlertChannel;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class IncidentService
{
    public function createIncident(array $data): Incident
    {
        $incident = Incident::create($data);

        // Trigger alerts
        $this->triggerAlerts($incident);

        return $incident;
    }

    public function resolveIncident(Incident $incident): void
    {
        $incident->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        // Log event
        $incident->events()->create([
            'type' => 'resolved',
            'message' => 'Incident resolved',
        ]);
    }

    private function triggerAlerts(Incident $incident): void
    {
        $alertChannels = AlertChannel::where('organization_id', $incident->monitor->organization_id)
            ->where('is_active', true)
            ->get();

        foreach ($alertChannels as $channel) {
            try {
                $this->sendAlert($channel, $incident);
            } catch (\Exception $e) {
                Log::error('Failed to send alert', [
                    'channel_id' => $channel->id,
                    'incident_id' => $incident->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function sendAlert(AlertChannel $channel, Incident $incident): void
    {
        $notification = Notification::create([
            'incident_id' => $incident->id,
            'alert_channel_id' => $channel->id,
            'status' => 'pending',
        ]);

        // Send based on type
        switch ($channel->type) {
            case 'email':
                $this->sendEmailAlert($channel, $incident, $notification);
                break;
            case 'webhook':
                $this->sendWebhookAlert($channel, $incident, $notification);
                break;
            case 'slack':
                $this->sendSlackAlert($channel, $incident, $notification);
                break;
        }
    }

    private function sendEmailAlert(AlertChannel $channel, Incident $incident, Notification $notification): void
    {
        // Implement email sending
        // Use Laravel Mail
        $notification->update(['status' => 'sent', 'sent_at' => now()]);
    }

    private function sendWebhookAlert(AlertChannel $channel, Incident $incident, Notification $notification): void
    {
        // Implement webhook
        $notification->update(['status' => 'sent', 'sent_at' => now()]);
    }

    private function sendSlackAlert(AlertChannel $channel, Incident $incident, Notification $notification): void
    {
        // Implement Slack
        $notification->update(['status' => 'sent', 'sent_at' => now()]);
    }
}