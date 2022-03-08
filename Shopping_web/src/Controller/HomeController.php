<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {

        $products = $this -> getDoctrine()->getRepository(Product::class) ->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
