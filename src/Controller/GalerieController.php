<?php

namespace App\Controller;

use App\Entity\Galerie;
use App\Entity\PieceArt;
use App\Form\GalerieType;
use App\Repository\GalerieRepository;
use App\Repository\PieceArtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/galerie')]
final class GalerieController extends AbstractController{
    #[Route(name: 'app_galerie_index', methods: ['GET'])]
    public function index(Request $request, GalerieRepository $galerieRepository, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('query');

        // Use the repository to get a query builder for the search term
        $queryBuilder = $galerieRepository->findBySearchTermQueryBuilder($searchTerm);

        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $queryBuilder, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            3 // Number of items per page
        );

        return $this->render('galerie/galerie.html.twig', [
            'galeries' => $pagination->getItems(), // Pass the paginated items
            'pagination' => $pagination, 
            'search_term' => $searchTerm,
        ]);
    }

    
    #[Route('/new', name: 'app_galerie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser(); 
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        if (!$this->isGranted('ROLE_ARTISTE')) {
            $this->addFlash('error', 'Accès refusé. Vous devez être un artiste pour créer une galerie.');
            return $this->redirectToRoute('app_galerie_index'); 
        }

        $existingGalerie = $entityManager->getRepository(Galerie::class)->findOneBy(['user' => $user]);

        if ($existingGalerie) {
            $this->addFlash('error', 'Chaque utilisateur ne peut créer qu\'une seule galerie.');
            return $this->redirectToRoute('app_galerie_index');
        }

        $galerie = new Galerie();
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $galerie->setUser($user); // Associate the gallery with the user
        
            $file = $form->get('photoG')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('photos_directory'), $filename);
                $galerie->setPhotoG($filename);
            }
            else {
                $galerie->setPhotoG('default.jpg'); 
            }
    
            $entityManager->persist($galerie);
            $entityManager->flush();

            $this->addFlash('success', 'Galerie ajoutée avec succès.');

            return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galerie/new.html.twig', [
            'galerie' => $galerie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galerie_show', methods: ['GET'])]
    public function show(Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les pièces d'art associées à la galerie
        $pieceArts = $entityManager->getRepository(PieceArt::class)->findBy(['galerie' => $galerie]);

        return $this->render('galerie/show.html.twig', [
            'galerie' => $galerie,
            'pieceArts' => $pieceArts,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_galerie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez s\'authentifier pour éditer une galerie.');
            return $this->redirectToRoute('app_galerie_index'); 
        }
    
        // Ensure the user is the owner of the gallery
        if ($galerie->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'etes pas autorisé à éditer cette galerie.');
            return $this->redirectToRoute('app_galerie_index');
        }
        
        $form = $this->createForm(GalerieType::class, $galerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoG')->getData();
            if ($file) {
                // Gérer l'upload de la photo ici
                $filename = uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('photos_directory'), // Assurez-vous que ce paramètre est défini dans services.yaml
                    $filename
                );
                // Mettez à jour le champ photoG avec le nouveau nom de fichier
                $galerie->setPhotoG($filename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('galerie/edit.html.twig', [
            'galerie' => $galerie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_galerie_delete', methods: ['POST'])]
    public function delete(Request $request, Galerie $galerie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $entityManager->refresh($user);
        }

        // Check if the user is authenticated and the owner of the gallery
        if (!$user || $galerie->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'etes pas autorisé à supprimer cette galerie.');
            return $this->redirectToRoute('app_galerie_index');
        }
    
        // Check CSRF token for security
        if ($this->isCsrfTokenValid('delete' . $galerie->getId(), $request->request->get('_token'))) {
            foreach ($galerie->getPieceArt() as $pieceArt) {
                $entityManager->remove($pieceArt);
            }
            $entityManager->remove($galerie); // Remove the gallery
            $entityManager->flush(); // Persist the deletion
        }
        $this->addFlash('success', 'Galerie supprimée avec succès.');
        return $this->redirectToRoute('app_galerie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/galeries', name: 'app_galerie_indexA')]
    public function indexAdmin(GalerieRepository $galerieRepository): Response
    {
        $galeries = $galerieRepository->findAll();

        return $this->render('galerie/index.html.twig', [
            'galeries' => $galeries,
        ]);
    }
    #[Route('/galerie/{id}/pieces-art', name: 'app_galerie_pieces_art', methods: ['GET'])]
    public function piecesArt(Galerie $galerie, PieceArtRepository $pieceArtRepository): Response
    {
        $piecesArt = $pieceArtRepository->findBy(['galerie' => $galerie]);

        return $this->render('piece_art/index.html.twig', [
            'piece_arts' => $piecesArt,
            'galerie' => $galerie,
        ]);
    }

}
