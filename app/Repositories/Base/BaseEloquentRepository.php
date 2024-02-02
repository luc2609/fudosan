<?php

namespace App\Repositories\Base;


abstract class BaseEloquentRepository implements BaseRepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $_model;

    /**
     * EloquentRepository constructor.
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * get model
     * @return string
     */
    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel()
    {
        $this->_model = app()->make($this->getModel());
    }

    /**
     * Get All
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->_model->all();
    }

    /**
     * Get All
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listMasterData()
    {
        return $this->_model->where('status', ACTIVE)->get();
    }

    /**
     * Get all data with soft deleted data
     *
     * @return mixed
     */
    public function allWithTrash()
    {
        return $this->_model->withTrashed()->all();
    }

    /**
     * Get all data with locale
     *
     * @return mixed
     */
    public function allLocale()
    {
        return $this->_model->withTranslation(app()->getLocale())->get();
    }

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->_model->find($id);
    }

    /**
     * Limit
     * @param $limit
     * @return mixed
     */
    public function limit($limit)
    {
        return $this->_model->limit($limit)->get();
    }

    /**
     * Insert
     * @param array $data
     * @return mixed
     */
    public function insert($data = [])
    {
        return $this->_model->insert($data);
    }

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = [])
    {
        return $this->_model->create($attributes);
    }

    /**
     * Create translate
     * @param array $attributes
     * @return mixed
     */
    public function createTrans($attributes = [])
    {
        return $this->_model->create($attributes);
    }

    /**
     * Update
     * @param $id
     * @param array $attributes
     * @return bool|mixed
     */
    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }
        return false;
    }

    /**
     * Update translate
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function updateTrans($id, $attributes = [])
    {
        $tableName = $this->_model->getTable();
        $idColumn = str_replace('trans', 'id', $tableName);
        $fillableColumn = $this->_model->getFillable();
        if (in_array($idColumn, $fillableColumn)) {
            $result = $this->_model->where($idColumn, $id)->where('locale', app()->getLocale())->first();
            if ($result) {
                $result->update($attributes);
                return $result;
            }
            return false;
        }
        return false;
    }

    /**
     * Soft delete
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    /**
     * Restore deleted data
     *
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $result = $this->_model->withTrashed()->find($id);
        if ($result) {
            $result->restore();
            return true;
        }
        return false;
    }

    /**
     * Hard delete
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->forceDelete();
            return true;
        }
        return false;
    }

    /**
     * Get last item
     * @return mixed
     */
    public function last()
    {
        return $this->_model->orderBy('created_at', 'desc')->first();
    }

    /**
     * @param $sortBy
     * @param $orderBy
     * @param $perPage
     * @return mixed
     */
    public function paginate($perPage, $sortBy = 'created_at', $orderBy = 'DESC')
    {
        return $this->_model->orderBy($sortBy, $orderBy)->paginate($perPage);
    }

    /**
     * @param $perPage
     * @param $sortBy
     * @param $orderBy
     * @return mixed
     */
    public function paginateLocale($perPage, $sortBy = 'created_at', $orderBy = 'DESC')
    {
        return $this->_model->translateOrDefault(app()->getLocale())->orderBy($sortBy, $orderBy)->paginate($perPage);
    }

    /**
     * @param $sortBy
     * @param $orderBy
     * @return mixed
     */
    public function getDefaultMasterData($sortBy = 'code', $orderBy = 'ASC')
    {
        return $this->_model->where('locale', app()->getLocale())
            ->orderBy($sortBy, $orderBy)
            ->get();
    }

    public function checkExistMasterData($name, $id)
    {
        if ($id) {
            return $this->_model->where(
                [
                    ['name', 'like BINARY', $name],
                    ['status', ACTIVE],
                    ['id', '!=', $id],
                ]
            )->first();
        } else {
            return $this->_model->where(
                [
                    ['name', 'like BINARY', $name],
                    ['status', ACTIVE],
                ]
            )->exists();
        }
    }
}
