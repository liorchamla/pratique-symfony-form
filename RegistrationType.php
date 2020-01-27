<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('position', ChoiceType::class, [
                'placeholder' => 'Choisissez un poste',
                'choices' => [
                    'Développeur' => 'developer',
                    'Testeur' => 'tester'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        /**
         * La fonction configureOptions nous permet de gérer les options passées à un formulaire lors de la création du 
         * FormBuilder
         * 
         * On peut donc aussi expliquer quelle est la valeur des options PAR DEFAUT (si on ne passe pas d'option de ce nom là)
         * et c'est donc ici qu'on peut dire que, par défaut, le formulaire doit représenter ses données avec la classe 
         * RegistrationData
         * 
         * Nous laissons ceci en commentaire pour l'instant car nous aimons le fait de choisir LORS DE LA CONSTRUCTION DU FORMBUILDER
         * la façon dont on veut représenter les données du formulaire.
         */

        // $resolver->setDefaults([
        //     'data_class' => RegistrationData::class
        // ]);
    }
}
