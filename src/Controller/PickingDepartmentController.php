<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Status;
use App\Entity\OrderExtraInfo;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Utility\OrderStatuses;
use App\CustomEvents\OrderStatusEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PickingDepartmentController extends AbstractController
{

    // Order data pagination constants
    const PAGINATION_PAGE_NUMBER = 1;
    const PAGINATION_LIMIT_PER_PAGE = 3;
    
    /**
     * @Route("/picking/department", name="picking_department")
     */
    public function index(OrderRepository $orderRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $orders = $orderRepository->createQueryBuilder('o')
        ->andWhere('o.status = :orderReceived OR o.status = :orderProcessing')
        ->setParameter('orderReceived', OrderStatuses::ORDER_RECEIVED)
        ->setParameter('orderProcessing', OrderStatuses::ORDER_PROCESSING)
        ->getQuery()
        ->getResult();
        
        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', self::PAGINATION_PAGE_NUMBER), /*page number*/
            self::PAGINATION_LIMIT_PER_PAGE /*limit per page*/
        );
        
        return $this->render('picking_department/index.html.twig', [
          'department_name' => 'Picking',
          'orders' => $pagination
        ]);
    }

    /**
     * @Route("/picking/order-details/{id}", name="picking_department_order_details")
     */
    public function showPickingDepartmentDetailsAction($id): Response
    {  
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager(); 
        
        $order = $em->getRepository(Order::class)->findOneBy(['id' => $id]);
        
        // fetch extra info data associated with this order ID
        $orderExtraInfo = $em->getRepository(OrderExtraInfo::class)
        ->findOneBy(['order_id' => $order->getOrderId()]);
        
        return $this->render('picking_department/order_details.html.twig', [
            'department_name' => 'Picking',
            'order' => $order,
            'order_extra_info' => $orderExtraInfo
        ]);
    }
    
    /**
     * @Route("/picking/products/{id}", name="picking_products")
     */
    public function pickingProductsAction($id, EventDispatcherInterface $eventDispatcher): Response
    {
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager(); 
        
        $order = $em->getRepository(Order::class)->findOneBy(['id' => $id]);
        
        // fetch order status data where status = ORDER_PROCESSING
        $status = $em->find(Status::class, OrderStatuses::ORDER_PROCESSING);
        
        // update order status to ORDER_PROCESSING
        $order->setStatus($status);
        $em->persist($order);
        $em->flush();
        
        // send success flash message
        $this->addFlash('success', 'Order status successfully updated to PROCESSING');
        
        // creates the OrderStatusEvent and dispatches it
        $orderStatusEvent = new OrderStatusEvent($order);
        $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);

       return $this->redirectToRoute('picking_department');
    }
    
    /**
     * @Route("/picking/products/complete/{order_id}", name="picking_products_complete")
     * @Method("POST")
    */
    public function pickingProductsCompleteAction(Request $request, $order_id, EventDispatcherInterface $eventDispatcher): Response
    {
        // extract box id value from request
        $boxId = $request->get('box_id');
        
        $em = $this->getDoctrine()->getManager(); 
        
        $order = $em->getRepository(Order::class)->findOneBy(['order_id' => $order_id]);
        
        // fetch order status data where status = ORDER_READY_TO_SHIP
        $status = $em->find(Status::class, OrderStatuses::ORDER_READY_TO_SHIP);
        
        $orderExtraInfo = $em->getRepository(OrderExtraInfo::class)->findOneBy(['order_id' => $order_id]);
        
        // if orderExtraInfo data doesn't exist instantiate it otherwise update
         if(!$orderExtraInfo){
            $orderExtraInfo = new OrderExtraInfo();
            $orderExtraInfo->setOrderId($order_id);
         }
         $orderExtraInfo->setBoxId($boxId);
         
         // update order status to ORDER_READY_TO_SHIP
         $order->setStatus($status);
         
         $em->persist($orderExtraInfo);
         $em->persist($order);
         $em->flush();
         
         // send success flash message
         $this->addFlash('success', 'Order status successfully updated to READY TO SHIP');
         
         // creates the OrderStatusEvent and dispatches it
         $orderStatusEvent = new OrderStatusEvent($order);
         $eventDispatcher->dispatch($orderStatusEvent, OrderStatusEvent::NAME);
         
         return $this->redirectToRoute('picking_department');
    }
    
}