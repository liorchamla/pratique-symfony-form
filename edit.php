<?php

/**
 * BIENVENUE DANS CE COURS SUR LES FORMULAIRES !
 * -----------------------
 * Dans ce fichier, on aborde un formulaire pré-rempli par des valeurs particulières pour voir ce qui change par rapport à un formulaire vierge
 */

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/RegistrationType.php';

// On imagine qu'on récupère des valeurs à partir d'une base de données ou autre :
$data = [
    'firstName' => 'Lior',
    'lastName' => 'Chamla',
    'email' => 'lior@gmail.com',
    'phone' => '0612345678',
    'position' => 'developer',
];
// Remplace l'ancien :
// $firstName = 'Lior';
// $lastName = 'Chamla';
// $email = 'lior@gmail.com';
// $phone = '0612345678';
// $position = 'developer';

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
$builder = $formFactory->createBuilder(RegistrationType::class);

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
if ($form->isSubmitted()) {
    // Remplace l'ancien :
    // $isSubmitted = !empty($_POST);
    // if ($isSubmitted) {

    // Exemple de validation
    $isValid = true;
    $errors = [];

    /**
     * EXTRACTION DES CHAMPS SOUMIS :
     * ----------------
     * Pour extraire les données, les superglobales $_POST ou $_GET ne nous intéressent plus ! Tout a été géré par le formulaire
     * lui-même ! Merveilleux.
     * 
     * Il suffit d'appeler la méthode $form->getData() pour obtenir un tableau qui représente les données soumises via
     * le formulaire HTML sous la forme d'un tableau associatif.
     * 
     * On peut ensuite faire appel à la fonction extract($data) pour extraire les informations du tableau associatif sous la forme
     * de variables simples. 
     * 
     * Nous garderons ici le tableau associatif afin de nous conformer à ce qu'on voit le plus souvent mais il faudra donc modifier
     * le reste de l'algorithme de validation ainsi que l'affichage du formulaire HTML.
     */
    $data = $form->getData();
    // Remplace l'ancien :
    // $firstName = $_POST['firstName'] ? trim($_POST['firstName']) : false;
    // $lastName = $_POST['lastName'] ? trim($_POST['lastName']) : false;
    // $email = $_POST['email'] ? trim($_POST['email']) : false;
    // $phone = $_POST['phone'] ? trim($_POST['phone']) : false;
    // $position = $_POST['position'] ?? false;
    // $agreeTerms = $_POST['agreeTerms'] ?? false;

    // Début de la validation
    if (!$data['firstName']) {
        $isValid = false;
        $errors['firstName'] = 'Le prénom est obligatoire';
    }
    if ($data['firstName'] && mb_strlen($data['firstName']) < 3) {
        $isValid = false;
        $errors['firstName'] = 'Le prénom doit avoir au moins 3 caractères';
    }
    if (!$data['lastName']) {
        $isValid = false;
        $errors['lastName'] = 'Le nom de famille est obligatoire';
    }
    if ($data['lastName'] && mb_strlen($data['lastName']) < 3) {
        $isValid = false;
        $errors['lastName'] = 'Le nom de famille doit avoir au moins 3 caractères';
    }
    if (!$data['email']) {
        $isValid = false;
        $errors['email'] = 'L\'email est obligatoire !';
    }
    if ($data['email'] && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errors['email'] = 'L\'email n\'est pas au format valide !';
    }
    if (!$data['phone']) {
        $isValid = false;
        $errors['phone'] = 'Le téléphone est obligatoire !';
    }
    if (!$data['position']) {
        $isValid = false;
        $errors['position'] = 'La position souhaitée est obligatoire !';
    }
    if ($data['position'] && !in_array($data['position'], ['developer', 'tester'])) {
        $isValid = false;
        $errors['position'] = 'La position que vous avez choisi n\'est pas valide !';
    }

    // Si tout va bien, on traite et on affiche le résultat
    if ($isValid) {
        // Traitement (enregistrement en base de données ou envoi de mail, peu importe)
        // TODO ...

        // Affichage
        include __DIR__ . '/views/result.html.php';
        return;
    }
}

// Affichage du formulaire
include __DIR__ . '/views/form.html.php';
