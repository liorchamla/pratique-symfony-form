<?php

/**
 * BIENVENUE DANS CE COURS SUR LES FORMULAIRES !
 * -----------------------
 * Dans ce fichier, on aborde un formulaire pré-rempli par des valeurs particulières pour voir ce qui change par rapport à un formulaire vierge
 */

use Symfony\Component\Form\Form;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/RegistrationType.php';
require __DIR__ . '/RegistrationData.php';

// On imagine qu'on récupère des valeurs à partir d'une base de données ou autre :
$data = new RegistrationData();
$data->firstName = 'Lior';
$data->lastName = 'Chamla';
$data->email = 'lior@gmail.com';
$data->phone = '0612345678';
$data->position = 'developer';


/**
 * MISE EN COMMUN DE LA CONFIGURATION DU FACTORY :
 * ----------------
 * La fabrique de formulaire (FormFactory) pourrait être utilisé dans d'autres fichiers, on peut donc le mettre
 * en place à part.
 */
require __DIR__ . '/configuration.php';

/**
 * RENCONTRE AVEC LE FORMBUILDER :
 * -----------------
 * Voir le fichier index.php pour plus de détails sur ce point
 */
$builder = $formFactory->createBuilder(RegistrationType::class, null, [
    'csrf_field_name' => 'csrf_token',
    'csrf_message' => 'Vous n\'avez pas respecté la politique de sécurité CSRF pour ce formulaire !',
    'data_class' => RegistrationData::class
]);

/** 
 * CONSTRUIRE UN FORMULAIRE AVEC LE FORMBUILDER :
 * ----------------
 * Voir le fichier index.php pour plus de détails sur ce point
 */

/** @var Form */
$form = $builder->getForm();

/**
 * PRE-REMPLIR LE FORMULAIRE 
 * -----------------
 * On peut donner des données existantes au formulaire afin qu'il les prenne en compte !
 * 
 * C'est très simple : il suffit d'appeler la méthode $form->setData($data) en partant du principe que $data
 * est un tableau associatif dont les clés correspondent aux champs configurés sur le formulaire.
 */
$form->setData($data);

/**
 * TRAITEMENT DE LA REQUETE 
 * ------------------
 * Voir le fichier index.php pour plus de détails sur ce point
 */
$form->handleRequest();

/**
 * DEBUT DE L'ALGORITHME DE TRAITEMENT :
 * -----------------
 * Si le formulaire a été soumis, il faut alors extraire les données envoyées, les valider et ensuite faire le traitement voulu
 */
if ($form->isSubmitted() && $form->isValid()) {
    // Si tout va bien, on traite et on affiche le résultat
    // Traitement (enregistrement en base de données ou envoi de mail, peu importe)
    // TODO ...

    // Affichage
    include __DIR__ . '/views/result.html.php';
    return;
}

$errors = [];
$violations = $form->getErrors(true);
foreach ($violations as $violation) {
    $fieldName = str_replace(['[', ']'], '', (string) $violation->getOrigin()->getPropertyPath());
    $message = $violation->getMessage();

    $errors[$fieldName] = $message;
}

// Affichage du formulaire
include __DIR__ . '/views/form.html.php';
