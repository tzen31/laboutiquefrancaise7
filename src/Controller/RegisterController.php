<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{ 
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //dd($request);
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        
        $form->handleRequest($request); //écoute $request        

        if ($form->isSubmitted() && $form->isValid()) {                
            //dd($form->getData());
            //dd($user);
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addflash(
                'success',
                'Votre compte est correctement crée, veuillez vous connecter'
            );

            return $this->redirectToRoute('app_login');
        }
        // si le formulaire est soumis alors
        // Tu enregistres les datas en BDD
        // Tu envoies un message de confirmation du compte crée

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
