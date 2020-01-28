<?php

/**
 * CINQUIEME PARTIE : AUTOMATISER DES TRAITEMENTS SUR LES DONNEES AVEC LES DATA TRANSFORMERS
 * -----------------------
 * Nous arrivons pratiquement à la fin de notre découverte du composant symfony/form même si beaucoup de points n'ont pas
 * été abordés, nous vous laissons découvrir la documentation très riche et pratiquer afin de toujours en savoir plus !
 * 
 * Découvrons alors maintenant ce que sont les DataTransformers et comment ils peuvent nous aider à gérer une transformation
 * entre une donnée passée au formulaire et la donnée qui doit s'afficher et inversement.
 * 
 * Si un formulaire permet de transformer des données écrites dans une page HTML en données PHP (objet ou tableau) et inversement
 * alors un DataTransformer peut être vu comme une brique qui se place entre les deux univers afin de traiter et appliquer des
 * transformations sur les données.
 * 
 * Le DataTransformer le plus connu est celui qui est contenu dans les champs DateTimeType qui permettent en partant d'un objet
 * DateTime d'afficher une date au format texte, et d'une date au format texte d'obtenir un objet DateTime.
 * 
 * Si bien que dans le monde PHP, notre données est bien un objet DateTime pratique à utiliser, mais au sein du HTML, cela devient
 * un input classique.
 * 
 * TRANSFORMATION SUR LE PRENOM ET LE NOM
 * ------------------
 * Pour cet exemple un peu stupide, imaginons que l'on souhaite systématiquement afficher dans le formulaire HTML le nom et le 
 * prénom au format classique (comme par exemple "Lior" ou "Magali") mais que dans le monde PHP, on souhaite systématiquement 
 * que ces données soient en majuscules ("LIOR" ou "MAGALI").
 * 
 * On pourrait gérer ça à la main lors de la création ou de la soumission du formulaire, mais il faudrait alors répéter le même
 * traitement partout où on utilise le même formulaire.
 * 
 * C'est pourquoi il vaudrait mieux utiliser les DataTransformer qui, eux, sont directement liés au formulaire et seront donc
 * réutilisés à chaque fois que l'on souhaite utiliser ce formulaire !
 * 
 * Pour voir les changements principaux, concentrez vous sur :
 * - RegistrationType.php => On met en place le DataTransformer sous la forme d'une simple fonction (on verra ensuite comment
 * le faire sous forme de classe)
 * - index.php => Uniquement des var_dump qui vous permettent de constater le fonctionnement
 * - edit.php => On passe le prénom et le nom en MAJUSCULES et vous constaterez qu'ils s'affichent bien en minuscules dans le form
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
 * CONSTATATION SUR LA TRANSFORMATION DE DONNEES :
 * ------------
 * Ce var_dump n'est là que pour vous faire voir que désormais, si vous écrivez "Lior" pour le prénom ou "Chamla" pour le nom
 * dans le formulaire HTML, ce que le formulaire vous donnera au final ce sera bien "LIOR" ou "CHAMLA" !
 */
var_dump($form->getData());

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
