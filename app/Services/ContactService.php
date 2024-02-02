<?php

namespace App\Services;

use App\Repositories\Contact\ContactRepositoryInterface;
use Illuminate\Http\Request;

class ContactService
{
    protected $contactInterface;
    protected $mailService;

    public function __construct(
        ContactRepositoryInterface $contactInterface,
        MailService $mailService
    ) {
        $this->contactInterface = $contactInterface;
        $this->mailService = $mailService;
    }

    public function create(Request $request)
    {
        $params = [
            'user_id' => auth()->user()->id,
            'type_id' => $request->type_id,
            'subject' => $request->subject,
            'contents' => $request->contents,
        ];
        $contact = $this->contactInterface->create($params);
        if ($contact) {
            $this->mailService->sendEmail(env('SYSTEM_MAIL'), $params, __('text.email_contact'), 'mail.send_contact');
            return _success($contact, __('message.created_success'), HTTP_SUCCESS);
        } else {
            return _error(null, __('message.created_fail'), HTTP_SUCCESS);
        }
    }

    public function list($request)
    {
        $pageSize = $request->page_size ?? PAGE_SIZE;
        $contacts =  $this->contactInterface->getList($request)->paginate($pageSize);
        $data = [
            'contacts' => $contacts->items(),
            'items_total' => $contacts->total()
        ];

        return _success($data, __('message.get_list_success'), HTTP_SUCCESS);
    }

    public function showContact($id)
    {
        $contact = $this->contactInterface->showContact($id);
        if (!$contact) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        }

        return _success($contact, __('message.show_success'), HTTP_SUCCESS);
    }

    public function deleteContact($id)
    {
        $contact = $this->contactInterface->find($id);
        if (!$contact) {
            return _error(null, __('message.not_found'), HTTP_NOT_FOUND);
        }

        $this->contactInterface->delete($id);
        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }
}
