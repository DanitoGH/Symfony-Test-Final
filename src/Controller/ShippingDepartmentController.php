<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Status;
use App\Entity\OrderExtraInfo;
use App\Form\OrderExtraInfoFormType;
use App\Utility\OrderStatuses;
use App\CustomEvents\OrderStatusEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShippingDepartmentController extends AbstractController
{
    /**
     * @Route("/shipping/department", name="shipping_department")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager(); 
        
        $orders = $em->getRepository(Order::class)->findBy(['status' => OrderStatuses::ORDER_READY_TO_SHIP]);
    
        return $this->render('shipping_department/index.html.twig', [
            'department_name' => 'Shipping',
            'orders' => $orders 
        ]);
    }
  
    /**
     * @Route("/shipping/order-details/{id}", name="shipping_department_order_details")
    */
    public function showShippingDepartmentDetailsAction(Request $request, $id, EventDispatcherInterface $eventDispatcher): Response
    {   
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager(); 
        
        $order = $em->getRepository(Order::class)->findOneBy(['id' => $id]);
        
        // fetch extra info data associated with this order ID
        $orderExtraInfo = $em->getRepository(OrderExtraInfo::class)
        ->findOneBy(['order_id' => $order->getOrderId()]);
        
        $form = $this->createForm(OrderExtraInfoFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // fetch order status data where status = ORDER_SHIPPED
            $status = $em->find(Status::class, OrderStatuses::ORDER_SHIPPED);
            // update order status to ORDER_SHIPPED
            $order->setStatus($status);
            
            $shipperFormData = $form->getData();
            // save order extra info report
            $orderExtraInfo->setShippingCompany($shipperFormData->getShippingCompany());
            $orderExtraInfo->setTrackingNumber($shipperFormData->getTrackingNumber());
            $orderExtraInfo->setImageFile($shipperFormData->getImageFile());
             
             $em->persist($orderExtraInfo);
             $em->persist($order);
             $em->flush();
             
             // send success flash message
             $this->addFlash('success', 'Order status successfully updated to SHIPPED');
             
             // creates the OrderStatusEvent and dispatches it
             $orderStatusEvent = new OrderStatusEvent($order);
             $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);
             
             return $this->redirectToRoute('shipping_department');
        }
        
        return $this->render('shipping_department/order_details.html.twig', [
          'department_name' => 'Shipping',
          'order' => $order,
          'order_extra_info' => $orderExtraInfo,
          'shipOrderForm' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/shipping/issues/report/{order_id}", name="shipping_issues_report")
     * @Method("POST")
    */
    public function shippingIssuesReportAction(Request $request, $order_id, EventDispatcherInterface $eventDispatcher): Response
    {
         // extract values from request
         $reason = $request->get('reason');
         $details = $request->get('details');
         
         // instantiate entity manager
         $em = $this->getDoctrine()->getManager(); 
        
         $order = $em->getRepository(Order::class)->findOneBy(['order_id' => $order_id]);
         
         // fetch order status data where status = ORDER_PROCESSING
         $status = $em->find(Status::class, OrderStatuses::ORDER_PROCESSING);
         
         $orderExtraInfo = $em->getRepository(OrderExtraInfo::class)->findOneBy(['order_id' => $order_id]);
         // Save order extra info report
         $orderExtraInfo->setIssue($reason);
         $orderExtraInfo->setIssueDetails($details);
         
         // Update order status
         $order->setStatus($status);
         
         $em->persist($orderExtraInfo);
         $em->persist($order);
         $em->flush();
         
         // send success flash message
         $this->addFlash('success', 'Order report successfully sent');
         
         // creates the OrderStatusEvent and dispatches it
         $orderStatusEvent = new OrderStatusEvent($order);
         $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);
         
         return $this->redirectToRoute('shipping_department');
    }
    
}