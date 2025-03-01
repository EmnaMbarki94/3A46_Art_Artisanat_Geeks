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
                'label' => 'Événement',
                'choice_label' => 'nom', // Affiche le nom de l'événement
                'data' => $options['relation'], // Associe l'événement sélectionné à partir de la réservation
                'required' => true,
                'disabled' => true,  // Désactive le champ pour éviter toute modification
                'attr' => [
                    'readonly' => true,  // Le rendre en lecture seule pour empêcher la modification via l'interface
                ],
                'choice_attr' => function (Event $event) {
                    return [
                        'data-prixVIP' => $event->getPrixVIP(),
                        'data-prixSimple' => $event->getPrixS(),
                    ];
                }
            ])
            ->add('prix', null, [
                'label' => 'Prix',
                'mapped' => false,  // Ce champ ne sera pas directement lié à l'entité
                'attr' => [
                    'readonly' => true, // Le prix est en lecture seule
                    'value' => '0', 
                ]
            ])
            ->add('user_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',  
                'label' => 'Utilisateur',
                'required' => true
            ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'relation' => null,
        ]);
    }
}
