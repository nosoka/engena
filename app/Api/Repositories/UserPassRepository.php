<?php

namespace App\Api\Repositories;

use App\Api\Events\PassesPurchased;
use App\Api\Models\UserPass;
use App\Api\Models\Pass;
use App\Api\Services\EmailService;
use App\Api\Services\FileService;
use Carbon\Carbon;
use Log;

class UserPassRepository extends BaseRepository
{
    protected $filters = ['id' => 'ID'];

    public function __construct(UserPass $model, FileService $fileService, Pass $pass)
    {
        $this->model       = $model;
        $this->pass        = $pass;
        $this->fileService = $fileService;
        $this->request     = app('request');
    }

    public function updatePhoto($id)
    {
        $urlPath           = url('/api/public/images/userpasses/');
        $destinationFolder = base_path('/public/images/userpasses/');
        $uploadedFile      = $this->request->file('filename');
        $photo             = $this->fileService->moveFile($uploadedFile, $destinationFolder);

        $userPass             = $this->findOrFail($id);
        $userPass->pass_photo = $urlPath . "/" . $photo->getFilename();
        if ($userPass->save()) {
            return $userPass;
        }

        return false;
    }

    public function createPasses()
    {
        $passes     = collect();
        $passesData = $this->request->get('passes');
        foreach($passesData as $passData) {
            for ($i=0; $i < $passData['quantity']; $i++) {
                $passes->push($this->create($passData));
             }
        }

        event(new PassesPurchased($passes));
        return $passes;
    }

    public function create($data)
    {
        $pass = $this->pass->find($data['id']);
        $passData   = [
            'user_id'     => $this->auth->user()->ID,
            'pass_id'     => $data['id'],
            'pass_amount' => $pass['price'],
            'start_date'  => $data['startDate'],
            'status'      => 'active',
        ];
        $passData['start_date'] = $this->calculateStartDate($passData);
        $passData['end_date']   = $this->calculateEndDate($passData);
        $passData['is_owner']   = $this->isOwner($passData);

        if (!$userPass = $this->model->create($passData)) {
            return $this->response->error(trans('api.pass.create.failed'), 422);
        }

        Log::info("created user passes: UserPassId:: {$userPass->id} wechatTransactionId:: {$this->request->get('wechatTransactionId')}");

        return $userPass;
    }

    //TODO:: Simplify this implementation of url filters
    public function customDbFilters()
    {
        // Only fetch logged in user passes
        $this->select->where('user_id', $this->auth->user()->ID);

        // Only fetch passes with no photos
        if ($this->request->input('photo') == 'null') {
            $this->select
                ->whereNull('pass_photo')->orWhere('pass_photo', '')
                ->where('is_owner', '>=', (bool) true)
                ->where('start_date', '>=', date("Y-m-d"))
                ;
        }

        $this->select->orderBy('end_date', 'desc');

        return $this;
    }

    public function calculateStartDate($passData)
    {
        if ($passData['start_date'] != '') {
            $startDate = Carbon::createFromFormat('Y-m-d', $passData['start_date']);
        } else {
            $startDate = Carbon::now();
        }

        return $startDate->startofDay()->toDateTimeString();
    }

    public function calculateEndDate($passData)
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $passData['start_date']);
        $pass      = $this->pass->find($passData['pass_id']);

        $duration        = $pass->duration->duration;
        $duration_metric = $pass->duration->metric;

        if($duration_metric == 'day') {
            $endDate = $startDate->addDays($duration);
        }
        if($duration_metric == 'week') {
            $endDate = $startDate->addWeeks($duration);
        }
        if($duration_metric == 'month') {
            $endDate = $startDate->addMonths($duration);
        }
        if($duration_metric == 'year') {
            $endDate = $startDate->addYears($duration);
        }

        return $endDate->subDay()->endofDay()->toDateTimeString();
    }

    public function isOwner($passData)
    {
        $userPasses = $this->model->select('id')
            ->where('user_id', $passData['user_id'])
            ->where('pass_id', $passData['pass_id'])
            ->where('start_date', '>=', $passData['start_date'])
            ->whereAnd('end_date', '<=', $passData['end_date'])
            ->get()->toArray()
            ;

        if (sizeof($userPasses) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
