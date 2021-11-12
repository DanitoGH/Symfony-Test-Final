<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Status;
use App\Utility\OrderStatuses;
use App\CustomEvents\OrderStatusEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderController extends AbstractApiController
{
    public function createAction(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->buildForm(OrderFormType::class);
        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }
        
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager();
        
        $status = $em->find(Status::class, OrderStatuses::ORDER_RECEIVED);
        if(!$status) throw new NotFoundHttpException('Status not found');
    
        $order = $form->getData();
        $order->setStatus($status);
        $em->persist($order);
        $em->flush();
        
        // creates the OrderStatusEvent and dispatches it
        $orderStatusEvent = new OrderStatusEvent($order);
        $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);

        return $this->respond($order);
    }
    
    public function cancelAction(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
       if(!$request->isMethod('PATCH'))throw new BadRequestHttpException();
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager();
        
        $orderId = $request->get('orderId');
        $order = $em->getRepository(Order::class)->findOneBy([
          'order_id' => $orderId
        ]);
        if(!$order) throw new NotFoundHttpException('Order not found');

        $status = $em->find(Status::class, OrderStatuses::ORDER_CANCELED);
        if(!$status) throw new NotFoundHttpException('Status not found');
        
        $order->setStatus($status);
        $em->persist($order);
        $em->flush();
        
        // creates the OrderStatusEvent and dispatches it
        $orderStatusEvent = new OrderStatusEvent($order);
        $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);

        return $this->respond($order);
    }
    
}