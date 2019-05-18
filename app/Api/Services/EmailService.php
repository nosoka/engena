<?php
/**
 * Created by PhpStorm.
 * User: zayinkrige
 * Date: 2016/03/22
 * Time: 15:54
 */

namespace App\Api\Services;

use Illuminate\Contracts\Mail\Mailer;
use Log;

class EmailService
{
    public $from;
    public $to;
    public $subject;
    public $view;
    public $data;
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->from   = ['email' => env('MAIL_FROM_ADDRESS'), 'name' => env('MAIL_FROM_NAME')];
    }

    public function emailPasswordReminder($user, $reminder)
    {

        $resetCode = "{$user->emailHash}.{$reminder->code}";
        $resetUrl  = env('ENGENA_APP_BASEURL') . "reset_password?code={$resetCode}";

        $this->to      = ['email' => $user->Email, 'name' => $user->full_name];
        $this->subject = 'Engena - Password Reset Request';
        $this->view    = 'emails.reset_password';
        $this->data    = compact('user', 'resetUrl');

        return $this->deliver();
    }

    public function emailSignupConfirmation($user)
    {
        $this->to      = ['email' => $user->Email, 'name' => $user->full_name];
        $this->subject = 'Engena - Sign Up Confirmation';
        $this->view    = 'emails.signup_confirmation';
        $this->data    = compact('user');

        return $this->deliver();
    }

    public function emailActivePasses($reserve)
    {
        $this->to      = ['email' => $reserve->Admin_Email, 'name' => $reserve->Admin_Email];
        $this->subject = "Engena - {$reserve->ReserveName} Reserve - Daily valid passes list";
        $this->view    = 'emails.admin.active_passes';
        $this->data    = compact('reserve');

        return $this->deliver();
    }

    public function emailNewPassesToCustomer($passes)
    {
        $pass          = $passes->first();
        $user          = $pass->user;
        $reserve       = $pass->pass->reserve;
        $this->to      = ['email' => $user->Email, 'name' => $user->full_name];
        $this->subject = "Engena - {$reserve->ReserveName} Reserve - Your pass purchase confirmation";
        $this->view    = 'emails.passes_purchased';
        $this->data    = compact('user', 'pass', 'passes');

        return $this->deliver();
    }

    public function emailNewPassesToAdmin($passes)
    {
        $pass          = $passes->first();
        $user          = $pass->user;
        $reserve       = $pass->pass->reserve;
        $this->to      = ['email' => $reserve->Admin_Email, 'name' => $reserve->Admin_Email];
        $this->subject = "Engena - {$reserve->ReserveName} Reserve - New Passes purchased";
        $this->view    = 'emails.admin.passes_purchased';
        $this->data    = compact('user', 'pass', 'passes');

        return $this->deliver();
    }

    public function deliver()
    {
        if (!filter_var($this->to['email'], FILTER_VALIDATE_EMAIL)) {
            Log::error('Invalid email format for receipent email', [$this->to['email']]);
            return false;
        }

        return $this->mailer->send($this->view, $this->data, function ($message) {
            $message->from($this->from['email'], $this->from['name'])
                ->to($this->to['email'], $this->to['name'])
                ->subject($this->subject);
        });
    }
}
