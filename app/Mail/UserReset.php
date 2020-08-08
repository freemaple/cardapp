<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserReset extends Mailable
{
    use Queueable, SerializesModels;
	
    /**
     * 邮件模板的变量数据
     * @var array
     */
    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	return $this->view('emails.reset',$this->data)
        			->subject('Welcome to '.$this->data['site_name'].'!');
    }
}
