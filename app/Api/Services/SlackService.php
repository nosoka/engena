<?php
/**
 * Created by PhpStorm.
 * User: zayinkrige
 * Date: 2016/03/22
 * Time: 15:54
 */

namespace App\Api\Services;

use Log;
use Slack;

class SlackService
{

    public function notifySlackOfNewPasses($passes)
    {
        $pass          = $passes->first();
        $user          = $pass->user;
        $reserve       = $pass->pass->reserve;
        $data          = compact('user', 'pass', 'passes', 'reserve');

        return $this->deliver($data);
    }

    private function deliver($data)
    {
        $user       = $data["user"];
        $pass       = $data["pass"];
        $passes     = $data["passes"];
        $reserve    = $data["reserve"];
        if ($passes->count() > 1) {
            return Slack::send("{$user->full_name} just purchased {$passes->count()} passes for {$reserve->ReserveName}, valid : {$pass->start_date} - {$pass->end_date }, value : {$passes->sum('pass_amount')}");
        } else {
            return Slack::send("{$user->full_name} just purchased a {$pass->pass_amount} pass for {$reserve->ReserveName}, valid : {$pass->start_date} - {$pass->end_date }, value : {$pass->pass_amount}");
        }
    }
}
