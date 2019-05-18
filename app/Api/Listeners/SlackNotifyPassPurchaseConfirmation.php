<?php

namespace App\Api\Listeners;

use App\Api\Events\PassesPurchased;
use App\Api\Services\EmailService;
use App\Api\Services\SlackService;

class SlackNotifyPassPurchaseConfirmation
{

    public function __construct(SlackService $slackService)
    {
        $this->slackService = $slackService;
    }

    public function handle(PassesPurchased $event)
    {
        $this->slackService->notifySlackOfNewPasses($event->passes);
    }

}
