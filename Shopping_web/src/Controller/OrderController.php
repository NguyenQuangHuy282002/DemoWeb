<?php

namespace App\Controller;

use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\DetailRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'Order')]
    public function index(): Response
    {   
        $orders = $this ->getDoctrine() -> getRepository(Order::class)->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    
    #[Route('/detail/{id}', name: 'Order_detail')]
    public function Order_detail($id)
    {   
    
        $order = $this ->getDoctrine() -> getRepository(Detail::class)->find($id);
        if ($order == null) {
            $this -> addFlash('error', 'Order not found');
            return $this -> redirectToRoute('Order');
        }
        return $this->render('order/detail.html.twig', [
            'order' => $order,
        ]);
    }

    //Add new product
    #[Route('/add', name: 'order_add')]
    public function AddProduct(Session $session, ProductRepository $PR, SessionInterface $si)
    {
        $order = new Order;
        $now = date("Y-m-d");
        $order -> setDate(\DateTime::createFromFormat('Y-m-d',$now));
        $manager = $this->getDoctrine() -> getManager();
        $manager ->persist($order);
        $manager -> flush();


        $cart = $si->get('cart', []);

        $cartWithData = [];        

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $PR->find($id),
                'quantity' => $quantity,
            ];
            $orderDetails = new Detail;
            foreach ($cartWithData as $item) {
                $orderDetails -> setProductName($item['product']->getName());
                $orderDetails -> setQuantity($item['quantity']);
                $orderDetails -> setPrice($item['product']->getPrice());
                $orderDetails -> setOrders($order);
                // $totalItem = $item['product']->getPrice() * $item['quantity'];
                // $total += $totalItem;
            }
            $manager -> persist($orderDetails);
        }

        $total = 0;
                  
        $manager -> flush();

        // for ($i=0; $i < count($cart); $i++) { 
        //     unset($cart[$i]);
        // }

        return $this -> redirectToRoute('product_index');
    }

    #[Route('/delete/{id}', name: 'Order_delete')]
    public function Order_delete($id, DetailRepository $DR)
    {   
    
        $order = $this ->getDoctrine() -> getRepository(Order::class)->find($id);
        $manager = $this -> getDoctrine() -> getManager();
        
        if(count($order->getDetails()) > 0) {
            $orderDetails = $DR -> findDetailByOID($order);
            for ($i=0; $i < count($orderDetails); $i++) { 
                $manager -> remove($orderDetails[$i]);
            }
            // foreach($orderDetails as $id){
            //     $manager-> remove()
            // }
        }

        if ($order == null) {
            $this -> addFlash('error', 'Order not found or this order has already been deleted');
            return $this -> redirectToRoute('Order');
        }
        
        $manager -> remove($order);
        $manager -> flush();
        $this -> addFlash('success', 'Order deleted successfully');
        return $this->redirectToRoute('Order', [
             
        ]);
    }
}
