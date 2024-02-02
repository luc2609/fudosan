<?php

namespace App\Repositories\PasswordReset;

use App\Models\PasswordReset;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Str;

class PasswordResetEloquentRepository extends BaseEloquentRepository implements PasswordResetRepositoryInterface
{
    public function getModel()
    {
        return PasswordReset::class;
    }

    public function updateOrCreate($email)
    {
        $passwordReset = $this->_model->where('email', $email)->first();

        $token = Str::random(60);

        if (!$passwordReset) {
            $this->_model->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);
        } else {
            $this->_model->where('email', $email)->update(
                [
                    'token' => $token,
                    'created_at' => now()
                ]
            );
        }

        return $this->_model->where('email', $email)->first();
    }

    public function findOneByToken($token)
    {
        return $this->_model->where('token', $token)->first();
    }

    public function deleteByEmail($email)
    {
        return $this->_model->where('email', $email)->delete();
    }
}
