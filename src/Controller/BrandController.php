<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[Route('/brand')]
class BrandController extends AbstractController
{
    #[Route('', name: 'brand_index')]
    public function ViewAllBrand(BrandRepository $repository)
    {
        // $brand = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $brand = $repository->ViewBrandList();
        return $this->render(
            "brand/index.html.twig",
            [
                'brands' => $brand
            ]
        );
    }

    #[Route('/detail/{id}', name: 'brand_detail')]
    public function ViewBrandById($id)
    {
        $brand = $this->getDoctrine()->getRepository(Brand::class)->find($id);
        if ($brand == null) {
            $this->addFlash("Error", "Brand not found !");

            return $this->redirectToRoute('brand_index');
        }
        return $this->render(
            "brand/detail.html.twig",
            [
                'brand' => $brand
            ]
        );
    }

    #[Route('/delete/{id}', name: 'brand_delete')]
    public function DeleteBrand($id)
    {
        $brand = $this-> getDoctrine()->getRepository(Brand::class)-> find($id);
        if ($brand === null) {
            $this -> addFlash("ERROR","Brand not found !");
        }
        else if (count($brand->getProducts()) > 0) {
            $this -> addFlash("ERROR","Can not delete this brand !");
        }
        else{
            $manager =$this -> getDoctrine()->getManager();
            $manager -> remove($brand);
            $manager -> flush();
            $this -> addFlash("Success","Brand deleted successfully !");
        }
        return $this -> redirectToRoute('brand_index');
    }

    #[Route('/add', name: 'brand_add')]
    public function AddBrand(Request $request)
    {
        $brand = new Brand;
        $form = $this -> createForm(BrandType::class,$brand);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($brand);
            $manager->flush();
            return $this->redirectToRoute('brand_index');
        }
        return $this->renderForm('brand/add.html.twig',
        [
            'brandForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'brand_edit')]
    public function EditBrand(Request $request, $id)
    {
        $brand = $this->getDoctrine()->getRepository(Brand::class)->find($id);
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($brand);
            $manager->flush();
            return $this->redirectToRoute('brand_index');
        }
        return $this->renderForm(
            'brand/edit.html.twig',
            [
            'brandForm' => $form
        ]
        );
    }

    #[Route('/search', name: 'brand_search')]
    public function SearchBrand(Request $request, BrandRepository $repository)
    {
        $name = $request->get('word');
        $brand = $repository->searchBrand($name);
            return $this->render("brand/index.html.twig",
            [
                'brands' => $brand
            ]);
    }
}
