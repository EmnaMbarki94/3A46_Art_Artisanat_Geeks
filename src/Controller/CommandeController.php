<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Service\CartService;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $commande = new Commande();
    //     $form = $this->createForm(CommandeType::class, $commande);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($commande);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('commande/new.html.twig', [
    //         'commande' => $commande,
    //         'form' => $form,
    //     ]);

    // Protège la route pour que seuls les utilisateurs connectés puissent accéder à cette méthode
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // Vérifie que l'utilisateur a le rôle ROLE_USER
    public function new(Request $request, EntityManagerInterface $entityManager, CartService $cartService): Response
    {
        $commande = new Commande();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser(); // ou $security->getUser();
        if ($user) {
            $commande->setUser($user); // Lier la commande à l'utilisateur
        }

        // Récupérer le total du panier via CartService
        $totalPanier = $cartService->getTotal();

        $form = $this->createForm(CommandeType::class, $commande, [
            'total' => $totalPanier, // Passer l'option 'total'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraison = 8;
            $totalPanier = $cartService->getTotal();
            if ($totalPanier === null) {
                $totalPanier = 0;
            }

            // Ajouter les frais de livraison
            $commande->setTotal($totalPanier + $livraison);

            // Persist et flush de la commande
            $entityManager->persist($commande);
            $entityManager->flush();

            // Vider le panier
            $cartService->clearCart();

            // Rediriger vers la page de confirmation ou la liste des commandes
            return $this->redirectToRoute('app_article_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'total' => $totalPanier,        // Passer le total à la vue
            'cart' => $cartService->getCart(), // Passer le panier à la vue
        ]);
    }





    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Seuls les administrateurs peuvent éditer les commandes
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'total' => $commande->getTotal(), // Vérifiez que la méthode getTotal() existe
        ]);
        
    }


    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
