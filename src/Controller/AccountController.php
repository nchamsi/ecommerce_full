<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountDetailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/compte", name="account")
     */
    public function index(): Response
    {

        return $this->render('account/index.html.twig', [
        ]);
    }

    /**
     * @Route("/mon-compte", name="detail")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function detail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(AccountDetailType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $new_firstname = $form->get('firstname')->getData();
            $new_lastname = $form->get('lastname')->getData();
            $new_email = $form->get('email')->getData();

            $user->setFirstname($new_firstname);
            $user->setLastname($new_lastname);
            $user->setEmail($new_email);

            $entityManager->flush();

            return $this->redirectToRoute('account');

        }

            return $this->render('account/mon-compte.html.twig', [
                'form'=> $form->createView()
            ]);
    }


}
