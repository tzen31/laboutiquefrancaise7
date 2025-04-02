<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//use function Symfony\Component\Form\handleRequest;

class PasswordController extends AbstractController
{
    private $entityManager;

    /**
     * AccountPasswordController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    // #[Route('/mot-de-passe_oublie', name: 'app_password')]
    // public function index(Request $request): Response
    // {
    //     //1. Formulaire
    //     $form = $this->createForm(ForgotPasswordType::class);
    //     $form->handleRequest($request);

    //     //2. Traitement formulaire
    //     if ($form->isSubmitted() && $form->isValid()){
    //         //3. Si l'email renseigné par l'utilisateur est en base de données    
    //         //4. Si c'est le cas, on reset le password et on envoie par email le nouveau mot de passe    
    //         //5. Si aucun email trouvé, on push une notification : Email introuvable
    //     }        
    
    //     return $this->render('password/index.html.twig', [
    //         'forgotPasswordForm' => $form->createView()
    //     ]);
    // }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();
            
            $this->addFlash(
                'success',
                'Votre mot de passe a été correctement modifié'
            );
        }

        return $this->render('account/password/index.html.twig', [
           'modifyPwd' => $form->createView()
        ]);
    }
}
