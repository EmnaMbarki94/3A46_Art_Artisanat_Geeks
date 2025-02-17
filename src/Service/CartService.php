<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Article;

class CartService
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    // Add an article to the cart
    public function addToCart(Article $article, $quantity)
    {
        $cart = $this->session->get('cart', []);
        $cart[$article->getId()] = [
            'id' => $article->getId(),
            'nomA' => $article->getNomA(),
            'prixA' => $article->getPrixA(),
            // 'descA' => $article->getDescA(),
            'quantity' => $quantity,
            'imagePath' => $article->getImagePath() ?? 'default_image.jpg', // Provide a fallback value
        ];

        $this->session->set('cart', $cart);
    }

    // Get the current cart
    public function getCart()
    {
        return $this->session->get('cart', []);
    }

    // Remove an article from the cart
    public function removeFromCart(Article $article)
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$article->getId()])) {
            unset($cart[$article->getId()]);
            $this->session->set('cart', $cart);
        }
    }
    public function updateQuantity(Article $article, int $quantity): void
    {
        // Récupérer le panier actuel depuis la session
        $cart = $this->session->get('cart', []);

        // Vérifier si l'article est déjà dans le panier
        if (isset($cart[$article->getId()])) {
            // Mettre à jour la quantité
            $cart[$article->getId()]['quantity'] = $quantity;

            // Sauvegarder le panier mis à jour dans la session
            $this->session->set('cart', $cart);
        }
    }


    // Get the total price of the cart
    public function getTotal()
    {
        $cart = $this->session->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            if (isset($item['prixA']) && isset($item['quantity'])) { // ✅ Vérifie l'existence des clés
                $total += $item['prixA'] * $item['quantity'];
            }
        }

        return $total;
    }


    // Clear the cart
    public function clearCart()
    {
        $this->session->remove('cart');
    }
}
