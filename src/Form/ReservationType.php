<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nb_place')
            ->add('libelle')
            ->add('etatE', ChoiceType::class, [
                'choices'  => [
                    'VIP' => 'VIP',      // Choix "VIP"
                    'Simple' => 'Simple',// Choix "Simple"
                ],
                'label' => 'Etat', // Titre du champ
                'expanded' => false, // Liste déroulante (false) ou boutons radio (true)
                'multiple' => false, // Permet de choisir plusieurs valeurs (false = un seul choix)
            ])
            ->add('relation', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
