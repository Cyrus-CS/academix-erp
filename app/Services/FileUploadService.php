<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;

class FileUploadService {
    public function uploadPhoto(?UploadedFile $photo): ?string
    {
        if (!$photo) {
        return null;
        }

        return $photo->store('students','public');
    }
}