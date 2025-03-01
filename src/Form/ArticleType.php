<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Magasin;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $magasins = $options['magasins'];

        $builder
            ->add('nomA', null, [
                'label' => 'Nom du produit',
            ])
            ->add('prixA', NumberType::class, [
                'label' => 'Prix du produit',
                'required' => true,
            ])
            ->add('descA', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'rich-text-editor'],
                'required' => true,
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité disponible',
                'required' => true,
                
            ])
            ->add('magasin', EntityType::class, [
                'label' => 'Choix du Magasin',
                'class' => Magasin::class,
                'choices' => $magasins,
                'choice_label' => function ($magasin) {
                    return $magasin->getNomM();
                },
                'placeholder' => 'Choisissez un magasin',
            ])
            ->add('imagePath', FileType::class, [
                'label' => 'Image (PNG, JPG, JPEG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG)',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'magasins' => [],
        ]);
    }
}
