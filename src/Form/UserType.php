<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('numTel')
            ->add('address')
        ;
        $builder->add('role', ChoiceType::class, [
            'choices'  => [
                'Abonné' => 'ROLE_USER',
                'Artiste' => 'ROLE_ARTISTE',
                'Enseignant' => 'ROLE_ENSEIGNANT',
            ],
            'expanded' => false,
            'multiple' => false,
            'required' => true,
            'mapped' => false, // Empêche Symfony d'essayer de l'utiliser dans l'entité
        ]);

        // Convertir `role` en `roles`
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // Vérifier que `role` est bien défini
            if ($form->has('role') && $form->get('role')->getData()) {
                $role = $form->get('role')->getData();
                $user->setRoles([$role]); // Convertir et stocker dans `roles`
            }
        });
       
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
