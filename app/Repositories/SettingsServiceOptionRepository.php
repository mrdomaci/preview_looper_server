<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\SettingsServiceOption;

class SettingsServiceOptionRepository
{
    public function get(int $id): SettingsServiceOption
    {
        return SettingsServiceOption::where('id', $id)->firstOrFail();
    }
}
