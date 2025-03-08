<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualPassword', PasswordType::class, [
                'label' => 'Votre mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Indiquez votre mot de passe actuel'
                ],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30
                    ]),
                ],    
                'first_options' => [
                    'label' => 'Votre mot de passe', 
                    'attr' => [
                        'placeholder' => 'Choisissez votre adresse mot de passe'
                    ],
                    'hash_property_path' => 'password' //crypté et encodé (bcrypt)
                ],
                'second_options' => [
                    'label' =>'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe'
                    ],
                ],
                'mapped' => false //plainpassword != password en Bdd
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour mon mot de passe',                
                'attr' => [
                    'class' => 'btn btn-success'                    
                ]
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event){
                //die('OK mon event marche');
                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                //dd($form->getConfig()->getOptions());
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                
                //dd($form->getConfig()->getOptions()['data']);
                //dd($user->getPassword());                
                //dd($actualPwd);

                // 1. Récupérer le mot de passe saisi par l'utilisateur et le comparer au mdp en Bdd 
                $actualPwd = $form->get('actualPassword')->getData();
                $isValid = $passwordHasher->isPasswordValid(
                    $user,
                    $form->get('actualPassword')->getData()
                );
                //dd($isValid);

                // 2 . Récupérer le mot de passe actuel en BDD
                //$actualPwdDatabase = $user->getPassword();

                //dump($actualPwd);
                //dd($actualPwdDatabase);

                // 3 . Si c'est != envoyer une erreur 
                if (!$isValid) {
                    $form->get('actualPassword')->addError(new FormError('Votre mot de passe actuel n\'est pas conforme'));
                }

            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
