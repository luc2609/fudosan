<?php

namespace App\Console\Commands;

use App\Services\ProjectService;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrentPhaseProject extends Command
{
    protected $signature = 'command:curentPhaseProjects';
    protected $description = 'Command description';
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $projectPhase = $this->projectService->pushNotiPhaseProject();
            DB::commit();
            return $projectPhase;
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : false - ' . $e->getMessage());
            DB::rollBack();
            return true;
        }
    }
}
