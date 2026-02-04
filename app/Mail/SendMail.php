<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $subject;
    public $attach_file;
    public $from_email;
    public $from_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $adminAuth=\Auth::guard('admin')->user();
        $this->details = $details;
        $this->subject = $details['subject'];
        $this->attach_file = array_key_exists('attach_file', $details) ? $details['attach_file'] : null;
        $this->from_email = array_key_exists('from_email', $details) ? $details['from_email'] : $adminAuth->email;
        $this->from_name = array_key_exists('from_name', $details) ? $details['from_name'] : $adminAuth->firstname.' '.$adminAuth->lastname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->attach_file){
            return $this->from($this->from_email,$this->from_name)->attach('laravel/storage/app/public/images/'.$this->attach_file)->subject($this->subject)->view('emails.mail');
        }else{
            return $this->from($this->from_email,$this->from_name)->subject($this->subject)->view('emails.mail');
        }
    }
}
