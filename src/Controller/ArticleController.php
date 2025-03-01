<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Magasin;
use App\Service\CartService;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

// use Psr\Log\LoggerInterface;



#[Route('/article')]
final class ArticleController extends AbstractController
{

    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

//////////////////////////PANIER///////////////////////////////////
    #[Route('/add-to-cart/{id}', name: 'app_add_to_cart')]
    public function addToCart(Article $article, Request $request): RedirectResponse
    {
        // Récupère la quantité envoyée par l'utilisateur dans l'input, avec un minimum de 1
        $quantity = $request->query->get('quantity', 1); // Quantité par défaut si non envoyée

        // Si la quantité est inférieure à 1, réinitialiser à 1
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Ajoute l'article au panier avec la quantité donnée
        $this->cartService->addToCart($article, $quantity);

        // Message flash pour informer l'utilisateur
        //$this->addFlash('success', 'Article ajouté au panier !');

        return $this->redirectToRoute('app_cart'); // Redirection vers le panier
    }

    //update !quantity cart
    #[Route('/article/update-quantity/{id}', name: 'app_update_quantity', methods: ['POST', 'GET'])]
    public function updateQuantity(Request $request, SessionInterface $session, $id): JsonResponse
    {
        $cart = $session->get('cart', []);

        if (!isset($cart[$id])) {
            return new JsonResponse(['success' => false, 'message' => 'Produit non trouvé']);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] < 1) {
            return new JsonResponse(['success' => false, 'message' => 'Quantité invalide']);
        }
        
        // Mise à jour de la quantité
        $cart[$id]['quantity'] = (int) $data['quantity'];
        $session->set('cart', $cart);

        // Recalcul du total du panier
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'] * $item['prixA'];
        }

        // 🔹 Ajout de `total` dans la réponse JSON
        return new JsonResponse([
            'success' => true,
            'quantity' => $cart[$id]['quantity'],
            'total' => $total // ✅ Ajout du total ici !
        ]);
    }


    // View the cart
    #[Route('/cart', name: 'app_cart')]
    public function viewCart()
    {
        $cart = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        return $this->render('article/cart.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    // Remove item from the cart
    #[Route('/remove-from-cart/{id}', name: 'app_remove_from_cart')]
    public function removeFromCart(Article $article)
    {
        $this->cartService->removeFromCart($article);
        $this->addFlash('success', 'Article removed from cart!');

        return $this->redirectToRoute('app_cart');
    }

    // Clear the cart
    #[Route('/clear-cart', name: 'app_clear_cart')]
    public function clearCart()
    {
        $this->cartService->clearCart();
        $this->addFlash('success', 'Cart cleared!');

        return $this->redirectToRoute('app_cart');
    }


    ///////////////////////////////ARTICLES///////////////////////////////////////////////
    #[Route(name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/articles', name: 'app_article_list', methods: ['GET', 'POST'])]
public function showArticle(
    ArticleRepository $articleRepository,
    SessionInterface $session,
    PaginatorInterface $paginator,
    Request $request
): Response {
    $cart = $session->get('cart', []); //  Récupérer le panier depuis la session

    // Utiliser QueryBuilder pour la pagination
    $query = $articleRepository->findAllQuery(); // Appelle la méthode du repository

    //  Paginer les résultats
    $pagination = $paginator->paginate(
        $query, // La requête Doctrine
        $request->query->getInt('page', 1), // Numéro de la page (par défaut 1)
        4 // Nombre d'articles par page
    );

    return $this->render('article/article.html.twig', [
        'articles' => $pagination->getItems(),
        'pagination' => $pagination,
        'cart' => $cart, //  Passer la variable cart
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
            $file = $form->get('imagePath')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_directory'), $filename);
                $article->setImagePath($filename);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
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
            $file = $form->get('imagePath')->getData();

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_directory'), $filename);
                $article->setImagePath($filename);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);

            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('produits_directory'), $filename);
                $article->setImagePath($filename);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
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
    #[Route('/magasin/{id}', name: 'magasin_articles')]
    public function articlesParMagasin(Magasin $magasin, ArticleRepository $articleRepository): JsonResponse
    {
        // Récupérer les articles correspondant à ce magasin
        $articles = $articleRepository->findBy(['magasin' => $magasin]);

        // Vérifier si des articles sont trouvés
        if (empty($articles)) {
            return new JsonResponse(['message' => 'Aucun article trouvé pour ce magasin'], 200);
        }

        // Sérialiser les articles pour les envoyer sous forme de données JSON
        $articleData = [];
        foreach ($articles as $article) {
            $articleData[] = [
                'id' => $article->getId(),
                'nomA' => $article->getNomA(),
                'prixA' => $article->getPrixA(),
                'imagePath' => $article->getImagePath(),
                'quantite' => $article->getQuantite(),

            ];
        }

        // Retourner les articles sérialisés sous forme de réponse JSON
        return new JsonResponse($articleData);
    }
}
