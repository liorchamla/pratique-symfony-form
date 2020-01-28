<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

require __DIR__ . '/NameDataTransformer.php';
require __DIR__ . '/NameType.php';
require __DIR__ . '/PositionType.php';

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

        /**
         * MISE EN PLACE D'UN LISTENER SUR L'EVENEMENT PRE_SET_DATA
         * ------------------------
         * L'événement PRE_SET_DATA est appelé après l'appel de la fonction setData() mais avant que les données
         * ne soient adoptées par le formulaire. On peut donc travailler en analysant les données passées et 
         * éventuellement modifier notre formulaire afin de répondre à nos besoins.
         * 
         * Ici le but est de voir si on nous a passé un objet RegistrationData qui soit vierge ou pré-rempli. Si il est vierge
         * on veut ajouter le champs "agreeTerms" au formulaire, sinon, on ne l'ajoute pas !
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // On peut récupérer les données passées au formulaire
            $data = $event->getData();

            // Si on ne nous a rien passé, on s'arrête
            if (!$data) {
                return;
            }

            // On peut récupérer le formulaire tel qu'il est à ce moment là :
            $form = $event->getForm();

            // Si l'objet qu'on nous a passé n'a pas d'id défini
            // Alors on veut ajouter le champ "agreeTerms"
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
