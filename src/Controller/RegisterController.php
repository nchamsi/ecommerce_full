<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        //Variable facile à utiliser
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneBy(['email' =>$user->getEmail()]);

            if (!$search_email){

                $password = $user->getPassword();

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                //pas de réception sur gmail ; utiliser yopmail
                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstName().","
                    ."<br/>Bienvenue.<br/>"
                    ."<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci aspernatur consequatur dignissimos 
                dolor ducimus explicabo in incidunt ipsam, ipsum, laudantium 
                minus omnis qui quo tempora tempore unde voluptas voluptatum?</p>";

                $mail->send($user->getEmail(), $user->getFirstName(), 'Nicolas Gautier/Un petit bienvenu', $content);

                $notification="Votre inscription s'est correctement déroulée. Un email de confirmation vient de vous être envoyé";
            } else {
                $notification="L'email que vous avez renseignez existe déjà.";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
