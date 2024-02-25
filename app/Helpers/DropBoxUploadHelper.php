<?php

declare(strict_types=1);

namespace App\Helpers;

class DropBoxUploadHelper
{
    public static function upload(string $path, string $fileName): void
    {
        $filePath = $path . '/' . $fileName;
        $accessToken = env('DROPBOX_ACCESS_TOKEN');
        
        $fp = fopen($filePath, 'rb');
        $size = filesize($filePath);
        
        $cheaders = array('Authorization: Bearer '.$accessToken,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: {"path":"/backup/'.$fileName.'", "mode":"add"}');
        
        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}
