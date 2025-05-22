<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Services\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AccountAddressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'account_address')]
    public function index(AddressRepository $repo): Response
    {
            $addresses = $repo->findBy(['user' => $this->getUser()]);
            return $this->render('account/address.html.twig', [
                'addresses' => $addresses,
            ]);
    }

    #[Route('/compte/ajouter-une-adresse', name: 'add_account_address')]
    public function add(Request $request, EntityManagerInterface $manager, Cart $cart): Response
    {   
        $address = new Address;

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'L\'enregistrement s\'est effectué correctement'
            );
            if ($cart->get() > 0) {
                return $this->redirectToRoute('order');
            }
            return $this->redirectToRoute('account_address');
           
        }
        
        return $this->render('account/add_address.html.twig', [
            'form' => $form,
        ]);
        
    }
    #[Route('/compte/modifier/{id}', name: 'edit_address')]
    public function edit(Address $address, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() !== $address->getUser()) {
            $this->addFlash(
                "Erreur",
                "Vous ne pouvez pas modifier cette adresse."
            );
            $this->redirectToRoute('account_address');
        }
        
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            
            $manager->persist($address);
            $manager->flush();
            
            return $this->redirectToRoute('account_address');
        }
        return $this->render('account/add_address.html.twig', [
            'form' => $form->createView(),
        ]);
    } 
    #[Route('/compte/adresse/supprimer/{id}', name: 'delete_address')]
    public function delete(Address $address, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $address->getUser()) {
            $this->addFlash(
                "Erreur",
                "Vous ne pouvez pas supprimer cette adresse."
            );
            $this->redirectToRoute('account_address');
        }

        $entityManager->remove($address);
        $entityManager->flush();
        

        $this->addFlash('success', 'Adresse supprimée avec succès.');
        return $this->redirectToRoute('account_address');
    }
}
