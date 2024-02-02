<?php

namespace App\Services;

use App\Repositories\Certificate\CertificateRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;
use DateTime;

class CertificateService
{
    protected $certificateInterface;
    protected $userInterface;

    public function __construct(
        CertificateRepositoryInterface $certificateInterface,
        UserRepositoryInterface $userInterface
    ) {
        $this->certificateInterface = $certificateInterface;
        $this->userInterface =  $userInterface;
    }

    /**
     * List certificate
     *
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($request, $id)
    {
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $certificates = $this->certificateInterface->index($id)->paginate($pageSize);
        return _success($certificates, __('message.list_certificate_success'), HTTP_SUCCESS);
    }

    /**
     * Certificate for APP
     *
     * @param $params
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function curdCertificateApp($params, $userId)
    {
        try {
            $auth = auth()->user();
            $user = $this->userInterface->find($userId);

            if (!_hasPermission($auth, $user)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }

            $certificateParams = [];
            if (isset($params['certificates'])) {
                $certificateParams = $params['certificates'];
            }
            $currentCertificateIds = $user->certificates->pluck('id')->toArray();
            $updateCertificateIds = [];

            foreach ($certificateParams as $certificate) {
                if (is_string($certificate)) {
                    $certificate = json_decode($certificate, true);
                }

                $degreeDate = $this->degreeDate($certificate['degree_date']);

                if (isset($certificate['id'])) {
                    $certificateUser = $user->certificates
                        ->where('id', $certificate['id'])
                        ->first();

                    if (!$certificateUser) {
                        return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
                    }

                    $data = [
                        'name' => $certificate['name'] ?? ' ',
                        'degree_date' => $degreeDate
                    ];

                    $this->certificateInterface->update($certificateUser->id, $data);
                    $updateCertificateIds[] = $certificateUser->id;
                } else {

                    $data = [
                        'name' => $certificate['name'] ?? ' ',
                        'degree_date' => $degreeDate,
                        'user_id' => $userId,
                    ];

                    $existCertificate = $this->certificateInterface->existCertificate($userId, $certificate['name'], $degreeDate);
                    if (!$existCertificate) {
                        $updateCertificateIds[] = $this->certificateInterface->create($data);
                    }
                }
            }
            $deleteCertificateIds = array_diff($currentCertificateIds, $updateCertificateIds);
            foreach ($deleteCertificateIds as $deleteCertificateId) {
                $this->certificateInterface->delete($deleteCertificateId);
            }

        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Create certificate for CMS
     *
     * @param $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($request, $userId)
    {
        try {
            $auth = auth()->user();
            $user = $this->userInterface->find($userId);

            if (!_hasPermission($auth, $user)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }

            $degreeDate = $this->degreeDate($request->degree_date);

            $data = [
                'name' => $request->name,
                'degree_date' => $degreeDate,
                'user_id' => $userId,
            ];

            $existCertificate = $this->certificateInterface->existCertificate($userId, $request->name, $degreeDate);
            if (!$existCertificate) {
                $this->certificateInterface->create($data);
                return _success(null, __('message.add_certificate_success'), HTTP_SUCCESS);
            } else {
                return _error(null, __('message.certificate_already_exists'), HTTP_SUCCESS);
            }

        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Show certificate for CMS
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $auth = auth()->user();

            $certificate = $this->certificateInterface->find($id);
            if (!$certificate) {
                return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
            }

            $userId = $certificate->user_id;
            $user = $this->userInterface->find($userId);

            if (!_hasPermission($auth, $user)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }

            return _success($certificate, __('message.show_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Update certificate CMS
     *
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function update($request, $id)
    {
        try {
            $auth = auth()->user();

            $certificate = $this->certificateInterface->find($id);
            if (!$certificate) {
                return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
            }

            $userId = $certificate->user_id;
            $user = $this->userInterface->find($userId);

            if (!_hasPermission($auth, $user)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }

            $degreeDate = $this->degreeDate($request->degree_date);

            $existCertificate = $this->certificateInterface->existListCertificate($userId, $request->name, $degreeDate, $id);
            if (count($existCertificate) < EXIST_CERTIFICATE) {
                $data = [
                    'name' => $request->name,
                    'degree_date' => $degreeDate,
                    'user_id' => $userId,
                ];
                $this->certificateInterface->update($id, $data);
                return _success(null, __('message.updated_success'), HTTP_SUCCESS);
            } else {
                return _error(null, __('message.certificate_already_exists'), HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Delete certificate for CMS
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $auth = auth()->user();

            $certificate = $this->certificateInterface->find($id);
            if (!$certificate) {
                return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
            }

            $userId = $certificate->user_id;
            $user = $this->userInterface->find($userId);

            if (!_hasPermission($auth, $user)) {
                return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
            }

            $this->certificateInterface->delete($id);
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }



    /**
     * @param $degreeDate
     * @return DateTime
     * @throws Exception
     */
    protected function degreeDate($degreeDate)
    {
        $year = substr($degreeDate, 0, -3);

        if (strlen($year) == 4) {
            $degreeDate = new DateTime($degreeDate);
        } else if (strlen($year) == 3) {
            $degreeDate = new DateTime('0' . $degreeDate);
        } else if (strlen($year) == 2) {
            $degreeDate = new DateTime('00' . $degreeDate);
        } else {
            $degreeDate = new DateTime('000' . $degreeDate);
        }
        return $degreeDate;
    }
}
