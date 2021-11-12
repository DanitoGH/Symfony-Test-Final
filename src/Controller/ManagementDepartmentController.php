<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderStatusLogger;
use App\Entity\OrderExtraInfo;
use App\Form\ManagementSearchFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ManagementDepartmentController extends AbstractController
{
    // Order data pagination constants
    const PAGINATION_PAGE_NUMBER = 1;
    const PAGINATION_LIMIT_PER_PAGE = 15;

    /**
     * @Route("/management/department", name="management_department")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {   
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager(); 
    
        $orders = $em->getRepository(Order::class)->findAll();
        
        $form = $this->createForm(ManagementSearchFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           $orderId = $form->get('order_id')->getData();
           // fetch order where order ID equals user search term
           $orders = $em->getRepository(Order::class)->findBy(['order_id' => $orderId]);
        }
        
        // paginate query results
        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', self::PAGINATION_PAGE_NUMBER), /*page number*/
            self::PAGINATION_LIMIT_PER_PAGE /*limit per page*/
        );

        return $this->render('management_department/index.html.twig', [
            'department_name' => 'Management',
            'orders' => $pagination,
            'searchForm' => $form->createView(),
        ]);
    }
    
     /**
     * @Route("/management/order-details/{id}", name="management_department_order_details")
     */
    public function showManagementDepartmentDetailsAction($id): Response
    {  
        // instantiate entity manager
        $em = $this->getDoctrine()->getManager(); 
        
        // fetch selected order details info
        $order = $em->getRepository(Order::class)->findOneBy(['id' => $id]);
        
        // fetch extra info data associated with this order Id
        $orderExtraInfo = $em->getRepository(OrderExtraInfo::class)
        ->findOneBy(['order_id' => $order->getOrderId()]);
        
         // fetch selected order status change logs
        $orderStatusLogs = $em->getRepository(OrderStatusLogger::class)->findBy(['order_id' => $order->getOrderId()]);
        
        return $this->render('management_department/order_details.html.twig', [
            'department_name' => 'Management',
            'order' => $order,
            'order_extra_info' => $orderExtraInfo,
            'order_status_logs' => $orderStatusLogs,
        ]);
    }
}