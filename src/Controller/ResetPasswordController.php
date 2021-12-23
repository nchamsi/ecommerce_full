<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        if ($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email'))
        {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email')]);

            if($user)
            {
                // 1 : Enregistrer en base la demande de reset password avec user, token, createdAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // 2 : Envoyer un email avec un lien lui permettant de mettre à jour son mot de passe
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getFirstname()."<br/>Vous avez demandé à réinitialiser votre mot de passe<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant : Lien = <a href='".$url."'> mettre à jour votre mot de passe.</a><br/><br/>";
                $content .="L'adresse à rentrer ressemble à http://127.0.0.1:8000/modifier-mon-mot-de-passe-oublie/61c2f0dadc9e2 (modifier le token et supprimer le préfixe d'adresse";
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFullName(), 'Réinitialiser votre mot-de-passe', $content);
                $this->addFlash('notice', 'Vous allez recevoir un mail avec un lien de réinitialisation.');

            }else{
                $this->addFlash('notice', 'Cette adresse email est inconnue');
            }

        }

        return $this->render('reset_password/index.html.twig', );
    }

    /**
     * @Route("/modifier-mon-mot-de-passe-oublie/{token}", name="update_password")
     * @param $token
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function update($token, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token'=>$token]);

        if(!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        // Vérifier si le createdAt = now  - 3 h
        $now = new \DateTime();
        if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour'))
        {
            //modifier mon mot de passe
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveler');
            return $this->redirectToRoute('reset_password');
        }

        //Rendre une vue avec mot de passe et confirmer votre mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Récupérer le password dans le formulaire
            $new_pwd = $form->get('new_password')->getData();

            //Encoder le nouveau password
            $password = $passwordHasher->hashPassword($reset_password->getUser(), $new_pwd );

            //Définir le nouveau mot de passe
            $reset_password->getUser()->setPassword($password);

            //Maj de la BDD
            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a été mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView()
        ] );
    }
}
