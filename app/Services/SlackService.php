<?php
namespace App\Services;

use App\Notifications\SlackNotification;
use Illuminate\Notifications\Notifiable;

class SlackService
{
    use Notifiable;

    public function send($message)
    {
        $this->notify(new SlackNotification($message));
    }

    protected function routeNotificationForSlack()
    {
        return env('SLACK_WEBHOOK_URL');
    }
}
