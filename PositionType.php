<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * BONUS : UN CHAMP <SELECT> POUR LE POSTE SOUHAITE
 * -------------
 * Dans le formulaire RegistrationType, on a un champ "position" qui est un ChoiceType avec toutes ses options. Mais si on sait
 * qu'on va utiliser ce champ plusieurs fois et qu'on souhaite le centraliser, une bonne façon de factoriser et de créer un
 * type de champ personnalisé qui soit un enfant de ChoiceType::class mais qui le préconfigure !
 */
class PositionType extends AbstractType
{
    public function getParent()
    {
        // Notre champ est un enfant de ChoiceType, on bénéficie donc de toute la logique du ChoiceType
        return ChoiceType::class;
    }

    /**
     * MODIFIER LES OPTIONS PAR DEFAUT POUR LE CHAMP
     * -----------
     * Notre seul but c'est d'être un ChoiceType dont les options sont déjà mises en place. On donne donc des valeurs
     * par défaut à notre champ. Bien sur, ces valeurs par défaut peuvent être écrasés lors de l'utilisation du PositionType
     * dans un formulaire donné.
     * 
     * Grâce à ça, je peux utiliser 
     * $builder->add('position', PositionType::class)
     * 
     * au lieu de 
     * $builder->add('position', ChoiceType::class, [
     *  'label' => 'Poste souhaité',
     *  'placeholder' => 'Choisissez un poste',
     *  'choices' => [
     *      'Développeur' => 'developer',
     *      'Testeur' => 'tester'
     *  ]
     * ])
     * 
     */
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
