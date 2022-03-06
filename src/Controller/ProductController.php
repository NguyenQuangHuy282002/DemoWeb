<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/product')]
class ProductController extends AbstractController
{
    //View all products
    #[Route('', name: 'product_index')]
    public function ViewAllProduct()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig',[
            'products'=> $products
        ]);
    }
    //View product by id
    #[Route('/detail/{id}', name: 'product_detail')]
    public function ViewProductById($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash(
               'Error',
               'Product not found'
            );
            return $this->redirectToRoute('product_index');
        }
        return $this->render('product/detail.html.twig',[
            'product'=>$product
        ]);
    }
    // Delete product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/delete/{id}', name: 'product_delete')]
    public function DeleteProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash(
               'Error',
               'product not found'
            );
        }
        else{
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();
        $this->addFlash("Success","Delete product success !");
        }
        return $this->redirectToRoute('product_index');
    }
    //Add new product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/add', name: 'product_add')]
    public function AddProduct(Request $request)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $product->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('product_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $product->setImage($imageName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/add.html.twig',
        [
            'productform'=>$form
        ]);
    }
    //Edit product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/edit/{id}', name: 'product_edit')]
    public function EditProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $product->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('product_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $product->setImage($imageName);
            }
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->renderForm('product/edit.html.twig',
        [
            'productform' => $form
        ]);
    }

    #[Route("/asc",name:"sort_asc_product")]
    public function ascName(ProductRepository $repository) {
        $products = $repository->sortProductAsc();
        return $this->render('product/index.html.twig',[
            'products'=> $products
        ]);
    }


    #[Route("/desc",name:"sort_desc_product")]
    public function descName(ProductRepository $repository) {
        $products = $repository->sortProductDesc();
        return $this->render('product/index.html.twig',[
            'products'=> $products
        ]);
        
    }
}
