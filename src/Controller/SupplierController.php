<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[Route('/supplier')]
class SupplierController extends AbstractController
{
    #[Route('', name: 'supplier_index')]
    public function ViewAllSupplier(SupplierRepository $repository)
    {
        // $supplier = $this->getDoctrine()->getRepository(Supplier::class)->findAll();
        $supplier = $repository->ViewSupplierList();
        return $this->render(
            "supplier/index.html.twig",
            [
                'suppliers' => $supplier
            ]
        );
    }

    #[Route('/detail/{id}', name: 'supplier_detail')]
    public function ViewSupplierById($id)
    {
        $supplier = $this->getDoctrine()->getRepository(Supplier::class)->find($id);
        if ($supplier == null) {
            $this->addFlash("Error", "Supplier not found !");

            return $this->redirectToRoute('supplier_index');
        }
        return $this->render(
            "supplier/detail.html.twig",
            [
                'supplier' => $supplier
            ]
        );
    }

    #[Route('/delete/{id}', name: 'supplier_delete')]
    public function DeleteSupplier($id)
    {
        $supplier = $this-> getDoctrine()->getRepository(Supplier::class)-> find($id);
        if ($supplier === null) {
            $this -> addFlash("ERROR","Supplier not found !");
        }
        else if (count($supplier->getProducts()) > 0) {
            $this -> addFlash("ERROR","Can not delete this supplier !");
        }
        else{
            $manager =$this -> getDoctrine()->getManager();
            $manager -> remove($supplier);
            $manager -> flush();
            $this -> addFlash("Success","Supplier deleted successfully !");
        }
        return $this -> redirectToRoute('supplier_index');
    }

    #[Route('/add', name: 'supplier_add')]
    public function AddSupplier(Request $request)
    {
        $supplier = new Supplier;
        $form = $this -> createForm(SupplierType::class,$supplier);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($supplier);
            $manager->flush();
            return $this->redirectToRoute('supplier_index');
        }
        return $this->renderForm('supplier/add.html.twig',
        [
            'supplierForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'supplier_edit')]
    public function EditSupplier(Request $request, $id)
    {
        $supplier = $this->getDoctrine()->getRepository(Supplier::class)->find($id);
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($supplier);
            $manager->flush();
            return $this->redirectToRoute('supplier_index');
        }
        return $this->renderForm(
            'supplier/edit.html.twig',
            [
            'supplierForm' => $form
        ]
        );
    }

    #[Route('/search', name: 'supplier_search')]
    public function SearchSupplier(Request $request, SupplierRepository $repository)
    {
        $name = $request->get('word');
        $supplier = $repository->searchSupplier($name);
            return $this->render("supplier/index.html.twig",
            [
                'suppliers' => $supplier
            ]);
    }
}
