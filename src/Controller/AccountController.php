<?php

namespace App\Controller;

use App\Form\PasswordUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        //dd($user);

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addflash(
                'success',
                'Votre mot de passe est correctement mis à jour'
            );

            $entityManager->flush();
            //dd($form->getData());
        }

        return $this->render('account/password.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }
}
