<?php

namespace App\Form;

use App\Entity\Artiste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Artiste1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Rap' => 'Rap',
                    'Jazz' => 'Jazz',
                    'Classique' => 'Classique',
                    'La musique électronique' => 'La musique électronique',
                    'La variété française' => 'La variété française',
                    'La variété internationale' => 'La variété internationale',
                    'Les musiques du monde' => 'Les musiques du monde',


                ]
            ])
            ->add('description', TextareaType::class)

            ->add('image', FileType::class, [
                // Le mapped=>false c'est pour ne pas stocker
                // l img dans la base de donnée
                'mapped' => false,
                'required' => false

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
        ]);
    }
}
