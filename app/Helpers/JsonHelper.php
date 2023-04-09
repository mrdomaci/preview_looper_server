<?php
declare(strict_types=1);

namespace App\Helpers;

use Nette\Utils\Json;

class JsonHelper
{
    /**
     * @param Json $json
     * @param string $key
     * @return bool
     */
    public static function containsKey(Json $json, string $key): bool
    {
        if (isset($json->$key)) {
            return true;
        }
        return false;
    }
}