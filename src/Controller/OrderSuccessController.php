<?php

namespace App\Controller;

use App\Classe\Carts;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     * @param Carts $cart
     * @param $stripeSessionId
     * @return Response
     */
    public function index(Carts $cart, $stripeSessionId): Response
    {

        $order = $this->entityManager
                      ->getRepository(Order::class)
                      ->findOneBy(['stripeSessionId'=>$stripeSessionId]);

        if (!$order || $order->getUser() != $this->getUser()) {

            return $this->redirectToRoute("home");
        }

        if ($order->getState()==0){
            // Vider le cart du user
            $cart->remove();

            // Modifier le statut isPaid en mettant 1
            $order->setState(1);
            $this->entityManager->flush();

            //Envoi d'un message de confirmation de commande (pas de réception sur gmail ; utiliser yopmail)
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstName().","
                ."<br/>Votre commande est bien validée.<br/>"
                ."<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci aspernatur consequatur dignissimos 
                dolor ducimus explicabo in incidunt ipsam, ipsum, laudantium 
                minus omnis qui quo tempora tempore unde voluptas voluptatum?</p>";

            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Nicolas Gautier/Confirmation de commande', $content);


        }
        //Afficher les quelques informations de la commande de l'utilisateur

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
