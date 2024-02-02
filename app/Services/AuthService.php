<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\PasswordSecurity\PasswordSecurityRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserColor\UserColorRepositoryInterface;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;


class AuthService
{
    protected $userInterface;
    protected $passwordSecurityInterface;
    protected $passwordResetInterface;
    protected $divisionInterface;
    protected $userDeviceTokenInterface;
    protected $mailService;
    protected $fileService;
    protected $userColorRepositoryInterface;

    public function __construct(
        UserRepositoryInterface $userInterface,
        PasswordSecurityRepositoryInterface $passwordSecurityInterface,
        PasswordResetRepositoryInterface $passwordResetInterface,
        DivisionRepositoryInterface $divisionInterface,
        UserDeviceTokenRepositoryInterface $userDeviceTokenInterface,
        MailService $mailService,
        FileService $fileService,
        UserColorRepositoryInterface $userColorRepositoryInterface
    ) {
        $this->userInterface = $userInterface;
        $this->passwordSecurityInterface = $passwordSecurityInterface;
        $this->passwordResetInterface = $passwordResetInterface;
        $this->divisionInterface = $divisionInterface;
        $this->userDeviceTokenInterface = $userDeviceTokenInterface;
        $this->fileService = $fileService;
        $this->mailService = $mailService;
        $this->userColorRepositoryInterface = $userColorRepositoryInterface;
    }

    /**
     * Login user
     *
     * @param $credentials
     * @return \Illuminate\Http\JsonResponse
     */
    protected function login($credentials)
    {
        auth()->attempt($credentials);
        $user = auth()->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $data['access_token'] = $tokenResult->accessToken;
        $data['token_type'] = 'Bearer';
        $userId = $user->id;
        $attribute['last_login'] = now();
        $this->userInterface->update($userId, $attribute);
        return _success($data, __('message.login_success'), HTTP_SUCCESS);
    }

