<?php
// src/Service/PdfService.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

class PdfService
{
    public function generatePdf(string $htmlContent, string $filename): Response
    {
        // Configurer Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true); // Activer le chargement d'images
        $options->set('chroot', $_SERVER['DOCUMENT_ROOT']); // Définir la racine des fichiers accessibles

        $dompdf = new Dompdf($options);

        // Charger le HTML
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Retourner le PDF en réponse
        return new Response(
            $dompdf->stream($filename, ["Attachment" => true]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
}
