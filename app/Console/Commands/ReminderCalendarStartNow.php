<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CalendarService;

class ReminderCalendarStartNow extends Command
{
    protected $signature = 'command:reminderCalendarStartNow';
    protected $description = 'Command description';
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
    }
    public function handle()
    {
        return $this->calendarService->reminderCalendarStartTime();
    }
}
