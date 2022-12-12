<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\admin\{Setting};
use Helper;

class NotesNotify extends Mailable
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
        //return $this->view('view.name');
        $address = env("MAIL_FROM_ADDRESS");
        //$address = Helper::getAdminEmail();
        $subject = 'New Notes Add';
        $name = 'Vivid';
        return $this->markdown('Email.NotesNotify')->from($address, $name) ->subject($subject)->with('mailData', $this->mailData);
    }
}
