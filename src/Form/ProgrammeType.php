<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('session', EntityType::class, [
            //     'class' => Session::class,
            //     'choice_label' => 'intitule',
            //     'attr' => [
            //         'class' => 'form-select'
            //     ]
            // ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label_attr' => [
                    'class' => 'center'
                ],
            
            ])
            ->add('nombreJours', NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'center'
                ],
            ]);

            // Vérifie si la variable 'edit' est définie avant d'ajouter le champ 'enregistrer'
            if (isset($options['edit']) && $options['edit']) {
                $builder->add('enregistrer', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-success'
                    ]
                ]);
            }
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
