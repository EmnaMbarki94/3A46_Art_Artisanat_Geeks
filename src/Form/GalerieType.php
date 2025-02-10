<?php

namespace App\Form;

use App\Entity\Galerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GalerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nomG', TextareaType::class, [
                'label' => 'Titre de la Galerie',
            ])
            ->add('descG', TextareaType::class, [
                'label' => 'Description de la Galerie',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'id' => 'descriptionField', 
                ],
            ])
            ->add('photoG', FileType::class, [
                'label' => 'Photo de la Galerie',
                'mapped' => false, // Ne pas mapper à l'entité
                'required' => true,
            ])
            ->add('typeG', TextType::class, [
                'label' => 'Type de la Galerie',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Galerie::class,
        ]);
    }
}
