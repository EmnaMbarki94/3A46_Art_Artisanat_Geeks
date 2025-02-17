<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
                'choice_label' => 'nom',  // Affiche le nom de l'événement
                'label' => 'Événement',
                'required' => true,
                'choice_attr' => function (Event $event) {
                    return [
                        'data-prixVIP' => $event->getPrixVIP(),
                        'data-prixSimple' => $event->getPrixS(),
                    ];
                }
            ])
            ->add('prix', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('user_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',  
                'label' => 'Utilisateur',
                'required' => true
            ]) 
            ->add('prix', null, [
                'label' => 'Prix',
                'mapped' => false,  // Ce champ ne sera pas directement lié à l'entité
                'attr' => [
                    'readonly' => true, // Le prix est en lecture seule
                    'value' => '0', // Valeur initiale
                ]
            ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
