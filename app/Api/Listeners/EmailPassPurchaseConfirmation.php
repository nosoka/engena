<?php

namespace App\Api\Listeners;

use App\Api\Events\PassesPurchased;
use App\Api\Services\EmailService;

class EmailPassPurchaseConfirmation
{

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PassesPurchased $event)
    {
        $this->emailService->emailNewPassesToCustomer($event->passes);
        $this->emailService->emailNewPassesToAdmin($event->passes);
    }

}
