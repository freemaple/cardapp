<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Libs\Service\OrderService;

class OrderPaid extends Mailable
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
        $order = $this->data['order'];
        $order = OrderService::getOrderDetail($order);
        $this->data['order'] = $order;
        return $this->view('emails.order.paid',$this->data)->subject('Your '.$this->data['site_name'].' Order');
    }
}
