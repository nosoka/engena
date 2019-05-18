<?php

namespace App\Api\Services;

use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    public function moveFile(UploadedFile $uploadedFile, $destinationFolder)
    {
        $extension = $uploadedFile->guessExtension();
        $fileName  = str_shuffle(sha1(microtime())) . "." . $extension;
        if(!$savedFile = $uploadedFile->move($destinationFolder, $fileName)) {
            return false;
        }

        if ($this->isImage($savedFile)) {
            $this->createThumbImages($savedFile);
        }

        return $savedFile;
    }

    public function createThumbImages(File $image)
    {
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
        Image::make($fileRealPath)->fit(555,333)->save();
    }

    public function isImage(File $file)
    {
        $imageMimes = ['jpeg', 'png', 'gif', 'bmp', 'svg'];
        return in_array($file->guessExtension(), $imageMimes);
    }

    public function guessThumbImageUrls($imageUrl)
    {
        if (!preg_match('/engena/', $imageUrl)) {
            return [
                'user' => $imageUrl, 'small' => $imageUrl, 'medium' => $imageUrl,
                'large' => $imageUrl, 'original' => $imageUrl,
            ];
        }

        $path_parts = pathinfo($imageUrl);
        if ((!array_key_exists('extension', $path_parts)) || ($path_parts['extension'] == '')) {
            return "";
        }

        $extension          = '.' . $path_parts['extension'];
        $images             = array();
        $images['small']    = str_replace($extension, '_small' . $extension, $imageUrl);
        $images['medium']   = str_replace($extension, '_medium' . $extension, $imageUrl);
        $images['large']    = str_replace($extension, '_large' . $extension, $imageUrl);
        $images['original'] = str_replace($extension, '_original' . $extension, $imageUrl);

        return $images;
    }
}
