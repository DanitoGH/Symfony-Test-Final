<?php

namespace App\EventListener;

use App\Entity\OrderExtraInfo;
use Psr\Log\LoggerInterface;
use App\CustomEvents\OrderStatusEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OrderStatusActivitySubscriber implements EventSubscriberInterface
{    
    const ORDER_RECEIVED = 'ORDER_RECEIVED';
    const ORDER_CANCELED = 'ORDER_CANCELED';
    const ORDER_PROCESSING = 'ORDER_PROCESSING';
    const ORDER_READY_TO_SHIP = 'ORDER_READY_TO_SHIP';
    const ORDER_SHIPPED = 'ORDER_SHIPPED';
    
    private $tokenStorage;
    private $logger;
    private $manager;
    private $params;
    
    public function __construct(TokenStorageInterface $tokenStorage, ParameterBagInterface $params, 
      LoggerInterface $ordersLogger, EntityManagerInterface $manager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $ordersLogger;
        $this->manager = $manager;
        $this->params = $params;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            OrderStatusEvent::NAME => 'onStatusChanged',
        ];
    }

    public function onStatusChanged(OrderStatusEvent $event)
    {
        $orderId = $event->getOrderStatus()->getOrderId();
        $user = $this->tokenStorage->getToken()->getUser();
        $userId = $this->tokenStorage->getToken()->getUser()->getId();
        $username = $user->getUserIdentifier();
        $status = $event->getOrderStatus()->getStatus();
        $orderStatus = $status->getStatus();
        $statusName = $status->getStatusName();
        
        // base url from config/services.yaml
        $parameterValue = $this->params->get('base_url');
 
         switch ($orderStatus) {
             case self::ORDER_RECEIVED:
                   $this->logActivity("Order #$orderId has been received by the system", $orderId, $status);
             case self::ORDER_CANCELED:
                   $this->logActivity("Order #$orderId has been cancelled by customer", $orderId, $status);
                  break;
             case self::ORDER_PROCESSING:
                    if($user->isShipper() && $orderStatus === 'ORDER_PROCESSING'){
                     // fetch order extra info data associated with this order
                      $orderExtraInfo = $this->manager->getRepository(OrderExtraInfo::class)->findOneBy(['order_id' => $orderId]);
                      $reportIssue = strtoupper($orderExtraInfo->getIssue());
                      $issueDetails = $orderExtraInfo->getIssueDetails();
                    
                      $this->logActivity("Order #$orderId has been changed to $statusName by
                       $username ($userId) with reason: $reportIssue ($issueDetails)", $orderId, $status);
                    }else{
                      $this->logActivity("Order #$orderId has been changed to $statusName by $username ($userId)", $orderId, $status);
                    }
                  break;
             case self::ORDER_READY_TO_SHIP:
                    // fetch order extra info data associated with this order
                    $orderExtraInfo = $this->manager->getRepository(OrderExtraInfo::class)->findOneBy(['order_id' => $orderId]);
                    $boxId = $orderExtraInfo->getBoxId();
                          
                     $this->logActivity("Order #$orderId has been changed to $statusName by $username ($userId) with BOX_ID: $boxId", $orderId, $status);
                  break;
             case self::ORDER_SHIPPED:
                    // fetch order extra info data associated with this order
                    $orderExtraInfo = $this->manager->getRepository(OrderExtraInfo::class)->findOneBy(['order_id' => $orderId]);
                    $trackingNumber = $orderExtraInfo->getTrackingNumber();
                    $imageFile = $orderExtraInfo->getImageName();
                    // custom file path
                     $filePath = "<a href='$parameterValue/files/labels/$imageFile' target='_blank'>[View Label]</a>";
                     
                    $this->logActivity("Order #$orderId has been changed to $statusName by $username ($userId) with AWB: #$trackingNumber by UPS $filePath", $orderId, $status);
                  break;
             default:
                 break;
         }
    }
    
    private function logActivity(string $message, $orderId,  $status): void
    {
        $this->logger->info($message, ['orderId' => $orderId, 'status' => $status]);
    }
}