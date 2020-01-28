<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
         * MISE EN PLACE DU DATATRANSFORMER (PLUS NECESSAIRE AVEC LE NOUVEAU NAMETYPE)
         * ------------------------
         * Un DataTransformer s'attache à un champ en particulier et est représenté par 2 fonctions :
         * 1) Une fonction qui va transformer la données PHP en la donné qu'on veut afficher
         * 2) Une fonction va transformer la donnée soumise (du texte) en la donnée PHP que l'on souhaite
         * 
         * Dans notre cas, nous imaginons que nous souhaitons que le nom et le prénom soit 
         * 1) Transformés en style classique lors de l'affichage du formulaire (exemple : "Lior" et "Chamla")
         * 2) Transformés en majuscules lors de la récupération des données du formulaire (exemple : "LIOR" et "CHAMLA")
         * 
         * On va donc créer deux DataTransformer attachés aux champs "firstName" et "lastName"
         */
        // $nameDataTransformer = new NameDataTransformer();

        // $builder->get('firstName')->addModelTransformer($nameDataTransformer);
        // $builder->get('lastName')->addModelTransformer($nameDataTransformer);
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
