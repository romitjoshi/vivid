<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\admin\{Setting};
use Helper;

class PublisherApproved extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = env("MAIL_FROM_ADDRESS");
        //$address = Helper::getAdminEmail();
        $subject = 'You’re Approved!  🎉';
        $name = 'Vivid';
        return $this->markdown('Email.PublisherApproved')->from($address, $name) ->subject($subject)->with('mailData', $this->mailData);
    }
}
