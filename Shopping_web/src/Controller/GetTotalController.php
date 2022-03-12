<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Order;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GetTotalController extends AbstractController
{
    #[Route('/getTotal', name: "order_total")]
    public function getTotal( OrderRepository $OR, DetailRepository $DR)
    {
        $dateSelected = $_POST['date'];

        // $dateType = (\DateTime::createFromFormat('Y-m-d', $dateSelected));

        // die($dateType);

        $get =  ($DR -> getIt($dateSelected));

        $calTotal = "";
        $calTotal1 = "";

        foreach ($get as $item) {
            $calTotal = $item['totalPrice'];
        }

        // die(json_encode($calTotal));

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();
        
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
            'total' => $calTotal,
            'totalU' => $calTotal1,
            'date' => $date,
            'name' => $name,
        ]);
    }

    #[Route('/getTotalU', name: "order_totalU")]
    public function getTotalU(OrderRepository $OR, DetailRepository $DR)
    {
        $nameSelected = $_POST['name'];

        // $dateType = (\DateTime::createFromFormat('Y-m-d', $dateSelected));

        // die($dateType);

        $get =  ($DR -> getIt1($nameSelected));

        $calTotal="";

        $calTotal1 = "";

        foreach ($get as $item) {
            $calTotal1 = $item['totalPrice'];
        }

        // die(json_encode($calTotal));

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $brands = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();

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
            'total' => $calTotal,
            'totalU' => $calTotal1,
            'date' => $date,
            'name' => $name,
        ]);
    }


     
}