    /**
     * Login user with email, password
     *
     * @param $credentials
     * @param $system
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginWithEmail($credentials, $system)
    {
        try {
            // Get user information and check password is correct or not
            $user = $this->userInterface->findOneByEmail($credentials['email']);
            $checkPassword = Hash::check($credentials['password'], $user->password);

            if (!$checkPassword) {
                return _error(null, __('message.current_password_incorrect'), HTTP_SUCCESS);
            }

            // If email and password is correct, check permission of account
            $permission = $this->checkPermissionLogin($user, $system);
            if (!$permission) {
                return _error(null, __('message.no_permission'), HTTP_SUCCESS);
            }

            // If permission and account is correct, create token
            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($user->id, TOKEN_LOGIN_TYPE);

            $token = $this->generateToken();

            if (is_null($passwordSecurity)) {
                $this->passwordSecurityInterface->create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'type' => TOKEN_LOGIN_TYPE
                ]);
            } else {
                if (!$passwordSecurity->security_enable && $system == APP) {
                    return $this->login($credentials);
                }

                if (!$passwordSecurity->cms_security_enable && $system == CMS_COMPANY) {
                    return $this->login($credentials);
                }

                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'token' => $token,
                        'active' => true,
                        'incorrect' => 0
                    ]
                );
            }

            // update time login last
            $userId = $user->id;


            if ($user->authentication == ACTIVE) {
                // Send email login token
                $this->mailService->sendEmail(
                    $user->email,
                    ['token' => $token],
                    __('text.email_login_verify_token'),
                    'mail.login_verify_token'
                );
                return _success(null, __('message.general_token_login_success'), HTTP_SUCCESS);
            } else {
                $tokenResult = $user->createToken('Personal Access Token');
                $dataLogin = [
                    "access_token" => $tokenResult->accessToken,
                    "token_type" => "Bearer"
                ];
                $attribute['last_login'] = now();
                $this->userInterface->update($userId, $attribute);
                return _success($dataLogin, __('message.login_success'), HTTP_SUCCESS);
            }
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Verify token login
     *
     * @param $credentials
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTokenLogin($credentials, $token)
    {
        try {
            $user = $this->userInterface->findOneByEmail($credentials['email']);
            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($user->id, TOKEN_LOGIN_TYPE);

            if (is_null($passwordSecurity)) {
                return _error(null, __('message.verify_token_error'), HTTP_SUCCESS);
            }

            if (!$passwordSecurity->active) {
                return _error(null, __('message.verify_token_inactive'), HTTP_SUCCESS);
            }

            if ($token != $passwordSecurity->token) {
                $incorrect = (int) $passwordSecurity->incorrect + 1;

                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'active' => ($incorrect >= INCORRECT_TOKEN_LIMIT) ? INACTIVE : ACTIVE,
                        'incorrect' => $incorrect
                    ]
                );

                // Send mail notice the code entered is wrong by more than 3 times
                if ($incorrect >= INCORRECT_TOKEN_LIMIT) {
                    $data['token'] = $passwordSecurity->token;
                    $this->mailService->sendEmail(
                        $user->email,
                        $data,
                        __('text.the_verification_code_has_expired'),
                        'mail.verification_code_has_expired'
                    );
                }
                return _error(null, __('message.verify_token_incorrect'), HTTP_SUCCESS);
            } else {
                return $this->login($credentials);
            }
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    // Resend token login
    public function resendToken($email)
    {
        try {
            $user = $this->userInterface->findOneByEmail($email);
            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($user->id, TOKEN_LOGIN_TYPE);

            $token = $this->generateToken();
            if (is_null($passwordSecurity)) {
                $this->passwordSecurityInterface->create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'type' => TOKEN_LOGIN_TYPE
                ]);
            } else {
                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'token' => $token,
                        'active' => true,
                        'incorrect' => 0
                    ]
                );
            }

            // Send email token login
            $this->mailService->sendEmail(
                $user->email,
                ['token' => $token],
                __('text.email_login_verify_token'),
                'mail.login_verify_token'
            );

            return _success(null, __('message.resend_verify_token'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Send token reset password
     *
     * @param $email
     * @param $system
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTokenResetPassword($email, $system)
    {
        try {
            $user = $this->userInterface->findOneByEmail($email);
            $userId = $user->id;

            $permission = $this->checkPermissionResetPassword($user, $system);
            if (!$permission) {
                return _error(null, __('message.email_incorrect'), HTTP_SUCCESS);
            }

            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($userId, TOKEN_RESET_PASS_TYPE);

            $token = $this->generateToken();
            if (is_null($passwordSecurity)) {
                $this->passwordSecurityInterface->create([
                    'user_id' => $userId,
                    'token' => $token,
                    'type' => TOKEN_RESET_PASS_TYPE
                ]);
            } else {
                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'token' => $token,
                        'active' => true,
                        'incorrect' => 0
                    ]
                );
            }

            // Send email token reset password
            $data = [
                'token' => $token,
                'username' => $user->username,
            ];

            $this->mailService->sendEmail(
                $user->email,
                $data,
                __('text.email_reset_password_verify_token'),
                'mail.reset_password_verify_token'
            );

            return _success(null, __('message.resend_verify_token'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Generate token authentication
     *
     * @return int
     */
    protected function generateToken()
    {
        return rand(100000, 999999);
    }

