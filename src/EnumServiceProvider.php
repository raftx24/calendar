<?php

namespace LaravelEnso\Calendar;

use LaravelEnso\Calendar\app\Enums\Calendars;
use LaravelEnso\Enums\EnumServiceProvider as ServiceProvider;

class EnumServiceProvider extends ServiceProvider
{
    public $register = [
        'calendars' => Calendars::class,
    ];
}
