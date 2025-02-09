<?php

namespace App\Form;

use App\Entity\Galerie;
use App\Entity\PieceArt;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class PieceArtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomP',TextareaType::class)
            ->add('dateCrea', DateType::class,[
                'data' => new \DateTime(), // Date par défaut : aujourd'hui
            ])
            ->add('photoP', FileType::class, [
                'label' => 'Photo de la Pièce',
                'mapped' => false, 
            ])            
            
            ->add('descP',TextareaType::class, [
                'label' => 'Description de la pièce',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'id' => 'descriptionField', 
                ],
            ])

            ->add('galerie', EntityType::class, [
                'class' => Galerie::class,
                'choice_label' => 'nomG', 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PieceArt::class,
        ]);
    }
}
