<?php

namespace App\Listeners\Notifications;

use App\Models\AlertRule;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        // Get the event type
        $eventType = $this->getEventType($event);

        // Find active alert rules for this event type
        $alertRules = AlertRule::active()
            ->where('company_id', $event->company->id)
            ->byEventType($eventType)
            ->get();

        foreach ($alertRules as $rule) {
            // Get recipients based on rule
            $recipients = $rule->getRecipients();

            foreach ($recipients as $recipient) {
                // Create notification
                $notification = Notification::create([
                    'type' => $this->getNotificationType($eventType),
                    'category' => $this->getCategory($eventType),
                    'title' => $this->getTitle($event, $eventType),
                    'body' => $rule->message_template ?? $this->getBody($event, $eventType),
                    'notifiable_type' => User::class,
                    'notifiable_id' => $recipient->id,
                    'priority' => $this->getPriority($eventType),
                    'company_id' => $event->company->id,
                    'data' => $this->getData($event),
                ]);

                // Log notification (handled by LogNotificationListener)
            }
        }
    }

    protected function getEventType($event): string
    {
        $className = class_basename($event);

        return strtolower(str_replace('Event', '', $className));
    }

    protected function getNotificationType($eventType): string
    {
        return match ($eventType) {
            'budgetexceeded', 'lowstock', 'paymentoverdue' => 'error',
            'deadlineapproaching', 'contractexpiring' => 'warning',
            'approvalrequested' => 'info',
            default => 'info',
        };
    }

    protected function getCategory($eventType): string
    {
        return match ($eventType) {
            'budgetexceeded' => 'alert',
            'deadlineapproaching' => 'deadline',
            'approvalrequested' => 'approval',
            'contractexpiring' => 'alert',
            'lowstock' => 'alert',
            'paymentoverdue' => 'alert',
            default => 'system',
        };
    }

    protected function getPriority($eventType): string
    {
        return match ($eventType) {
            'budgetexceeded', 'paymentoverdue' => 'urgent',
            'lowstock', 'contractexpiring' => 'high',
            'deadlineapproaching' => 'normal',
            'approvalrequested' => 'normal',
            default => 'normal',
        };
    }

    protected function getTitle($event, $eventType): string
    {
        return match ($eventType) {
            'budgetexceeded' => 'Budget Exceeded',
            'deadlineapproaching' => 'Deadline Approaching',
            'approvalrequested' => 'Approval Required',
            'contractexpiring' => 'Contract Expiring Soon',
            'lowstock' => 'Low Stock Alert',
            'paymentoverdue' => 'Payment Overdue',
            default => 'Notification',
        };
    }

    protected function getBody($event, $eventType): string
    {
        return match ($eventType) {
            'budgetexceeded' => "Budget exceeded: {$event->spent} / {$event->budget}",
            'deadlineapproaching' => "Deadline approaching: {$event->deadline}",
            'approvalrequested' => "Approval required for {$event->type}",
            'contractexpiring' => "Contract expiring on {$event->expiryDate}",
            'lowstock' => "Stock level is low: {$event->currentStock} / {$event->minStock}",
            'paymentoverdue' => "Payment is {$event->daysOverdue} days overdue",
            default => 'You have a new notification',
        };
    }

    protected function getData($event): array
    {
        return [
            'event' => class_basename($event),
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
