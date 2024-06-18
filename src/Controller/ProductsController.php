<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use App\Service\Uploader;

class ProductsController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/products', name: 'app_products')]
    public function index(ProductRepository $products): Response
    {

        return $this->render('product/index.html.twig', [
            'products' => $products->findAll()
        ]);


    }

    #[Route('/products/create', name: 'app_products_create')]

    public function createProduct(Request $request, Uploader $uploader): Response
    {

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('category')->getData()->getId();
            $category = $this->em->getRepository(Category::class)->find($id);
            $product = $form->getData();
            $product->setCategory($category);
            $this->em->persist($product);
            $this->em->flush();
            $file = $form->get('image')->getData();

            if ($file) {
                $image = new Image();
                $FileName = $uploader->upload($file);
                $image->setImage($FileName);
                $image->setProduct($product);
                $this->em->persist($image);
                $this->em->flush();
            }
            return $this->redirectToRoute('app_products');

        }

        return $this->render('product/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/products/view/{id}', name: 'app_products_view')]
    public function viewProduct($id): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy(['category' => $id], []);
        $category = $this->em->getRepository(Category::class)->find($id);

        return $this->render('product/product.html.twig', [
           'products' => $products,
           'category' => $category->getName()
        ]);
    }


    #[Route('/products/single/{id}', name: 'app_products_single')]

    public function singleProduct($id, ProductRepository $productR): Response
    {
        $product =  $productR->find($id);

        return $this->render('product/singleProduct.html.twig', [
           'product' => $product,
           'category' => $product->getCategory()->getName()
        ]);

    }



}
