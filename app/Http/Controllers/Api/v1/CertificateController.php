<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateRequest;
use App\Http\Requests\GetListCertificateRequest;
use App\Services\CertificateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(
        CertificateService $certificateService
    ) {
        $this->certificateService = $certificateService;
    }

    /**
     * Get list certificate
     *
     * @param GetListCertificateRequest $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetListCertificateRequest $request, $userId)
    {
        try {
            return $this->certificateService->index($request, $userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Create new certificate in CMS
     *
     * @param CertificateRequest $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CertificateRequest $request, $userId)
    {
        try {
            return $this->certificateService->create($request, $userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Show certificate in CMS
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->certificateService->show($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Update certificate in CMS
     *
     * @param CertificateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CertificateRequest $request, $id)
    {
        try {
            return $this->certificateService->update($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Delete certificate in CMS
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            return $this->certificateService->destroy($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * CURD certificate for App
     *
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function curdCertificateApp(Request $request, $userId)
    {
        try {
            $params = $request->all();
            return $this->certificateService->curdCertificateApp($params, $userId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}
