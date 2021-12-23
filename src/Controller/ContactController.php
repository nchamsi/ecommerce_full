<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->addFlash('notice', 'Merci de nous avoir contacté. Notre équipe vous recontactera dans les meilleurs délais.');
            $content = $form['content']->getData();
            $mail = new Mail();
            $mail->send('nicolas@devnantes.fr', 'MEGA PROMO', 'Vous avez reçu une nouvelle demande de contact ', $content);
        }

        return $this->render('contact/index.html.twig', ['form' => $form->createView()]);
    }
}
