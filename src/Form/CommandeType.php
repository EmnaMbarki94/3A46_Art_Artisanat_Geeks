<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresseC', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('dateC', DateType::class, [
                'label' => 'Date de la commande',
                'widget' => 'single_text', // Render as a single input field
                'html5' => false, // Disable HTML5 input to use Flatpickr
                'attr' => ['class' => 'form-control datepicker'], // Add Flatpickr class
                'format' => 'dd-MM-yyyy', // Match the format expected by Flatpickr
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'total' => 0, // Ajouter l'option 'total' dans les options

        ]);
    }
}
