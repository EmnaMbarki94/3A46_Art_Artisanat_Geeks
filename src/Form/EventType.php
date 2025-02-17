<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('typeE')
            ->add('infoE')
            ->add('photoE', FileType::class, [
                'label' => 'Photo (JPEG ou PNG)',
                'mapped' => false,  
                'required' => false, 
                'constraints' => [
                    new Image([  
                        'maxSize' => '2M',  // Taille max de 2 Mo
                        'mimeTypes' => ['image/jpeg', 'image/png'], // Accepte seulement JPEG et PNG
                        'mimeTypesMessage' => 'Seuls les formats JPEG et PNG sont acceptés.',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ])
                ]
            ])
            ->add('dateE', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date de l\'événement',
                'required' => true, 
            ])
            
            ->add('prixS')
            ->add('prixVIP')
            ->add('nb_ticket');
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
