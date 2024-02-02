<?php

namespace App\Repositories\Contact;

use App\Models\Contact;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class ContactEloquentRepository extends BaseEloquentRepository implements ContactRepositoryInterface
{
    public function getModel()
    {
        return Contact::class;
    }

    public function getList($request)
    {
        $url = env('AWS_URL') . '/';
        $query = $this->_model
            ->leftJoin('users', 'users.id', 'contacts.user_id')
            ->leftJoin('master_contact_reasons', 'master_contact_reasons.id', 'contacts.type_id')
            ->select(
                'contacts.id',
                'contacts.type_id',
                'contacts.contents',
                'contacts.updated_at',
                'contacts.status',
                'master_contact_reasons.name as title',
                'contacts.user_id',
                'users.first_name',
                'users.last_name',
                'users.kana_first_name',
                'users.kana_last_name',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as username'),
                DB::raw('CONCAT(users.kana_last_name, " ", users.kana_first_name) as furigana'),
                'users.email',
                DB::raw('CONCAT("' . $url . '", users.avatar) as avatar'),


            );
        if ($request->keyword) {
            $query->where('users.email', 'like BINARY', '%' . $request->keyword . '%');
        }
        if ($request->status) {
            $query->where('contacts.status', $request->status);
        }
        return $query->orderBy('contacts.id', 'DESC');
    }

    public function showContact($id)
    {
        $url = env('AWS_URL') . '/';
        return $this->_model->where('contacts.id', $id)
            ->leftJoin('users', 'users.id', 'contacts.user_id')
            ->leftJoin('master_positions', 'master_positions.id', 'users.position')
            ->leftJoin('user_roles', 'users.id', 'user_roles.role_id')
            ->leftJoin('roles', 'roles.id', 'user_roles.role_id')
            ->leftJoin('companies', 'companies.id', 'users.company')

            ->leftJoin('master_contact_reasons', 'master_contact_reasons.id', 'contacts.type_id')
            ->select(
                'contacts.id',
                'master_contact_reasons.name as title',
                'contacts.type_id',
                'contacts.contents',
                'contacts.updated_at',
                'contacts.status',
                'contacts.user_id',
                'users.first_name',
                'users.last_name',
                'users.kana_first_name',
                'users.kana_last_name',
                DB::raw('CONCAT(users.last_name, " ", users.first_name) as username'),
                DB::raw('CONCAT(users.kana_last_name, " ", users.kana_first_name) as furigana'),
                'users.email',
                DB::raw('CONCAT("' . $url . '", users.avatar) as avatar'),
                'master_positions.name as position_name',
                'roles.name as role_name',
                'companies.name as company_name',

            )
            ->first();
    }
}
