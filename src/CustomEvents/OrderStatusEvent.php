<?php  

namespace App\CustomEvents; 

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;


class OrderStatusEvent  extends Event
{
    public const NAME = 'order_status';
    protected $order_status;
    
    public function __construct(Order $order)
    {
        $this->order_status = $order;
    }
    
    public function getOrderStatus()
    {
        return $this->order_status;
    }
}