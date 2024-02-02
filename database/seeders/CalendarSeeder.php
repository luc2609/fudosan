<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\Company;
use App\Models\Division;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use App\Models\User;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ja_JP');
        $currentTime = date('Y-m-d H:i:s');
        $url = 'https://workspace.google.com/';
        $companies = Company::all();
        $meetingEndTime = strtotime('+5 hour', strtotime($currentTime));
        $meetingEndTime = date('Y-m-d H:i:s', $meetingEndTime);
        $endDate = strtotime('+1 year', strtotime($currentTime));
        $endDate = date('Y-m-d H:i:s', $endDate);
        $limit = random_int(10, 50);

        // calendar no loop
        foreach ($companies as $company) {
            $companyId = $company->id;
            $divisionIds = Division::where('company_id', $companyId)->pluck('divisions.id');
            for ($i = 0; $i < $limit; $i++) {
                $divisionId = $divisionIds->random();
                Calendar::create([
                    'note' => $faker->sentence(20),
                    'meeting_start_time' => $currentTime,
                    'meeting_end_time' => $meetingEndTime,
                    'is_public' => random_int(0, 1),
                    'division_id' => $divisionId,
                    'meeting_url' =>  $url,
                    'meeting_type' => 1,
                    'notify_id' => random_int(1, 2),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'company_id' => $companyId,
                    'start_date' => $currentTime,
                    'end_date' => $meetingEndTime,
                    'title' => $faker->sentence(5),
                    'repeat_id' => 1,
                ]);
            }
        }

        // calendar loop
        foreach ($companies as $company) {
            $companyId = $company->id;
            $divisionIds = Division::where('company_id', $companyId)->pluck('divisions.id');
            for ($i = 0; $i < $limit; $i++) {
                $divisionId = $divisionIds->random();
                Calendar::create([
                    'note' => $faker->sentence(20),
                    'meeting_start_time' => $currentTime,
                    'meeting_end_time' => $meetingEndTime,
                    'is_public' => random_int(0, 1),
                    'division_id' => $divisionId,
                    'meeting_url' =>  $url,
                    'meeting_type' => 1,
                    'notify_id' => random_int(1, 2),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'company_id' => $companyId,
                    'start_date' => $currentTime,
                    'end_date' => $endDate,
                    'title' => $faker->sentence(5),
                    'repeat_id' => random_int(2, 3),
                ]);
            }
        }

        // calendar project
        foreach ($companies as $company) {
            $companyId = $company->id;
            $divisionIds = Division::where('company_id', $companyId)->pluck('divisions.id');
            for ($i = 0; $i < $limit; $i++) {
                $divisionId = $divisionIds->random();
                $projectIds = Project::where('company_id', $companyId)->where('division_id', $divisionId)->pluck('id');
                if (count($projectIds) > 0) {
                    $projectId = $projectIds->random();
                    $title = Project::where('id', $projectId)->first()->title;
                    Calendar::create([
                        'note' => $faker->sentence(20),
                        'meeting_start_time' => $currentTime,
                        'meeting_end_time' => $meetingEndTime,
                        'is_public' => random_int(0, 1),
                        'division_id' => $divisionId,
                        'meeting_url' =>  $url,
                        'meeting_type' => 1,
                        'notify_id' => random_int(1, 2),
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                        'company_id' => $companyId,
                        'start_date' => $currentTime,
                        'end_date' => $meetingEndTime,
                        'title' => $faker->sentence(5),
                        'repeat_id' => 1,
                        'project_id' => $projectId,
                        'title' => $title,
                        'project_phase_id' => random_int(1, 9)
                    ]);
                }
            }
        }

        $calendars = Calendar::all();
        foreach ($calendars as $calendar) {
            $calendarUsers = [];
            $calendarId = $calendar->id;
            $notifyId = $calendar->notify_id;
            $companyId = $calendar->company_id;
            $userIds = User::where('users.company', $companyId)->pluck('users.id');

            $calendarUser1 = [
                'user_id' => $userIds->random(),
                'calendar_id' => $calendarId,
                'notify_id' => $notifyId,
                'is_accept' => 1,
                'is_host' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
            $calendarUser2 = [
                'user_id' => $userIds->random(),
                'calendar_id' => $calendarId,
                'notify_id' => $notifyId,
                'is_accept' => random_int(0, 2),
                'is_host' => 0,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];

            array_push($calendarUsers, $calendarUser1);
            array_push($calendarUsers, $calendarUser2);
            DB::table('calendar_users')->insert($calendarUsers);
        }
    }
}
