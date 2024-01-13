<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Entity\Formation;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('nombrePlace', NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('stagiaires', EntityType::class, [
            //     'class' => Stagiaire::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'intitule',
                'attr' => [
                    'class' => 'form-select',
                ]
            ])
            ->add('formateur', EntityType::class, [
                'class' => Formateur::class,
                'choice_label' => function ($formateur) 
                {
                    return $formateur->getPrenom() . ' ' . $formateur->getNom();
                },
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('id', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'id',
                'attr' => [
                    'class' => 'form-select',
                ]
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
            'data_class' => Session::class,
        ]);
    }
}
