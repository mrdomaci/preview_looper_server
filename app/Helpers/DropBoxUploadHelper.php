<?php

declare(strict_types=1);

namespace App\Helpers;

class DropBoxUploadHelper
{
    public static function upload(string $path, string $fileName): ?string
    {
        $filePath = $path . '/' . $fileName;
        $accessToken = env('DROPBOX_ACCESS_TOKEN');
        
        // Open file for reading
        $fp = fopen($filePath, 'rb');
        $size = filesize($filePath);

        // Headers for Dropbox API request
        $cheaders = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: {"path":"/backup/' . $fileName . '", "mode":"add"}'
        ];

        // Initialize cURL
        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture response

        // Execute cURL and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            fclose($fp);
            throw new \Exception('cURL Error: ' . $error_msg);
        }

        // Close cURL and file handles
        curl_close($ch);
        fclose($fp);
        // Return response from Dropbox API
        return $response;
    }
}
