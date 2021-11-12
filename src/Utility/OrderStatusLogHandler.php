<?php

namespace App\Utility;

use App\Entity\OrderStatusLogger;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class OrderStatusLogHandler extends AbstractProcessingHandler
{
    private $manager;
    
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }
    
    protected function write(array $record): void
    {
        $orderStatusLogger = new OrderStatusLogger();
        $orderStatusLogger->setMessage($record['message']);
        $orderStatusLogger->setStatus($record['context']['status']);
        $orderStatusLogger->setOrderId($record['context']['orderId']);
        
        $this->manager->persist($orderStatusLogger);
        $this->manager->flush();
    }
}