<?php


namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{

    public function upload($imgFile, $image, $destination, SluggerInterface $slugger)
    {
        $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
        try {
            $imgFile->move(
                $destination,
                $newFilename
            );
            $image->setImage($newFilename);
        } catch (FileException $e) {

        }
    }
}
