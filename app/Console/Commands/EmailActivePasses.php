<?php

namespace App\Console\Commands;

use App\Api\Repositories\ReserveRepository;
use App\Api\Services\EmailService;
use Illuminate\Console\Command;

class EmailActivePasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:activepasses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email list of valid day passes for today to reserve admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ReserveRepository $reserve, EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
        $this->reserve      = $reserve;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reserves = $this->reserve->includeActivePasses();
        foreach ($reserves as $reserve) {
            $this->emailService->emailActivePasses($reserve);
        }
    }
}
