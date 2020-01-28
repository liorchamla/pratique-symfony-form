<?php

/**
 * DERNIERE PARTIE : AUTOMATISER DES TRAITEMENTS VIA LES EVENEMENTS DE FORMULAIRE
 * -----------------------
 * Nous y sommes, la dernière partie de ce long cheminement au cours : une partie très importante.
 * 
 * Le composant symfony/form permet au formulaire d'émettre des événements lors de son utilisation, des événements que l'on peut
 * intercepter au moment où ils sont émis pour travailler sur le formulaire. Les évenements utilisables sont :
 * 
 * 1) Les événements de construction :
 * - PRE_SET_DATA : émis après l'appel de la fonction setData() AVANT que les données ne soient adoptées par le formulaire
 * - POST_SET_DATA : émis après l'appel de la fonction setData() APRES que les données soient adoptées par le formulaire
 * 2) Les événements de soumission :
 * - PRE_SUBMIT : émis lors de la soumission du formuilaire (fonction submit()) AVANT de transformer les données de la requête
 * - SUBMIT : émis lors de la soumission du formulaire APRES avoir transformé les données de la requête 
 * - POST_SUBMIT : émis lors de la soumission du formulaire APRES avoir créé l'objet qui contient les données finales
 * 
 * NOTRE EXEMPLE :
 * --------------
 * Jusqu'à maintenant, la différence principale entre index.php et edit.php c'est que dans l'index.php on part d'un formulaire 
 * vierge alors que dans l'edit.php on part sur un formulaire pré-rempli par des données. 
 * 
 * Dans les deux cas nous passons un objet RegistrationData à notre formulaire, pour l'index, c'est un objet vide, pour l'edit 
 * c'est un objet rempli de données.
 * 
 * Dans le cas de l'index, on veut donc une case "agreeTerms" dans le formulaire que nous ne souhaitons pas avoir dans l'edit.
 * 
 * On peut déporter ce traitement (l'ajout d'un champ "agreeTerms") directement dans le formulaire :
 * - Si les données passées au formulaire sont pré-remplies, on ne touche à rien
 * - Si les données sont vierges, c'est qu'on est sur une INSCRIPTION et donc on veut la case à cocher
 * 
 * UTILISONS L'EVENEMENT PRE_SET_DATA :
 * ------------
 * On peut donc se brancher à l'événement PRE_SET_DATA du formulaire pour décider, en fonction des données passées au formulaire
 * lors de sa création, si l'on veut ajouter ou pas le champ "agreeTerms"
 * 
 * COMMENT FAIRE LA DIFFERENCE ENTRE FORMULAIRE VIERGE OU PRE REMPLI ?
 * ----------------
 * Dans les deux cas nous passons au formulaire un objet de la classe RegistrationData. Ajoutons y un champ public $id afin de
 * différencier un objet vierge ($id vide) ou pré-rempli ($id défini).
 * 
 * Pour voir les changements principaux, concentrez vous sur :
 * - RegistrationData.php => Ajout du champ public $id
 * - index.php => On retire l'ajout "manuel" du champ "agreeTerms"
 * - edit.php => On donne à l'objet $data un id
 * - RegistrationType.php => On met en place un listener sur l'événement PRE_SET_DATA
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

/**
 * CETTE PARTIE N'EST PLUS NECESSAIRE :
 * -----------
 * En effet, grâce à la gestion des événements au sein même du formulaire, cette partie est gérée directement dans 
 * RegistrationType.php
 */
// $builder->add('agreeTerms', CheckboxType::class, [
//     'label' => 'J\'accèpte les termes du réglement',
//     'constraints' => [
//         // C'est la seule validation qu'on ajoute à la main car elle n'est pas utile lors du edit.php
//         // On l'ajoute donc ici lors de l'ajout du champ, le reste des validations est décrit dans le fichier
//         // validation.yml ou dans nos annotations
//         new Assert\NotBlank(['message' => 'Vous n\'avez pas accepté les termes du réglement'])
//     ]
// ]);

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
