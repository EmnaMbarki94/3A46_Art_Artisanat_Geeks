<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Article;
use App\Entity\LigneCommande;
use App\Service\CartService;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/*pdf */
use Dompdf\Dompdf;
use Dompdf\Options;


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

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager, CartService $cartService, SessionInterface $session): Response
    {
        $commande = new Commande();
        $user = $this->getUser();
        if ($user) {
            $commande->setUser($user);
        }
    
        $totalPanier = $cartService->getTotal();
        $form = $this->createForm(CommandeType::class, $commande, ['total' => $totalPanier]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $livraison = 8;
            $totalPanier = $cartService->getTotal() ?? 0;
            $commande->setTotal($totalPanier + $livraison);
            $commande->setDateC(new \DateTime());
    
            foreach ($cartService->getCart() as $item) {
                $article = $entityManager->getRepository(Article::class)->find($item['id']);
                if ($article) {
                    if ($article->getQuantite() >= $item['quantity']) {
                        $article->setQuantite($article->getQuantite() - $item['quantity']);
    
                        // 🔹 Création de la ligne de commande
                        $ligneCommande = new LigneCommande();
                        $ligneCommande->setCommande($commande);
                        $ligneCommande->setArticle($article);
                        $ligneCommande->setQuantite($item['quantity']);
                        
                        $commande->getLigneCommandes()->add($ligneCommande); // ✅ Ajout à la collection

                        $entityManager->persist($ligneCommande);
                    } else {
                        $this->addFlash('danger', "Stock insuffisant pour {$article->getNomA()}.");
                        return $this->redirectToRoute('app_cart');
                    }
                    $entityManager->persist($article);
                }
            }
    
            $entityManager->persist($commande);
            $entityManager->flush();
            // $entityManager->refresh($commande); // ✅ Force Doctrine à recharger la commande depuis la base

            $cartService->clearCart();
            
            
            // 🔹 Chargement des lignes de commande avec JOIN FETCH
            $commande = $entityManager->createQueryBuilder()
            ->select('c', 'lc', 'a')
            ->from(Commande::class, 'c')
            ->leftJoin('c.ligneCommandes', 'lc')
            ->leftJoin('lc.article', 'a')
            ->where('c.id = :id')
            ->setParameter('id', $commande->getId())
            ->getQuery()
            ->getOneOrNullResult();

        
            $html = $this->renderView('commande/facture.html.twig', ['commande' => $commande]);
            
    
            // 🔹 Génération du PDF après la création de la commande
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $pdfOptions->set('chroot', realpath(''));
            $pdfOptions->set('isHtml5ParserEnabled',true);
            

            $dompdf = new Dompdf($pdfOptions);
    
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
    
            $pdfOutput = $dompdf->output();
            $session->set('facture_pdf', base64_encode($pdfOutput));

    
            // return $this->redirectToRoute('app_article_list', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'redirect_url' => $this->generateUrl('app_article_list'),
                'pdf_url' => $this->generateUrl('telecharger_facture', ['id' => $commande->getId()])
            ]);
            
        }
    
        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'total' => $totalPanier,
            'cart' => $cartService->getCart(),
            'commande' => $commande

        ]);
    }
    
    #[Route('/telecharger-facture', name: 'telecharger_facture')]
    public function telechargerFacture(SessionInterface $session): Response
    {
        $pdfOutput = base64_decode($session->get('facture_pdf'));
    
        // if (!$pdfOutput) {
        //     throw $this->createNotFoundException('Aucun fichier à télécharger.');
        // }
    
        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture.pdf"');
    
        return $response;
    }
    //StatcTOPproduct
    #[Route('/stats/top-products', name: 'stats_top_products')]
    public function statistiques(
        LigneCommandeRepository $ligneCommandeRepository, 
        CommandeRepository $commandeRepository
    ): Response {
        // 🔹 Récupérer les produits les plus vendus par magasin
        $topProducts = $ligneCommandeRepository->getTopProductByStore();
        $groupedByMagasin = [];
        foreach ($topProducts as $item) {
            $magasin = $item['magasin'];
            if (!isset($groupedByMagasin[$magasin])) {
                $groupedByMagasin[$magasin] = [];
            }
            $groupedByMagasin[$magasin][] = $item;
        }
    
        // 🔹 Récupérer les commandes par jour
        $ordersByDay = $commandeRepository->getOrdersCountByDay();
    
        return $this->render('commande/StatProduct.html.twig', [
            'topProductsByStore' => $groupedByMagasin,
            'ordersByDay' => $ordersByDay,
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

    // #[Route('/commande/facture/{id}', name: 'commande_facture')]
    // public function facture(Commande $commande, Pdf $snappy): Response
    // {
    //     // Rendre la vue du PDF
    //     $html = $this->renderView('commande/facture.html.twig', [
    //         'commande' => $commande
    //     ]);

    //     // Générer le PDF
    //     $pdf = $snappy->getOutputFromHtml($html);

    //     // Retourner une réponse HTTP avec le PDF
    //     return new Response(
    //         $pdf,
    //         200,
    //         [
    //             'Content-Type' => 'application/pdf',
    //             'Content-Disposition' => 'inline; filename="facture_' . $commande->getId() . '.pdf"'
    //         ]
    //     );
    // }
}
