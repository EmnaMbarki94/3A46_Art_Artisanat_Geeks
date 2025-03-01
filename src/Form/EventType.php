<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('typeE', ChoiceType::class, [
                'choices' => [
                    'Concert' => 'concert',
                    'Conférence' => 'conference',
                    'Spectacle' => 'spectacle',
                    'Marché' => 'spectacle',

                    
                ],
                'required' => false,
                'placeholder' => 'Sélectionner une catégorie',
            ])
            ->add('infoE', TextareaType::class, [
                'label' => 'Description de l\'événement',
                'attr' => [
                    'placeholder' => 'Entrez une description détaillée de l\'événement',
                    'rows' => 6,
                    'style' => 'resize: none;',  // Empêche le redimensionnement de la zone de texte
                ],
               
            ])
            ->add('photoE', FileType::class, [
                'label' => 'Photo (JPEG ou PNG)',
                'mapped' => false,  
                'required' => false, 
                'constraints' => [
                    new Image([  
                        // 'maxSize' => '2M',  // Taille max de 2 Mo
                        'mimeTypes' => ['image/jpeg', 'image/png'], // Accepte seulement JPEG et PNG
                        'mimeTypesMessage' => 'Seuls les formats JPEG et PNG sont acceptés.',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ])
                ]
            ])
            ->add('dateE', DateTimeType::class, [
                'widget' => 'single_text', // Use a single input for both date and time
                'html5' => true, // Use HTML5 input for both date and time
                'label' => 'Date et Heure de l\'événement',
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
