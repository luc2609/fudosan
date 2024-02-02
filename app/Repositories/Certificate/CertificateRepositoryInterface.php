<?php

namespace App\Repositories\Certificate;

use App\Repositories\Base\BaseRepositoryInterface;

interface CertificateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * List certificate
     * @param $id
     * @return mixed
     */
    public function index($id);

    /**
     * Get first certificate with degree date
     *
     * @param $userId
     * @param $name
     * @param $degreeDate
     * @return mixed
     */
    public function existCertificate($userId, $name, $degreeDate);

    /**
     * Get list certificate with degree date
     *
     * @param $userId
     * @param $name
     * @param $degreeDate
     * @param $id
     * @return mixed
     */
    public function existListCertificate($userId, $name, $degreeDate, $id);
}
