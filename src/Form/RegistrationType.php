<?php

namespace App\Form;

use App\Form\CustomType\NameType;
use App\Form\CustomType\PositionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', NameType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom']
            ])
            ->add('lastName', NameType::class, [
                'label' => 'Nom de famille',
                'attr' => ['placeholder' => 'Nom de famille']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Adresse email']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => 'Numéro de téléphone']
            ])
            ->add('position', PositionType::class);

        // Listener qui ajoute le champ "agreeTerms" si le formulaire est vierge
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data) {
                return;
            }

            $form = $event->getForm();

            if (!$data->id) {
                $form->add('agreeTerms', CheckboxType::class, [
                    'label' => 'J\'accèpte les termes du réglement',
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Vous n\'avez pas accepté les termes du réglement'])
                    ]
                ]);
            }
        });
    }
}
