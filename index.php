<?php

/**
 * QUATRIEME PARTIE : DISPOSER D'UN RENDU SIMPLIFIE GRÂCE A TWIG
 * -----------------------
 * Pour l'instant, nous écrivons nous-même le HTML qui servira à afficher le formulaire. 
 * 
 * La conséquence c'est que le rendu HTML et la configuration du formulaire sont désynchronisés ! Si on ajoute un champ
 * à la configuration du formulaire, il n'apparaitra pas automatiquement dans le HTML. Pareil si on supprime un champ.
 * 
 * Plus encore, la construction du formulaire via le FormBuilder nous permet d'enrichir les champs avec beaucoup d'options
 * qui concernent l'affichage, et pour l'instant, nous ne tirons pas partie de cette puissance.
 * 
 * TWIG A LA RESCOUSSE :
 * -----------------
 * On va donc faire appel à Twig qui va nous permettre de profiter à 100% de toutes les options d'affichage du composant
 * symfony/form.
 * 
 * Par défaut, la librairie Twig ne dispose pas du tout de fonctionnalités en rapport avec le composant symfony/form MAIS
 * Symfony a créé un BRIDGE (un pont) : une librairie qui va ajouter des extensions à Twig de façon à ce qu'il sache
 * travailler avec les formulaires du composant Form.
 * 
 * Après l'avoir installé (composer require symfony/twig-bridge) il va falloir configurer Twig et l'utiliser dans nos 
 * templates.
 * 
 * Pour voir les changements principaux, concentrez vous sur :
 * - configuration.php => Mise en place du moteur Twig
 * - view/form.html.twig => On modifie l'affichage du formulaire pour faire du Twig
 * - index.php et edit.php => On n'inclue plus de fichiers PHP pour l'affichage mais on fait du rendu Twig
 * - RegistrationType.php => On met en place des options d'affichage comme les labels et les placeholders
 */

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/RegistrationType.php';
require __DIR__ . '/RegistrationData.php';
require __DIR__ . '/configuration.php';

// L'objet dans lequel on va stocker à termes les informations du formulaire
$data = new RegistrationData();

// Construction du formulaire
$builder = $formFactory->createBuilder(RegistrationType::class, $data, [
    // Par défaut, l'identifiant du token est le même que le nom du formulaire (ici "registration")
    // On peut bien sur le modifier via l'option "csrf_token_id"
    // 'csrf_token_id' => 'registration',
    'csrf_field_name' => 'csrf_token',
    'csrf_message' => 'Vous n\'avez pas respecté la politique de sécurité CSRF pour ce formulaire !',
    // On désigne la classe avec laquelle on souhaite représenter les données
    'data_class' => RegistrationData::class
]);

$builder->add('agreeTerms', CheckboxType::class, [
    'label' => 'J\'accèpte les termes du réglement',
    'constraints' => [
        // C'est la seule validation qu'on ajoute à la main car elle n'est pas utile lors du edit.php
        // On l'ajoute donc ici lors de l'ajout du champ, le reste des validations est décrit dans le fichier
        // validation.yml ou dans nos annotations
        new Assert\NotBlank(['message' => 'Vous n\'avez pas accepté les termes du réglement'])
    ]
]);

/** @var Form */
$form = $builder->getForm();

// Traitement de la requête
$form->handleRequest();


/**
 * DEBUT DE L'ALGORITHME DE TRAITEMENT :
 * -----------------
 * Si le formulaire a été soumis, il faut alors extraire les données envoyées, les valider et ensuite faire le traitement voulu
 */
if ($form->isSubmitted() && $form->isValid()) {
    // Traitement (enregistrement en base de données ou envoi de mail, peu importe)
    // TODO ...

    // Affichage
    include __DIR__ . '/views/result.html.php';
    return;
}

// Traitement des erreurs de validation (plus nécessaire avec le Twig Bridge)
// $errors = [];
// $violations = $form->getErrors(true);
// foreach ($violations as $violation) {
//     $fieldName = str_replace(['[', ']'], '', (string) $violation->getOrigin()->getPropertyPath());
//     $message = $violation->getMessage();

//     $errors[$fieldName] = $message;
// }

$twig->display('form.html.twig', [
    'form' => $form->createView()
]);
// Remplace l'ancien :
// Affichage du formulaire
// include __DIR__ . '/views/form.html.php';
