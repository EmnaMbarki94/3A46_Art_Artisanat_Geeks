<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    $user=$options['user'];

    $builder
    ->add('contenuQ', TextType::class, [
        'label' => 'Contenu de la question',
        'attr' => ['class' => 'form-control']
    ])
    ->add('quiz', EntityType::class, [
        'class' => Quiz::class,
        'choice_label' => 'titreC',
        'label' => 'Quiz associé',
        'attr' => ['class' => 'form-select'],
        'query_builder' => function (EntityRepository $er) use ($user) {
            return $er->createQueryBuilder('q')
                ->leftJoin('q.cours', 'c')
                ->leftJoin('c.user', 'u')
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $user->getId());
            },
        
    ]);

    $builder
    ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    $form = $event->getForm();
    $data = $event->getData();


    $responses = $data->getReponses() ?? ['', '', '', ''];

    for ($i = 0; $i < 4; $i++) {
        $form->add("response_$i", TextType::class, [
            'mapped' => false,
            'required' => true,
            'label' => "Réponse " . ($i + 1),
            'attr' => ['class' => 'form-control'],
            'data' => $responses[$i] ?? '',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => "La réponse " . ($i + 1) . " ne doit pas être vide."
                ]),
            ],
        ]);
    }
});

$builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
    $form = $event->getForm();
    $question = $event->getData();

    $responses = [];
    for ($i = 0; $i < 4; $i++) {
        $responses[] = $form->get("response_$i")->getData();
    }

    $question->setReponses($responses);
});
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'user'=>null,
        ]);
    }
}
