<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Delivery;
use App\Entity\Detail;
use App\Entity\Order;
use App\Repository\DeliveryRepository;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/order')]
class OrderController extends AbstractController
{

    /**
     * @IsGranted("ROLE_MANAGER")
     */
    #[Route('/', name: 'Order')]
    public function index(OrderRepository $OR): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();
        $total = "";
        $totalU = "";
        $date = [];
        $get1 = $OR ->getDate();
        $i = 0;
        $j = 0;

        foreach ($get1 as $g){
            $date[$i] = $g['date'];
            $i++;
        }

        $name = [];
        $get2 = $OR ->getName();

        foreach ($get2 as $g){
            $name[$j] = $g['username'];
            $j++;
        }
        

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'brands' => $brands,
            'categories' => $categories,
            'total' => $total,
            'totalU' => $totalU,
            'date' => $date,
            'name' => $name,
        ]);
    }


    

    /**
     * @IsGranted("ROLE_MANAGER")
     */
    #[Route('/detail/{id}', name: 'Order_detail')]
    public function Order_detail($id, DetailRepository $DR, ProductRepository $PR)
    {

        $order = $this->getDoctrine()->getRepository(Detail::class)->find($id);
        
        $orderDetails = $DR->findDetailByOID($order);

        $total = 0;

        foreach ($orderDetails as $detail){
            $totalOfIT = $detail -> getPrice() * $detail -> getQuantity();
            $total += $totalOfIT;
        }


        if ($order == null) {
            $this->addFlash('error', 'Order not found');
            return $this->redirectToRoute('Order');
        }
        return $this->render('order/detail.html.twig', [
            'order' => $order,
            'detail' => $orderDetails,
            'total' => $total,

        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    #[Route('/add', name: 'order_add')]
    public function AddProduct(UserInterface $user, ProductRepository $PR, SessionInterface $si, DeliveryRepository $DR)
    {
        $order = new Order;
        $now = date("Y-m-d");
        $order->setDate(\DateTime::createFromFormat('Y-m-d', $now));
        $deliveryName = $_POST['delivery'];
        $deli = $DR->getNameDelivery($deliveryName);
        $delivery = new Delivery;
        foreach ($deli as $dl) {
            $delivery = $dl;
        }
        $order->setDelivery($delivery);
        $username = $user->getUsername();
        $order->setUsername($username);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();


        $cart = $si->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $PR->find($id),
                'quantity' => $quantity,
            ];
            $orderDetails = new Detail;
            foreach ($cartWithData as $item) {
                $orderDetails->setProductName($item['product']->getName());
                $orderDetails->setQuantity($item['quantity']);
                $orderDetails->setPrice($item['product']->getPrice());
                $orderDetails->setOrders($order);
                // $totalItem = $item['product']->getPrice() * $item['quantity'];
                // $total += $totalItem;
            }
            $manager->persist($orderDetails);
        }

        $total = 0;

        $manager->flush();

        // for ($i=0; $i < count($cart); $i++) { 
        //     unset($cart[$i]);
        // }

        return $this->redirectToRoute('homepage');
    }


    /**
     * @IsGranted("ROLE_MANAGER")
     */
    #[Route('/delete/{id}', name: 'Order_delete')]
    public function Order_delete($id, DetailRepository $DR)
    {

        $order = $this->getDoctrine()->getRepository(Order::class)->find($id);
        $manager = $this->getDoctrine()->getManager();

        if (count($order->getDetails()) > 0) {
            $orderDetails = $DR->findDetailByOID($order);
            for ($i = 0; $i < count($orderDetails); $i++) {
                $manager->remove($orderDetails[$i]);
            }
            // foreach($orderDetails as $id){
            //     $manager-> remove()
            // }
        }

        if ($order == null) {
            $this->addFlash('error', 'Order not found or this order has already been deleted');
            return $this->redirectToRoute('Order');
        }

        $manager->remove($order);
        $manager->flush();
        $this->addFlash('success', 'Order deleted successfully');
        return $this->redirectToRoute('Order', []);
    }

    


    #[Route("/asc", name: "sort_asc_date")]
    public function ascName(OrderRepository $repository)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $orders = $repository->sortDateAsc();
        $total = "";
        $totalU = "";
        $date = [];
        $get1 = $repository ->getDate();
        $i = 0;
        $j = 0;

        foreach ($get1 as $g){
            $date[$i] = $g['date'];
            $i++;
        }

        $name = [];
        $get2 = $repository ->getName();

        foreach ($get2 as $g){
            $name[$j] = $g['username'];
            $j++;
        }
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'brands' => $brands,
            'categories' => $categories,
            'total' => $total,
            'totalU' => $totalU,
            'date' => $date,
            'name' => $name,

        ]);
    }


    #[Route("/desc", name: "sort_desc_date")]
    public function descName(OrderRepository $repository)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $orders = $repository->sortDateDesc();
        $total = "";
        $totalU = "";
        $date = [];
        $get1 = $repository ->getDate();
        $i = 0;
        $j = 0;

        foreach ($get1 as $g){
            $date[$i] = $g['date'];
            $i++;
        }

        $name = [];
        $get2 = $repository ->getName();

        foreach ($get2 as $g){
            $name[$j] = $g['username'];
            $j++;
        }
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'brands' => $brands,
            'categories' => $categories,
            'total' => $total,
            'totalU' => $totalU,
            'date' => $date,
            'name' => $name,

        ]);
    }


    
}
