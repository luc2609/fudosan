<?php

namespace App\Repositories\Base;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Base
 */
interface BaseRepositoryInterface
{
    /**
     * Get all data without soft deleted data
     *
     * @return mixed
     */
    public function all();
    public function listMasterData();
    /**
     * Get all data with soft deleted data
     *
     * @return mixed
     */
    public function allWithTrash();

    /**
     * Get all data with locale
     *
     * @return mixed
     */
    public function allLocale();

    /**
     * Get one
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Limit
     *
     * @param $limit
     * @return mixed
     */
    public function limit($limit);

    /**
     * Create
     *
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = []);


    /**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data = []);

    /**
     * Create translate
     *
     * @param array $attributes
     * @return mixed
     */
    public function createTrans($attributes = []);

    /**
     * Update
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = []);

    /**
     * Update translate
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function updateTrans($id, $attributes = []);

    /**
     * Soft delete
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Restore deleted data
     *
     * @param $id
     * @return mixed
     */
    public function restore($id);

    /**
     * Hard delete
     *
     * @param $id
     * @return bool
     */
    public function destroy($id);

    /**
     * Get last item
     * @return mixed
     */
    public function last();

    /**
     * @param $sortBy
     * @param $orderBy
     * @param $perPage
     * @return mixed
     */
    public function paginate($perPage, $sortBy = 'created_at', $orderBy = 'DESC');

    /**
     * @param $perPage
     * @param $sortBy
     * @param $orderBy
     * @return mixed
     */
    public function paginateLocale($perPage, $sortBy = 'created_at', $orderBy = 'DESC');

    /**
     * @param $locale
     * @param $sortBy
     * @param $orderBy
     * @return mixed
     */
    public function getDefaultMasterData($sortBy = 'code', $orderBy = 'ASC');

    public function checkExistMasterData($name, $id);
}
