<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Entity\SearchFilters;
use App\Form\SearchFiltersType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('/nos-produits', name: 'products')]
    public function index(ProductRepository $repo, Request $request): Response
    {
        // $products = $repo->findAll();
        // $product = $manager->getRepository(Product::class)->findAll();
        // $product = $manager->getRepository(Product::class)->findOneByName("temporibus perferendis distinctio");
        // $product = $manager->getRepository(Product::class)->findBy(['category'=>188], ['price'=>'desc']);
        // dd($products);

        $search = new SearchFilters();
        $form = $this->createForm(SearchFiltersType::class, $search);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($search->getCategories()) > 0 || $search->getString()) {
                foreach ($search->getCategories() as $value) {
                    $id[] = $value->getId();
                }

                //$products = $repo->findBy(['category' => $id]);
                //dd($search);
                $products = $repo->findWithSearch($search);

                if (!$products) {
                    $error = "Il n'y a pas de produits de disponibles dans les catégories.";
                } else {
                    $error = null;
                }
            } else {
                $products = $repo->findAll();
            }
        } else {
            $products = $repo->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form,
            'error' => $error,
        ]);
    }

    #[Route('/compte/mes-commandes/{slug}/comment', name: 'products_comment')]
    public function comment(Request $request, Product $product, EntityManagerInterface $manager): Response
    {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setProduct($product);
            $comment->setCreatedAt(new DateTime());
        
            $comment->setUser($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');

            return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
        }

        return $this->render('product/comment.html.twig', [
           'form' => $form,
           'product' => $product
        ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