    /**
     * Verify token reset password
     *
     * @param $email
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTokenResetPassword($email, $token)
    {
        try {
            $user = $this->userInterface->findOneByEmail($email);
            $userId = $user->id;

            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($userId, TOKEN_RESET_PASS_TYPE);
            if (!$passwordSecurity) {
                return _error(null, __('message.verify_token_error'), HTTP_SUCCESS);
            }

            if (!$passwordSecurity->active) {
                return _error(null, __('message.verify_token_inactive'), HTTP_SUCCESS);
            }

            if ($token != $passwordSecurity->token) {
                $incorrect =  (int) $passwordSecurity->incorrect + 1;

                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'active' => ($incorrect >= INCORRECT_TOKEN_LIMIT) ? INACTIVE : ACTIVE,
                        'incorrect' => $incorrect
                    ]
                );

                return _error(null, __('message.verify_token_incorrect'), HTTP_SUCCESS);
            } else {
                $passwordReset = $this->passwordResetInterface->updateOrCreate($email);
                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    ['active' => INACTIVE]
                );

                $data = ['token' => $passwordReset->token];

                return _success($data, __('message.token_is_return'), HTTP_SUCCESS);
            }
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Reset password
     *
     * @param $token
     * @param $password
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($token, $password)
    {
        try {
            $passwordReset = $this->passwordResetInterface->findOneByToken($token);
            if (!$passwordReset) {
                return _error(null, __('message.token_invalid'), HTTP_SUCCESS);
            }

            $email = $passwordReset->email;
            $user = $this->userInterface->findOneByEmail($email);
            if (!$user) {
                return _error(null, __('message.not_found'), HTTP_SUCCESS);
            }

            $this->userInterface->update($user->id, [
                'password' => $password
            ]);

            $this->passwordResetInterface->deleteByEmail($user->email);

            return _success(null, __('message.reset_password_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    public function checkPermissionLogin($user, $system)
    {
        $permissionSystem = [
            CMS_SYSTEM => LOGIN_CMS_SYSTEM,
            CMS_COMPANY => LOGIN_CMS_COMPANY,
            APP => LOGIN_APP
        ];

        return $user->hasPermissionInSystem($system, $permissionSystem);
    }

    public function checkPermissionResetPassword(User $user, $system)
    {
        $permissionSystem = [
            CMS_COMPANY => RESET_PASSWORD_CMS_COMPANY,
            APP => RESET_PASSWORD_APP
        ];

        return $user->hasPermissionInSystem($system, $permissionSystem);
    }

    /**
     * Get user configuration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function configuration()
    {
        $userId = auth()->user()->id;
        $user = $this->userInterface->find($userId);

        $data = [
            'push_notify_enable' => $user->push_notify_enable,
            'email_notify_enable' => $user->email_notify_enable,
            'security_enable' => $user->passwordSecurityTypeLogin->first()->security_enable
        ];

        return _success($data, __('message.get_configuration_success'), HTTP_SUCCESS);
    }

    /**
     * Update Security Enable
     *
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function securityEnable($params)
    {
        try {
            $system = $params['system'];
            $status = $params['status'];
            $userId = auth()->user()->id;
            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($userId, TOKEN_LOGIN_TYPE);

            switch ($system) {
                case APP:
                    $updateData = ['security_enable' => $status];
                    break;

                case CMS_COMPANY:
                    $updateData = ['cms_security_enable' => $status];
                    break;

                default:
                    $updateData = null;
                    break;
            }

            if ($updateData) {
                $result = $this->passwordSecurityInterface->update($passwordSecurity->id, $updateData);
                return _success($result, __('message.security_enable_updated'), HTTP_SUCCESS);
            } else {
                return _error(null, __('message.system_not_found'), HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Update push Notify Enable
     *
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushNotifyEnable($params)
    {
        try {
            $userId = auth()->user()->id;
            $result = $this->userInterface->update($userId, ['push_notify_enable' => $params['status']]);
            return _success($result, __('message.push_notify_enable_updated'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Update email Notify Enable
     *
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function emailNotifyEnable($params)
    {
        try {
            $userId = auth()->user()->id;
            $result = $this->userInterface->update($userId, ['email_notify_enable' => $params['status']]);

            return _success($result, __('message.email_notify_enable_updated'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Update password
     *
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword($params)
    {
        try {
            $password = $params['password'];
            $newPassword = $params['new_password'];
            $user = auth()->user();

            $checkPassword = Hash::check($password, $user->password);

            if (!$checkPassword) {
                return _error(null, __('message.current_password_incorrect'), HTTP_SUCCESS);
            }

            if ($password == $newPassword) {
                return _error(null, __('message.new_password_same_password'), HTTP_SUCCESS);
            }

            $user->password = $newPassword;
            $user->save();
            return _success(null, __('message.update_password_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Verify token reset password
     *
     * @param $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTokenUpdatePassword($params)
    {
        try {
            $newPassword = $params['new_password'];
            $token = $params['token'];
            $userId = auth()->user()->id;

            $passwordSecurity = $this->passwordSecurityInterface->findOneByUserId($userId, TOKEN_UPDATE_PASS_TYPE);
            if (!$passwordSecurity) {
                return _error(null, __('message.verify_token_error'), HTTP_SUCCESS);
            }

            if (!$passwordSecurity->active) {
                return _error(null, __('message.verify_token_inactive'), HTTP_SUCCESS);
            }

            if ($token != $passwordSecurity->token) {
                $incorrect =  (int) $passwordSecurity->incorrect + 1;

                $this->passwordSecurityInterface->update(
                    $passwordSecurity->id,
                    [
                        'active' => ($incorrect >= INCORRECT_TOKEN_LIMIT) ? INACTIVE : ACTIVE,
                        'incorrect' => $incorrect
                    ]
                );

                return _error(null, __('message.verify_token_incorrect'), HTTP_SUCCESS);
            }

            $this->passwordSecurityInterface->update($passwordSecurity->id, ['active' => INACTIVE]);
            $result = $this->userInterface->update($userId, ['password' => $newPassword]);

            return _success($result, __('message.updated_password_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Delete device token
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDeviceToken($request)
    {
        try {
            $user = auth()->user();
            if ($request->device_token) {
                $this->userDeviceTokenInterface->destroyDeviceToken($user->id, $request->device_token);
            }
            $request->user()->token()->revoke();
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    public function destroyDeviceTokenUser($id)
    {
        try {
            $user = $this->userInterface->find($id);
            $user->accessTokens()->delete();
            return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Create device token
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDeviceToken($request)
    {
        try {
            $user = auth()->user();
            $result = $this->userDeviceTokenInterface->checkDeviceToken($user->id, $request->device_token);
            if (!$result) {
                $result = $this->userDeviceTokenInterface->create(['user_id' => $user->id, 'device_token' => $request->device_token]);
            }
            return _success($result, __('message.created_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Get profile info
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        try {
            $userId = auth()->user()->id;
            $user = $this->userInterface->show($userId);
            $userColor = $this->userColorRepositoryInterface->getColorByUserID($userId);
            if ($userColor) {
                if ($userColor->color_app) {
                    $user['color_app'] = json_decode($userColor->color_app);
                } else {
                    $user['color_app'] = null;
                }
                if ($userColor->color_web) {
                    $user['color_web'] = json_decode($userColor->color_web);
                } else {
                    $user['color_web'] = null;
                }
            } else {
                $user['color_app'] = null;
                $user['color_web'] = null;
            }
            return _success($user, __('message.get_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }

    /**
     * Get user commission
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCommission()
    {
        try {
            $userId = auth()->user()->id;
            $data = [];
            $userCommission = $this->userInterface->getUserCommission($userId);

            foreach ($userCommission->projects as $userProject) {
                $commissionRate = $userProject->commission_rate;
                $project = [
                    'month' => $userProject->month,
                    'year' => $userProject->year,
                    'contract_count' => $userProject->project_count,
                    'revenue' => $userProject->revenue,
                    'commission' => $commissionRate ? ($userProject->revenue * $commissionRate) / 100 : $userProject->revenue
                ];
                $data[] = $project;
            }

            return _success($data, __('message.get_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e);
            return _errorSystem();
        }
    }
}
