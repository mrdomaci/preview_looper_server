<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Advert;
use Gregwar\Image\Image;

class ImageHelper
{
    public static function generateProductImage(Advert $advert): void
    {
        $primary = $advert->getAttribute('primary_text');
        $secondary = $advert->getAttribute('secondary_text');
        $primaryColor = '#239ceb';
        $secondaryArray = explode(' ', $secondary);
        $firstLineSecondary = '';
        $secondLineSecondary = '';
        foreach ($secondaryArray as $value) {
            if (strlen($firstLineSecondary) < 35) {
                $firstLineSecondary .= $value . ' ';
            } else {
                $secondLineSecondary .= $value . ' ';
            }
        }
        $imageUrl = $advert->getAttribute('image_url');
        file_put_contents('storage/app/images/970x250.png', file_get_contents($imageUrl));
        $image = Image::open('storage/app/images/970x250.png');
        $image = self::resizeImageToFit($image, 970, 250, 20, 20);
        $banner = Image::create(970,250)
            ->fill('#ffffff')
            ->rectangle(0, 245, 970, 250, $primaryColor, true)
            ->merge($image, 10, 10)
            ->write('public/fonts/Ubuntu/Ubuntu-Regular.ttf', $primary, 485, 50, 50, 0, '#424949', 'center')
            ->write('public/fonts/Ubuntu/Ubuntu-Regular.ttf', $firstLineSecondary, 485, 190, 28, 0, '#424949', 'center')
            ->write('public/fonts/Ubuntu/Ubuntu-Regular.ttf', $secondLineSecondary, 485, 240, 28, 0, '#424949', 'center')
            ->save('storage/app/images/970x250.png');
    }

    public static function resizeImageToFit(Image $image, int $width, int $height, int $offsetWidth, int $offsetHeight): Image
    {
        $imageWidth = $image->width();
        $imageHeight = $image->height();
        $imageRatio = $imageWidth / $imageHeight;
        $targetRatio = $width / $height;

        if ($imageRatio > $targetRatio) {
            $image->resize($width - $offsetWidth, null);
        } else {
            $image->resize(null, $height - $offsetHeight);
        }

        return $image;
    }
}