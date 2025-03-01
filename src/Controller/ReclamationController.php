<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Reponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;



#[Route('/reclamation')]


final class ReclamationController extends AbstractController
{


 
    #[Route(name: 'app_reclamation_index', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        // Récupérer les réclamations de l'utilisateur
        $queryBuilder = $reclamationRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $user);

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder, /* query */
            $request->query->getInt('page', 1), /* page number */
            5 /* limit per page */
        );

        // Créer le formulaire dans la même page
        $reclamation = new Reclamation();
        $reclamation->setUser($user);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Redirection pour éviter la soumission multiple du formulaire
            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}/reponse', name: 'app_reclamation_reponse')]
    public function reponse(Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la réclamation est "Résolue"
        if ($reclamation->getStatusR() !== 'Résolue') {
            $this->addFlash('warning', 'Cette réclamation n\'a pas encore de réponse.');
            return $this->redirectToRoute('app_reclamation_list'); // Redirige vers la liste des réclamations
        }

        // Récupérer la réponse associée
        $reponse = $entityManager->getRepository(Reponse::class)->findOneBy(['reclamation' => $reclamation]);

        if (!$reponse) {
            $this->addFlash('danger', 'Aucune réponse trouvée pour cette réclamation.');
            return $this->redirectToRoute('app_reclamation_list');
        }

        return $this->render('reponse/index2.html.twig', [
            'reclamation' => $reclamation,
            'reponse' => $reponse,
        ]);
    }

    #[Route('/admin/reclamations', name: 'app_reclamation_index2', methods: ['GET'])]
    public function indexAdmin(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index2.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();

        $user = $this->getUser();
        if ($user) {
            $reclamation->setUser($user);
        }

        // Initialisation de la date si nécessaire
        if (!$reclamation->getDateR()) {
            $reclamation->setDateR(new \DateTime()); // Ajout d'une date par défaut
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Gérer les requêtes AJAX avec Turbo
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => true, 'redirect' => $this->generateUrl('app_reclamation_index')]);
            }

            return $this->redirectToRoute('app_reclamation_index');
        }

        // Réponse JSON en cas d'erreur
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => false,
                'form' => $this->renderView('reclamation/_form.html.twig', [
                    'form' => $form->createView(),
                ])
            ]);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }





    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return new JsonResponse(['success' => true]); // ✅ Indiquer que la mise à jour est OK
            }

            return new JsonResponse([
                'success' => false,
                'form' => $this->renderView('reclamation/_form.html.twig', [
                    'form' => $form->createView(),
                ])
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    // #[Route('/admin/reclamation/{id}/status', name: 'app_reclamation_update_status', methods: ['POST'])]
    // public function updateStatus(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    // {
    //     $status = $request->request->get('statusR');

    //     if (in_array($status, ['En attente', 'En cours', 'Résolue'])) {
    //         $reclamation->setStatusR($status);
    //         $entityManager->flush();
    //     }

    //     // Redirection vers la liste des réclamations admin
    //     return $this->redirectToRoute('app_reclamation_index2');
    // }
}
