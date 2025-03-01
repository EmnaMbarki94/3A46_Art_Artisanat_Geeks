<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ReponseType extends AbstractType
{

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('descRep',TextareaType::class, [
            'label' => 'Description',
            'attr' => ['class' => 'rich-text-editor'],
            'required' => true,
        ])
        ->add('dateRep', DateType::class,[
            'data' => new \DateTime(), // Date par défaut : aujourd'hui
        
        ]);
        // ->add('reclamation', EntityType::class, [
        //     'class' => Reclamation::class,
        //     'choice_label' => 'id', // ou un autre champ pertinent
        //     'placeholder' => 'Sélectionner une réclamation',                 
        //     'required' => true,
        // ]);
        // ->add('submit', SubmitType::class, ['label' => 'Envoyer la réponse',]);
}

}

