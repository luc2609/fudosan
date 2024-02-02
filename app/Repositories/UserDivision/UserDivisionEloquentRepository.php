<?php

namespace App\Repositories\UserDivision;

use App\Models\UserDivision;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class UserDivisionEloquentRepository extends BaseEloquentRepository implements UserDivisionRepositoryInterface
{
    public function getModel()
    {
        return UserDivision::class;
    }

    public function getManagerListOfDivision($divisionId, $params)
    {
        $url = env('AWS_URL') . '/';
        $query = $this->_model->select([
            'users.id', 'email', 'phone',
            DB::raw('CONCAT(last_name, " " , first_name) as username'),
            DB::raw('CONCAT(kana_last_name, " ", kana_first_name) as furigana'),
            DB::raw('CONCAT("' . $url . '", users.avatar) as avatar'),
            'master_positions.name as position_name'
        ])
            ->where('user_roles.role_id', MANAGER)
            ->where('division_id', $divisionId)
            ->leftJoin('users', 'users.id', 'user_divisions.user_id')
            ->leftJoin('master_positions', 'users.position', 'master_positions.id')
            ->leftJoin('user_roles', 'users.id', 'user_roles.user_id');
        if (isset($params['name'])) {
            $name = _trimSpace($params['name']);
            $query->where(DB::raw('concat(last_name, " ", first_name)'), 'like BINARY', "%" . $name . "%");
        }
        return $query->orderBy('user_divisions.id', 'DESC');
    }

    public function deleteDivision($userId, $divisionId)
    {
        return $this->_model->where('user_id', $userId)->where('division_id', $divisionId)->delete();
    }
}
