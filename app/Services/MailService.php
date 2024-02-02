<?php

namespace App\Services;

use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class MailService
{
    /**
     * Send simple mail
     * @param $mailTo - Mail send to
     * @param $data - Data to send
     * @param $subject
     * @param $view - Name view template of mail
     * @return bool
     */
    public function sendEmail($mailTo, $data, $subject, $view)
    {
        Log::info('SEND MAIL SUCCESS: 「' . $subject . '」 for ' . $mailTo);
        Mail::to($mailTo)->send(new SendMail($subject, $data, $view));

        if (count(Mail::failures()) > 0) {
            foreach (Mail::failures() as $emailAddress) {
                Log::warning('SEND MAIL FAIL: 「' . $subject . '」 : ' . $mailTo);
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * Send mail attach file
     */
    public function sendEmailAttachFile($mailTo, $data, $title, $view)
    {
        // TODO: Send email attach file
    }
}
