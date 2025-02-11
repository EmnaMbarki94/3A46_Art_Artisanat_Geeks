<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreC')
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'nomC',
                'placeholder' => 'Sélectionnez un cours',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.quiz', 'q')
                        ->where('q.id IS NULL');
                },
                'data' => $options['selected_course']?? null,
                'required' => true,
                
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'selected_course' => null,
        ]);
    }
}
