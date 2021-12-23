<?php

namespace App\Controller;

use App\Classe\Carts;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mon-panier", name="cart")
     * @param Carts $cart
     * @return Response
     */
    public function index(Carts $cart): Response
    {

        return $this->render('carts/index.html.twig', [
        'cart' => $cart->getFull()]);
    }

    /**
     * @Route("/cart/add/{id}", name="add-to-cart")
     * @param Carts $cart
     * @param $id
     * @return Response
     */
    public function add(Carts $cart, $id): Response
    {

        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove-my-cart")
     * @param Carts $cart
     * @return Response
     */
    public function remove(Carts $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete-to-cart")
     * @param Carts $cart
     * @param $id
     * @return Response
     */
    public function delete(Carts $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease-to-cart")
     * @param Carts $cart
     * @param $id
     * @return Response
     */
    public function decrease(Carts $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

}
