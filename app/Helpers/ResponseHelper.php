<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;

class ResponseHelper
{
    private const CDN_URL = 'https://cdn.myshoptet.com/usr/%s.myshoptet.com/user/shop/detail/%s';
    /**
     * @param Collection<Image> $images
     * @param int $eshopId
     * @return array<string>
     */
    public static function getImageResponseArray(Collection $images, int $eshopId): array
    {
        $response = [];
        foreach($images as $image) {
            $product = $image->product;
            $response[$product->getAttribute('guid')][] = sprintf(self::CDN_URL, (string) $eshopId, $image->getAttribute('name'));
        }
        return $response;
    }
}