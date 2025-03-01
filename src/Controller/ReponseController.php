<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Controller\ReclamationController;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    
    // #[Route('/{id}/rate', name:"rate_response", methods:["POST"])]
    // public function rateResponse($id, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $rating = $request->request->get('rating');
        
    //     // Vérifiez si la réponse existe pour cette réclamation
    //     $reponse = $entityManager->getRepository(Reponse::class)->findOneBy(['reclamation' => $id]);
        
    //     if (!$reponse) {
    //         $this->addFlash('danger', 'Aucune réponse trouvée pour cette réclamation.');
    //         // return $this->redirectToRoute('app_reclamation_list');
    //         return $this->redirectToRoute('app_reclamation_index');
    //     }

    //     // Enregistrez la note (ajoutez le champ 'rating' à votre entité Reponse si ce n'est pas déjà fait)
    //     $reponse->setRating($rating);  // Assurez-vous que votre entité `Reponse` a un champ `rating`
    //     $entityManager->flush();

    //     // Redirection après la soumission de la note
    //     $this->addFlash('success', 'Votre note a été enregistrée.');
    //     return $this->redirectToRoute('app_reclamation_reponse', ['id' => $id]);
    // }
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
public function index(ReponseRepository $reponseRepository, Request $request, PaginatorInterface $paginator): Response
{
    // Récupérer toutes les réponses
    $queryBuilder = $reponseRepository->createQueryBuilder('r');

    // Appliquer la pagination
    $pagination = $paginator->paginate(
        $queryBuilder,  // la requête pour récupérer les réponses
        $request->query->getInt('page', 1), // le numéro de page (par défaut 1)
        5 // le nombre d'éléments par page
    );

    return $this->render('reponse/index.html.twig', [
        'pagination' => $pagination,
    ]);
}

    #[Route('/new', name: 'app_reponse_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $reponse = new Reponse();
        
        // Récupérer l'ID de la réclamation depuis l'URL
        $reclamationId = $request->query->get('id');
        
        if ($reclamationId) {
            $reclamation = $em->getRepository(Reclamation::class)->find($reclamationId);
            if ($reclamation) {
                $reponse->setReclamation($reclamation);
            }
        }

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reponse);
            $em->flush();

            return $this->redirectToRoute('app_reponse_index');
        }

        return $this->render('reponse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
}
