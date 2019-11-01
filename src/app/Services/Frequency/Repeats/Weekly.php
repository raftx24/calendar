<?php

namespace LaravelEnso\Calendar\app\Services\Frequency\Repeats;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Weekly extends Repeat
{
    public function dates(): Collection
    {
        return $this->interval()
            ->filter(function (Carbon $date) {
                return $this->event->start_date->dayOfWeek === $date->dayOfWeek;
            });
    }
}
