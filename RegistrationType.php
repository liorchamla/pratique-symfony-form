<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Assert\Length(['min' => 3, 'minMessage' => 'Le prénom doit contenir au moins 3 caractères'])
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire']),
                    new Assert\Length(['min' => 3, 'minMessage' => 'Le nom de famille doit contenir au moins 3 caractères'])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse email est obligatoire']),
                    new Assert\Email(['message' => 'L\'adresse email n\'est pas au format valide'])
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire'])
                ]
            ])
            ->add('position', ChoiceType::class, [
                'placeholder' => 'Choisissez un poste',
                'choices' => [
                    'Développeur' => 'developer',
                    'Testeur' => 'tester'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Vous devez choisir une position']),
                    new Assert\Regex(['pattern' => '/^developer|tester$/', 'message' => 'Le poste choisi n\'existe pas'])
                ]
            ]);
    }
}
