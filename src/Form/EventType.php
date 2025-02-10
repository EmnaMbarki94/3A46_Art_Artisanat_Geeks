<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\App\Form\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('typeE')
            ->add('infoE')
            ->add('photoE', FileType::class, [
                'label' => 'Photo (image file)',
                'mapped' => false,    // IMPORTANT: Ne pas mapper directement à l'entité
                'required' => false,  // Pas obligatoire pour l'édition
                'data_class' => null, // Évite l'erreur de type
            ])
            ->add('dateE', null, [
                'widget' => 'single_text', 
                
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
