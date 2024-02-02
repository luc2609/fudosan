<?php

namespace App\Repositories\Certificate;

use App\Models\Certificate;
use App\Repositories\Base\BaseEloquentRepository;

class CertificateEloquentRepository extends BaseEloquentRepository implements CertificateRepositoryInterface
{
    public function getModel()
    {
        return Certificate::class;
    }

    /**
     * List certificate
     * @param $id
     * @return mixed
     */
    public function index($id)
    {
        return $this->_model
            ->select('id', 'name', 'degree_date')
            ->where('user_id', $id);
    }

    /**
     * Get first certificate with degree date
     *
     * @param $userId
     * @param $name
     * @param $degreeDate
     * @return mixed
     */
    public function existCertificate($userId, $name, $degreeDate)
    {
        return $this->_model
            ->where([
                'user_id' => $userId,
                'name' => $name,
                'degree_date' => $degreeDate
            ])->first();
    }

    /**
     * Get list certificate with degree date
     *
     * @param $userId
     * @param $name
     * @param $degreeDate
     * @param $id
     * @return mixed
     */
    public function existListCertificate($userId, $name, $degreeDate, $id)
    {
        return $this->_model
            ->where([
                'user_id' => $userId,
                'name' => $name,
                'degree_date' => $degreeDate
            ])
            ->where('id', '<>', $id)
            ->get();
    }
}
