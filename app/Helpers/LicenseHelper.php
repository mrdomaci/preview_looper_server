<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Currency;
use App\Models\License;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class LicenseHelper
{
    public static function generate(License $license, ?string $name, ?string $address, ?string $cin, ?string $tin): string
    {
        $imageBase64 = base64_encode(file_get_contents(public_path('images/easy-upsell/webpage_logo_cs.png')));
        $data = [
            'price' => Currency::formatPrice((string) $license->value, $license->currency),
            'valid_to' => $license->valid_to->format('d.m.Y'),
            'created_at' => $license->created_at->format('d.m.Y'),
            'number' => $license->getNumber(),
            'name' => $name,
            'address' => $address,
            'cin' => $cin,
            'tin' => $tin,
            'image' => $imageBase64,
        ];

        $html = view('template.license', $data)->render();

        $mpdf = new Mpdf(['tempDir' => storage_path('app/mpdf-tmp'), 'memory_limit' => '512M']);
        $mpdf->WriteHTML($html);

        $path = 'invoice/easy-upsell-' . $license->getNumber() . '.pdf';
        Storage::disk()->put($path, $mpdf->Output('', 'S'));

        return $path;
    }
}
