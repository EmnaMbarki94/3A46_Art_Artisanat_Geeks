<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Magasin;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/article')]
final class ArticleController extends AbstractController
{
    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    #[Route('/articles', name: 'app_article_list', methods: ['GET','POST'])]
    public function showArticle(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/article.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();

        $magasinRepository = $entityManager->getRepository(Magasin::class);
        $magasins = $magasinRepository->findAll();

        $form = $this->createForm(ArticleType::class, $article, [
            'magasins' => $magasins,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'image téléchargée
            $imageFile = $form->get('imagePath')->getData(); // 'imagePath' is now a single file input field

            if ($imageFile instanceof UploadedFile) {
                // Générer un nom unique pour l'image
                $fileName = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer l'image vers le répertoire défini dans services.yaml
                    $imageFile->move($this->getParameter('produits_directory'), $fileName);
                } catch (FileException $e) {
                    throw new \Exception('Impossible de télécharger l\'image.');
                }

                // Enregistrer le chemin de l'image dans l'entité Article
                $article->setImagePath($fileName);
            }

            // Persister l'article (et l'image, si applicable)
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire de l'article avec les magasins passés en option
        $magasins = $entityManager->getRepository(Magasin::class)->findAll();
        $form = $this->createForm(ArticleType::class, $article, [
            'magasins' => $magasins, // Passer les magasins au formulaire
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
<<<<<<< HEAD
            $file = $form->get('imagePath')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_directory'), $filename);
                $article->setImagePath($filename);
            }
            $entityManager->persist($article);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
=======
            // Gestion de l'upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imagePath')->getData();

            if ($imageFile) {
                // Générer un nom unique pour le fichier
                $fileName = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer l'image vers le dossier configuré
                try {
                    $imageFile->move(
                        $this->getParameter('produits_directory'), // Dossier d'upload pour les produits
                        $fileName
                    );
                } catch (FileException $e) {
                    throw new \Exception('Impossible de télécharger l\'image.');
                }

                // Supprimer l'ancienne image si elle existe
                if ($article->getImagePath()) {
                    $oldFilePath = $this->getParameter('produits_directory') . '/' . $article->getImagePath();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Mettre à jour le chemin de l'image
                $article->setImagePath($fileName);
            }

            // Sauvegarder les modifications de l'article dans la base de données
            $entityManager->flush();

            // Rediriger après l'édition
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
>>>>>>> 4df36eff2bc97aa07002853c4b56d516ec638d7b
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }


    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    
   

    #[Route('/articles/{id}', name: 'article_detail')]
    public function showArticleDetail($id, ArticleRepository $articleRepository): Response
    {
        // Manually find the article by ID
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        return $this->render('article/detail.html.twig', [
            'article' => $article,
        ]);
    }
}
