<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategoryType;
use App\Service\Uploader;

class CategoryController extends AbstractController
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/', name: 'app_category')]
    public function index(CategoryRepository $category)
    {

        // dd($cats->);
        return $this->render('category/index.html.twig', [
            'controller_name' => 'PostController',
            'categories' => $category->findAll()
        ]);

    }

    #[Route('/category/create', name: 'app_category_create')]
    public function categoryCreate(Request $request, Uploader $uploader)
    {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $file = $form->get('image')->getData();
            if ($file) {
                $FileName = $uploader->upload($file);
                $category->setImage($FileName);
            }
            $this->em->persist($category);
            $this->em->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form
        ]);


    }

    #[Route('/category/edit/{id}', name: 'app_category_edit')]
    public function categoryEdit($id, Request $request, Uploader $uploader)
    {

        $category  = $this->em->getRepository(Category::class)->find($id);

        if(!$category) {
            return $this->redirectToRoute('app_category');
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $file = $form->get('image')->getData();
            if ($file) {
                $FileName = $uploader->upload($file);
                $category->setImage($FileName);
            }
            $this->em->persist($category);
            $this->em->flush();
            return $this->redirectToRoute('app_category');
        }


        return $this->render('category/edit.html.twig', [
            'form' => $form
        ]);

    }

}
