<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\ProjectPhaseService;
use Illuminate\Support\Facades\Log;

class ProjectPhase extends Command
{
    protected $signature = 'command:projectPhases';

    protected $description = 'Command description';
    protected $projectPhaseService;

    public function __construct(ProjectPhaseService $projectPhaseService)
    {
        $this->projectPhaseService = $projectPhaseService;
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $projectPhase = $this->projectPhaseService->cronjobPhase();
            DB::commit();
            return $projectPhase;
        } catch (Exception $e) {
            Log::error('false -' . $this->signature . ' - ' . __METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            DB::rollBack();
            return true;
        }
    }
}
