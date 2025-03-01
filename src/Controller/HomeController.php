<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
// use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\GalerieRepository;
use App\Repository\PieceArtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security; // ✅ Import correct



final class HomeController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/galerie', name: 'app_galerie')]
    public function galerie(): Response
    {
        return $this->render('galerie/galerie.html.twig', [
            'controller_name' => 'GalerieController',
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('home/admin.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/reclamation2', name: 'app_admin2')]
    public function Reclamation(ReclamationRepository $reclamationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer toutes les réclamations ou une requête personnalisée pour la pagination
        $queryBuilder = $reclamationRepository->createQueryBuilder('r');

        // Pagination
        $pagination = $paginator->paginate(
            $queryBuilder, /* La requête */
            $request->query->getInt('page', 1), /* Le numéro de la page */
            5 /* Limite par page */
        );

        return $this->render('reclamation/index2.html.twig', [
            'controller_name' => 'ReclamationController',
            'pagination' => $pagination, // Passe la pagination à la vue
        ]);
    }

    #[Route('/reclamation/update_status/{id}', name: 'app_reclamation_update_status', methods: ['POST'])]
    public function updateStatus(int $id, Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée.');
        }

        $newStatus = $request->request->get('statusR');
        if ($newStatus) {
            $reclamation->setStatusR($newStatus);
            $entityManager->persist($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin2');
    }
    #[Route('/statistiques', name: 'statistiques_reclamations')]
    public function statistiques(ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer les réclamations
        $reclamations = $reclamationRepository->findAll();

        // Compter les réclamations par type
        $statistiques = [
            'site_web' => 0,
            'cours' => 0,
            'artiste' => 0,
            'evenement' => 0
        ];

        foreach ($reclamations as $reclamation) {
            $statistiques[$reclamation->getTypeR()]++;
        }

        return $this->render('reponse/statistiques.html.twig', [
            'statistiques' => $statistiques,
        ]);
    }
    #[Route('/reclamations/pdf', name: 'app_reclamation_pdf')]
    public function generatePdf(ReclamationRepository $reclamationRepository): Response
    {
        // Récupération des réclamations
        $reclamations = $reclamationRepository->findAll();
        $admin = $this->security->getUser()->getFirstname(); // ✅ Utilisation via la propriété

        // Options pour DomPDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('chroot', realpath(''));
        $pdfOptions->set('isHtml5ParserEnabled', true); // Activer le parser HTML5
        // $pdfOptions->set('isPhpEnabled', true); // Permettre l'utilisation de PHP dans le HTML (si nécessaire pour les images)

        // Initialiser DomPDF
        $dompdf = new Dompdf($pdfOptions);

        // Générer le HTML à partir d'un template Twig
        $html = $this->renderView('reponse/pdf.html.twig', [
            'reclamations' => $reclamations,
            'admin' => $admin,
            // 'logoPath' => $this->getParameter('kernel.project_dir') . 'public\image\logo1.jpg', // Chemin absolu vers l'image
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner la réponse PDF
        // return new Response(
        //     $dompdf->stream("liste_reclamations.pdf", ["Attachment" => true]),
        //     Response::HTTP_OK,
        //     ['Content-Type' => 'application/pdf']
        // );
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reclamation_.pdf"',
        ]);
    }


    #[Route('/admin/galeries', name: 'app_galerie_indexA')]
    public function galerieIndex(GalerieRepository $galerieRepository): Response
    {
        $galeries = $galerieRepository->findAll();

        return $this->render('galerie/index.html.twig', [
            'galeries' => $galeries,
        ]);
    }
    #[Route('/admin/piece-art', name: 'app_piece_art_index', methods: ['GET'])]
    public function piecesIndex(PieceArtRepository $pieceArtRepository): Response
    {
        $piece_arts = $pieceArtRepository->findAll();

        return $this->render('piece_art/index.html.twig', [
            'piece_arts' => $piece_arts,
        ]);
    }
    #[Route('/admin/statG', name: 'app_galerie_statistics', methods: ['GET'])]
    public function statistics(GalerieRepository $galerieRepository, PieceArtRepository $pieceArtRepository): Response
    {
        // Get the total number of galleries
        $totalGalleries = $galerieRepository->count([]);

        // Get the total number of art pieces
        $totalPieces = $pieceArtRepository->count([]);

        // Prepare data for the chart
        $recentArtPieces = $pieceArtRepository->countRecentArtPieces(3);

        $data = [
            'galleries' => $totalGalleries,
            'pieces' => $totalPieces,
            'recentArtPieces' => $recentArtPieces,
        ];

        return $this->render('galerie/statG.html.twig', [
            'data' => json_encode($data),
        ]);
    }
}
