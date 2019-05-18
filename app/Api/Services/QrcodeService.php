<?php

namespace App\Api\Services;

use App\Api\Models\Qrcode as QrcodeModel;
use App\Api\Models\QrcodeScan as QrcodeScan;
use Dingo\Api\Routing\Helpers;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator as SimpleQrcode;
use Symfony\Component\HttpFoundation\File\File;

class QrcodeService
{
    use Helpers;

    public function __construct(FileService $fileService,
        QrcodeModel $qrcodeModel, QrcodeScan $qrcodeScan, SimpleQrcode $simpleQrcode)
    {
        $this->fileService  = $fileService;
        $this->simpleQrcode = $simpleQrcode;
        $this->qrcodeModel  = $qrcodeModel;
        $this->qrcodeScan   = $qrcodeScan;
    }

    public function create(array $qrcode = [])
    {
        $qrcode               = $qrcode ?: app('request')->all();
        $qrcode['hash']       = hash('sha1', $qrcode['data']) . str_random(5);

        $filename             = "{$qrcode['hash']}.png";
        $qrcode['image_url']  = url("/api/images/qrcodes/{$filename}");
        $qrcode['image_path'] = base_path("/public/images/qrcodes/{$filename}");
        $logo                 = base_path("/public/images/logo_square2.png");

        $this->simpleQrcode->format('png')->size(640)->margin(0)
                    ->color(102,153,0)
                    ->errorCorrection('H')->merge($logo, 0.2, true)
                    ->generate($qrcode['hash'], $qrcode['image_path']);

        $this->createThumbImages(new File($qrcode['image_path']));

        return $this->qrcodeModel->firstOrCreate($qrcode);
    }

    public function checkin(array $data = [])
    {
        $data    = $data ?: app('request')->all();
        $qrcode  = $this->qrcodeModel->findByHash($data['qrcode']);
        $qrcode->scan_type = 'checkin';

        $data['scan_type'] = 'checkin';
        $data['user_id']   = $this->auth->user()->ID;
        $data['qrcode_id'] = $qrcode->id;
        $this->qrcodeScan->add($data);

        return $qrcode;
    }

    public function checkout(array $data = [])
    {
        $data    = $data ?: app('request')->all();
        $qrcode  = $this->qrcodeModel->findByHash($data['qrcode']);
        $qrcode->scan_type = 'checkout';

        $data['scan_type'] = 'checkout';
        $data['user_id']   = $this->auth->user()->ID;
        $data['qrcode_id'] = $qrcode->id;
        $this->qrcodeScan->add($data);

        return $qrcode;
    }

    public function delete($qrcodeId = '')
    {
        //TODO:: delete the image files from file system

        return $this->qrcodeModel->destroy($qrcodeId);
    }

    public function createThumbImages(File $image)
    {
        if (!$this->fileService->isImage($image)) {
            return false;
        }

        $extension    = "." . $image->guessExtension();
        $fileName     = $image->getPath() . "/" . $image->getBasename($extension);
        $fileRealPath = $image->getRealPath();

        $smallImage    = $fileName . '_small' . $extension;
        $mediumImage   = $fileName . '_medium' . $extension;
        $largeImage    = $fileName . '_large' . $extension;
        $originalImage = $fileName . '_original' . $extension;

        Image::make($fileRealPath)->save($originalImage);
        Image::make($fileRealPath)->fit(640, 640)->save($largeImage);
        Image::make($fileRealPath)->fit(300, 300)->save($mediumImage);
        Image::make($fileRealPath)->fit(150, 150)->save($smallImage);
        Image::make($fileRealPath)->fit(500, 500)->save();
    }

}
