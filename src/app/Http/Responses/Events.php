<?php

namespace LaravelEnso\Calendar\app\Http\Responses;

use Carbon\Carbon;
use LaravelEnso\Calendar\app\Models\Event;
use Illuminate\Contracts\Support\Responsable;
use LaravelEnso\Calendar\app\Models\Calendar;
use LaravelEnso\Calendar\app\Facades\Calendars;
use LaravelEnso\Calendar\app\Contracts\CustomCalendar;
use LaravelEnso\Calendar\app\Http\Resources\Event as Resource;

class Events implements Responsable
{
    private $request;
    private $calendars;

    public function toResponse($request)
    {
        $this->request = $this->request($request);

        $this->calendars = $this->calendars();

        return Resource::collection(
            $this->native()->concat($this->custom())
        );
    }

    private function native()
    {
        $nativeCalendars = $this->calendars->filter(function ($calendar) {
            return $this->isNative($calendar);
        });

        return Event::for($nativeCalendars)->between(
            $this->request->get('startDate'),
            $this->request->get('endDate')
        )->get();
    }

    private function custom()
    {
        return $this->calendars->reject(function ($calendar) {
            return $this->isNative($calendar);
        })->reduce(function ($events, CustomCalendar $calendar) {
            return $events->concat(
                $calendar->events(
                    $this->request->get('startDate'),
                    $this->request->get('endDate')
                )
            );
        }, collect());
    }

    private function isNative($calendar)
    {
        return $calendar instanceof Calendar;
    }

    private function calendars()
    {
        return Calendars::only($this->request->get('calendars'));
    }

    private function request($request)
    {
        $request->replace([
            'startDate' => $request->get('startDate')
                ? Carbon::parse($request->get('startDate'))
                : null,
            'endDate' => $request->get('endDate')
                ? Carbon::parse($request->get('endDate'))
                : null,
            'calendars' => $request->get('calendars', []),
        ]);

        return $request;
    }
}