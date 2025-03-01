<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security; // Ajout de Security


class CommandeType extends AbstractType
{

    private $security;

    public function __construct(Security $security) // Injection de Security
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->security->getUser(); // Récupération de l'utilisateur connecté

        $builder
            ->add('adresseC', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('dateC', DateType::class, [
                'label' => 'Date de la commande',
                'widget' => 'single_text',
                'html5' => true, // Active le support HTML5 pour éviter les conflits
                'attr' => [
                    'class' => 'form-control datepicker',
                    'autocomplete' => 'off' // Empêche les suggestions de saisie
                ],
            ]);
            if ($user) {
                $builder->add('user', TextType::class, [
                    'label' => 'Nom et prenom de l\'utilisateur',
                    'data' => $user->getFirstName() . ' ' . $user->getLastName(), // Affichage du nom et prénom
                    'attr' => ['readonly' => true, 'class' => 'form-control'],
                    'mapped' => false, // Ne pas lier ce champ à l'entité
                ]);
            }
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'total' => 0, // Ajouter l'option 'total' dans les options

        ]);
    }
}
