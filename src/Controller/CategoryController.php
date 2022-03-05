<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'category_index')]
    public function ViewAllcategory()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig',[
            'categorys'=> $categories
        ]);
    }
    //View category by id
    #[Route('/detail/{id}', name: 'category_detail')]
    public function ViewcategoryById($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash(
               'Error',
               'category not found'
            );
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/detail.html.twig',[
            'category'=>$category
        ]);
    }
    // Delete category
    #[Route('/delete/{id}', name: 'category_delete')]
    public function Deletecategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash(
               'Error',
               'category not found'
            );
        }
        else{
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash("Success","Delete category success !");
        }
        return $this->redirectToRoute('category_index');
    }
    //Add new category
    #[Route('/add', name: 'category_add')]
    public function Addcategory(Request $request)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/add.html.twig',
        [
            'categoryform'=>$form
        ]);
    }
    //Edit category
    #[Route('/edit/{id}', name: 'category_edit')]
    public function Editcategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->renderForm('category/edit.html.twig',
        [
            'categoryform' => $form
        ]);
    }
}
