<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('descR', TextareaType::class, [
            'label' => 'Description',
            'attr' => ['class' => 'rich-text-editor'],
            'required' => true,
        ])
        // ->add('dateR', DateType::class, [
        //     'data' => new \DateTime(), // Date par défaut : aujourd'hui
        // ])
        ->add('dateR', DateType::class, [
            'label' => 'Date de la commande',
            'widget' => 'single_text',
            'html5' => true, // Active le support HTML5 pour éviter les conflits
            'attr' => [
                'class' => 'form-control datepicker',
                'autocomplete' => 'off' // Empêche les suggestions de saisie
            ],
        ])
        ->add('typeR', ChoiceType::class, [
            'label' => 'Type de la réclamation',
            'choices' => [
                'Site Web' => 'site_web',
                'Cours' => 'cours',
                'Artiste' => 'artiste',
                'Événement' => 'evenement',
            ],
            'placeholder' => 'Sélectionnez un type',
            'attr' => ['class' => 'form-control'],
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}