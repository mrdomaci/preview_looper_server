<?php
declare(strict_types=1);

namespace App\Helpers;

use stdClass;

class JsonHelper
{
    /**
     * @param stdClass $json
     * @param string $key
     * @return bool
     */
    public static function containsKey(stdClass $json, string $key): bool
    {
        if (isset($json->$key)) {
            return true;
        }
        return false;
    }
}