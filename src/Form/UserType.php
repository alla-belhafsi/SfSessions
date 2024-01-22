<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        
        // 1ère Méthode : Vérifie si l'utilisateur a au moins l'un des rôles spécifiés (formateur, modérateur, administratif)
        // $isUser = array_reduce(['ROLE_TRAINER', 'ROLE_MODERATOR', 'ROLE_ADMINISTRATIF'], fn($result, $role) => $result or $this->authorizationChecker->isGranted($role), false);

        // 2ème Méthode : Vérifie si l'utilisateur a le rôle de formateur, modérateur ou administratif
        // $isUser = $this->authorizationChecker->isGranted('ROLE_TRAINER')
        // || $this->authorizationChecker->isGranted('ROLE_MODERATOR')
        // || $this->authorizationChecker->isGranted('ROLE_ADMINISTRATIF');
            

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => $isAdmin,
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => $isAdmin,
                ]
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => $isAdmin,
                ]
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => $isAdmin,
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Formateur' => 'ROLE_TRAINER',
                    'Administration' => 'ROLE_ADMINISTRATIF',
                    'Moderateur' => 'ROLE_MODERATOR',
                ],
                'expanded' => true,
                'multiple' => true,
                'disabled' => !$isAdmin,
            ])
            ->add('enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}