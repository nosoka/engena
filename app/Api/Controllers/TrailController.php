<?php

namespace App\Api\Controllers;

use App\Api\Repositories\TrailRepository;
use App\Api\Requests\UploadImageRequest;
use App\Api\Services\FileService;
use App\Api\Transformers\FileTransformer;
use App\Api\Transformers\TrailTransformer;

/**
 * @Resource("Trails", uri="api/trails")
 */
class TrailController extends BaseController
{
    public function __construct(FileService $fileService, TrailRepository $trail)
    {
        $this->fileService = $fileService;
        $this->trail       = $trail;
    }

    /**
     * Trails
     *
     * ```js
     * // Optional includes - reserve
     * // Optional filters  - id, name, reserve, activity, region
     *
     * // Get all trails
     * $.ajax({
     *     url: "https://engena.co.za/api/trails",
     *     type: "GET",
     *     headers: { Authorization: "Bearer <token>" },
     * });
     *
     * // Filter by id
     *     trails?id=1
     *     trails?id[]=1&id[]=2
     *
     * // Filter by name
     *     trails?name=canaries
     *     trails?name[]=canaries&name[]=koepel
     *
     * // Filter by activity
     *     trails?activity=bike
     *     trails?activity[]=bike&activity[]=running
     *
     * // Filter by reserve
     *     trails?reserve=Jonkershoek
     *     trails?reserve[]=Jonkershoek&reserve[]=Helderberg Farm
     *
     * // Filter by region
     *     trails?region=Durbanville
     *     trails?region[]=Durbanville&region[]=Somerset West
     *
     * // Include reserve information
     *     trails?include=reserve
     *     trails?name=canaries&include=reserve
     *
     * ```
     * @Get("")
     * @Response(200, body={"data":{{"id":2,"name":"Never Ending Story","summary":"Fun","description":"Fun","mapUrl":"https:\/\/www.google.com\/maps\/d\/edit?mid=z9K6kfJxSREs.kWsYRIkcCrz4&usp=sharing","activity":{"id":4,"name":"Bike"},"photos":{"data":{{"id":103,"name":"7aa0b343995684e96b20601ae1ba8e4d033115c1.jpeg","title":null,"mime_type":"image\/jpeg","description":null,"url":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1.jpeg","thumbs":{"small":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_small.jpeg","medium":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_medium.jpeg","large":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_large.jpeg","original":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_original.jpeg"}}}},"coverPhoto":{"id":103,"name":"7aa0b343995684e96b20601ae1ba8e4d033115c1.jpeg","title":null,"mime_type":"image\/jpeg","description":null,"url":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1.jpeg","thumbs":{"small":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_small.jpeg","medium":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_medium.jpeg","large":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_large.jpeg","original":"http:\/\/engena.co.za\/api\/public\/images\/trails\/7aa0b343995684e96b20601ae1ba8e4d033115c1_original.jpeg"}}}}})
     *
     */
    public function index()
    {
        return $this->collection($this->trail->model->addFilters()->active()->get(), new TrailTransformer);
    }

    public function uploadImage(UploadImageRequest $request)
    {
        $requestFile = app('request')->file('file');

        if (!$uploadedFile = $this->fileService->moveFile($requestFile, $this->trail->realDir)) {
            return $this->response->error('Failed to upload image.', 422);
        }
        if (!$fileRow = $this->trail->addFile($uploadedFile)) {
            return $this->response->error('Failed to save uploaded image.', 422);
        }

        return $this->item($fileRow, new FileTransformer);
    }
}
