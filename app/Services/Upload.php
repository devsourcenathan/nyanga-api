<?php

use Illuminate\Support\Facades\Storage;

class Upload
{
    public static function uploadFile($file)
    {
        if ($file) {
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');  // Utilisation du disque public

            $url = Storage::disk('public')->url($path);

            return $url;
        }

        return null;
    }
}
