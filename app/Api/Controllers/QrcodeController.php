<?php

namespace App\Api\Controllers;

use App\Api\Requests\CreateQrcodeRequest;
use App\Api\Requests\DeleteQrcodeRequest;
use App\Api\Requests\ScanQrcodeRequest;
use App\Api\Requests\CheckInAndOutRequest;
use App\Api\Services\QrcodeService;
use App\Api\Transformers\QrcodeTransformer;
use App\Api\Transformers\QrcodeScanTransformer;

/**
 * @Resource("QR Codes", uri="api")
 */
class QrcodeController extends BaseController
{
    public function __construct(QrcodeService $qrcodeService)
    {
        $this->qrcodeService = $qrcodeService;
    }

    public function store(CreateQrcodeRequest $request)
    {
        if (!$qrcode = $this->qrcodeService->create()) {
            return $this->response->error('Failed to generate QR Code.', 422);
        }

        return $this->item($qrcode, new QrcodeTransformer);
    }

    public function destroy($id, DeleteQrcodeRequest $request)
    {
        if (!$this->qrcodeService->delete($id)) {
            return $this->response->error('Failed to delete QR Code.', 422);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Check in
     *
     * ```js
     * // Sample Request
     * // qrcode is the decoded/scanned value of the QR Code image
     * $.ajax({
     *     url: "https://api.engena.co.za/api/checkin",
     *     type: "POST",
     *     data: { "qrcode": "xyz......", "source": "mobile app", "gps": "xyz..." }
     * });
     * ```
     *
     * @Post("/checkin")
     * @Transaction({
     *      @Response(200, body={"status": "success", "reserve": {}, "userPasses": {}}),
     *      @Response(422, body={"message": "Failed to checkin using the QR Code.", "status_code": 422})
     * })
     */
    public function checkin(CheckInAndOutRequest $request)
    {
        if (!$qrcode = $this->qrcodeService->checkin()) {
            return $this->response->error('Failed to scan QR Code.', 422);
        }

        return $this->item($qrcode, new QrcodeScanTransformer);
    }

    /**
     * Check out
     *
     * ```js
     * // Sample Request
     * // qrcode is the decoded/scanned value of the QR Code image
     * $.ajax({
     *     url: "https://api.engena.co.za/api/checkout",
     *     type: "POST",
     *     data: { "qrcode": "xyz......", "source": "mobile app", "gps": "xyz..." }
     * });
     * ```
     *
     * @Post("/checkout")
     * @Transaction({
     *      @Response(200, body={"status": "success", "reserve": {}}),
     *      @Response(422, body={"message": "Failed to checkin using the QR Code.", "status_code": 422})
     * })
     */
    public function checkout(CheckInAndOutRequest $request)
    {
        if (!$qrcode = $this->qrcodeService->checkout()) {
            return $this->response->error('Failed to scan QR Code.', 422);
        }

        return $this->item($qrcode, new QrcodeScanTransformer);
    }
}
