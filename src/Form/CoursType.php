<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomC',TextType::class ,[
                'required' => false,
                'attr' => ['class'=>'form-control']
            ])
            ->add('categC',TextType::class ,[
                'required' => false,
                'attr' => ['class'=>'form-control']
            ])
            ->add('contenuC',TextareaType::class,[
                'required' => false,
                'attr' => ['class'=>'form-control']
            ])
            ->add('dateC', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
            ])
            ->add('heureC', TimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
            ])
            ->add('image', FileType::class, [
                'label' => 'Course Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'maxSize' => '2M',
                        'mimeTypesMessage' => "L'image doit être au format JPG ou PNG.",
                        'maxSizeMessage'=>"L'image ne doit pas dépasser 2 Mo."
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
