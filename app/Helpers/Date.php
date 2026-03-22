<?php

namespace App\Helpers;

use Carbon\Carbon;

class Date
{
    public static function toTimezone(string $date): string
    {
        return Carbon::parse($date)
            ->timezone(config('app.timezone'))
            ->format('d/m/Y H:i:s');
    }
}
