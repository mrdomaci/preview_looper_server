<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Currency;
use App\Models\License;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class LicenseHelper
{
    public static function generate(License $license): string
    {
        $data = [
            'price' => Currency::formatPrice((string) $license->value, $license->currency),
            'valid_to' => $license->valid_to->format('d.m.Y'),
            'created_at' => $license->created_at->format('d.m.Y'),
        ];

        $html = view('template.license', $data)->render();
            
        $mpdf = new Mpdf(['tempDir' => storage_path('app/mpdf-tmp')]);
        $mpdf->WriteHTML($html);

        $path = 'invoice/easy-upsell-' . $license->id . '.pdf';
        Storage::disk()->put($path, $mpdf->Output('', 'S'));

        return $path;
    }
}
