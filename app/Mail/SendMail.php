<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $data;
    public $view;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $view)
    {
        $this->subject = $subject;
        $this->data = $data;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view($this->view);
    }

    public function getSubject(){
        openPeriodSetFlg = {
            '0': '//div[@id="js-plan-form"]//input[@name="openPeriodSetFlg" and @value="0"]',
            '1': '//div[@id="js-plan-form"]//input[@name="openPeriodSetFlg" and @value="1"]'
        }
        # TODO openPeriodSetFlg == 1
        openPeriodStrDayYear = '//div[@id="js-plan-form"]//select[@name="openPeriodStrDayYear"]'
        openPeriodStrDayMonth = '//div[@id="js-plan-form"]//select[@name="openPeriodStrDayMonth"]'
        openPeriodStrDayDay = '//div[@id="js-plan-form"]//select[@name="openPeriodStrDayDay"]'
        openPeriodEndDayYear = '//div[@id="js-plan-form"]//select[@name="openPeriodEndDayYear"]'
        openPeriodEndDayMonth = '//div[@id="js-plan-form"]//select[@name="openPeriodEndDayMonth"]'
        openPeriodEndDayDay = '//div[@id="js-plan-form"]//select[@name="openPeriodEndDayDay"]'
        rsvEndDay = '//div[@id="js-plan-form"]//select[@name="rsvEndDay"]'
        rsvEndHhHour = '//div[@id="js-plan-form"]//select[@name="rsvEndHhHour"]'
        rsvEndHhMinits = '//div[@id="js-plan-form"]//select[@name="rsvEndHhMinits"]'
    
        settleFlg = {
            '0': '//div[@id="js-plan-form"]//input[@name="settleFlg" and @value="0"]',
            '1': '//div[@id="js-plan-form"]//input[@name="settleFlg" and @value="1"]',
            '2': '//div[@id="js-plan-form"]//input[@name="settleFlg" and @value="2"]'
        }
        # TODO settleFlg == 3
        canFlg = {
            '0': '//div[@id="js-plan-form"]//input[@name="canFlg" and @value="0"]',
            '1': '//div[@id="js-plan-form"]//input[@name="canFlg" and @value="1"]',
            '2': '//div[@id="js-plan-form"]//input[@name="canFlg" and @value="2"]'
        }
        # TODO canFlg == 1
        checkboxDpFlg = '//div[@id="js-plan-form"]//input[@name="checkboxDpFlg"]'
        canRule1SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule1SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule1SetFlg" and @value="0"]'
        }
        # TODO canRule1SetFlg == 1
        canRule1CanRate = '//div[@id="js-plan-form"]//input[@name="canRule1CanRate"]'
        # end canRule1SetFlg == 1
        canRule2SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule2SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule2SetFlg" and @value="0"]'
        }
        # TODO canRule2SetFlg == 1
        canRule2CanRate = '//div[@id="js-plan-form"]//input[@name="canRule2CanRate"]'
        # end canRule2SetFlg == 1
        canRule3SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule3SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule3SetFlg" and @value="0"]'
        }
        # TODO canRule3SetFlg == 1
        canRule3CanPeriodTo = '//div[@id="js-plan-form"]//input[@name="canRule3CanPeriodTo"]'
        canRule3CanPeriodFrom = '//div[@id="js-plan-form"]//input[@name="canRule3CanPeriodFrom"]'
        canRule3CanRate = '//div[@id="js-plan-form"]//input[@name="canRule3CanRate"]'
        # end canRule3SetFlg == 1
        canRule4SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule4SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule4SetFlg" and @value="0"]'
        }
        # TODO canRule4SetFlg == 1
        canRule4CanPeriodTo = '//div[@id="js-plan-form"]//input[@name="canRule4CanPeriodTo"]'
        canRule4CanPeriodFrom = '//div[@id="js-plan-form"]//input[@name="canRule4CanPeriodFrom"]'
        canRule4CanRate = '//div[@id="js-plan-form"]//input[@name="canRule4CanRate"]'
        # end canRule4SetFlg == 1
        canRule5SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule5SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule5SetFlg" and @value="0"]'
        }
        # TODO canRule5SetFlg == 1
        canRule5CanPeriodTo = '//div[@id="js-plan-form"]//input[@name="canRule5CanPeriodTo"]'
        canRule5CanPeriodFrom = '//div[@id="js-plan-form"]//input[@name="canRule5CanPeriodFrom"]'
        canRule5CanRate = '//div[@id="js-plan-form"]//input[@name="canRule5CanRate"]'
        # end canRule5SetFlg == 1
        canRule6SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule6SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule6SetFlg" and @value="0"]'
        }
        # TODO canRule6SetFlg == 1
        canRule6CanPeriodTo = '//div[@id="js-plan-form"]//input[@name="canRule6CanPeriodTo"]'
        canRule6CanPeriodFrom = '//div[@id="js-plan-form"]//input[@name="canRule6CanPeriodFrom"]'
        canRule6CanRate = '//div[@id="js-plan-form"]//input[@name="canRule6CanRate"]'
        # end canRule6SetFlg == 1
        canRule7SetFlg = {
            '1': '//div[@id="js-plan-form"]//input[@name="canRule7SetFlg" and @value="1"]',
            '0': '//div[@id="js-plan-form"]//input[@name="canRule7SetFlg" and @value="0"]'
        }
        # TODO canRule7SetFlg == 1
        canRule7CanPeriodTo = '//div[@id="js-plan-form"]//input[@name="canRule7CanPeriodTo"]'
        canRule7CanPeriodFrom = '//div[@id="js-plan-form"]//input[@name="canRule7CanPeriodFrom"]'
        canRule7CanRate = '//div[@id="js-plan-form"]//input[@name="canRule7CanRate"]'
    }
}
