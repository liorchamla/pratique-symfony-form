<?php

namespace App\Form\CustomType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PositionType extends AbstractType
{
    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Poste souhaité',
            'placeholder' => 'Choisissez un poste',
            'choices' => [
                'Développeur' => 'developer',
                'Testeur' => 'tester'
            ]
        ]);
    }
}
