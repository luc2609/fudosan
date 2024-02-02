<?php

namespace App\Repositories\Contact;

use App\Repositories\Base\BaseRepositoryInterface;

interface ContactRepositoryInterface extends BaseRepositoryInterface
{
    public function getList($request);

    public function showContact($id);
}
