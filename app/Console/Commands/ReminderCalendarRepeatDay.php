<?php

namespace App\Console\Commands;

use App\Services\CalendarService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReminderCalendarRepeatDay extends Command
{
    protected $signature = 'command:reminderCalendarRepeatDay';
    protected $description = 'Command description';
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
    }
    public function handle()
    {
        return $this->calendarService->reminderCalendarRepeatDays();
    }
}
