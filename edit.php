<?php

/**
 * BIENVENUE DANS CE COURS SUR LES FORMULAIRES !
 * -----------------------
 * Dans ce fichier, on aborde un formulaire pré-rempli par des valeurs particulières pour voir ce qui change par rapport à un formulaire vierge
 */

// On imagine qu'on récupère des valeurs à partir d'une base de données ou autre :
$firstName = 'Lior';
$lastName = 'Chamla';
$email = 'lior@gmail.com';
$phone = '0612345678';
$position = 'developer';

// Si il y a quelque chose dans le POST c'est qu'on a bien essayer de soumettre quelque chose !
$isSubmitted = !empty($_POST);

/**
 * DEBUT DE L'ALGORITHME DE TRAITEMENT :
 * -----------------
 * Si le formulaire a été soumis, il faut alors extraire les données envoyées, les valider et ensuite faire le traitement voulu
 */
if ($isSubmitted) {
    var_dump($_POST);

    // Exemple de validation
    $isValid = true;
    $errors = [];

    // Extraction des champs afin de les examiner puis de les utiliser
    $firstName = $_POST['firstName'] ? trim($_POST['firstName']) : false;
    $lastName = $_POST['lastName'] ? trim($_POST['lastName']) : false;
    $email = $_POST['email'] ? trim($_POST['email']) : false;
    $phone = $_POST['phone'] ? trim($_POST['phone']) : false;
    $position = $_POST['position'] ?? false;
    $agreeTerms = $_POST['agreeTerms'] ?? false;

    // Début de la validation
    if (!$firstName) {
        $isValid = false;
        $errors['firstName'] = 'Le prénom est obligatoire';
    }
    if ($firstName && mb_strlen($firstName) < 3) {
        $isValid = false;
        $errors['firstName'] = 'Le prénom doit avoir au moins 3 caractères';
    }
    if (!$lastName) {
        $isValid = false;
        $errors['lastName'] = 'Le nom de famille est obligatoire';
    }
    if ($lastName && mb_strlen($lastName) < 3) {
        $isValid = false;
        $errors['lastName'] = 'Le nom de famille doit avoir au moins 3 caractères';
    }
    if (!$email) {
        $isValid = false;
        $errors['email'] = 'L\'email est obligatoire !';
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errors['email'] = 'L\'email n\'est pas au format valide !';
    }
    if (!$phone) {
        $isValid = false;
        $errors['phone'] = 'Le téléphone est obligatoire !';
    }
    if (!$position) {
        $isValid = false;
        $errors['position'] = 'La position souhaitée est obligatoire !';
    }
    if ($position && !in_array($position, ['developer', 'tester'])) {
        $isValid = false;
        $errors['position'] = 'La position que vous avez choisi n\'est pas valide !';
    }
    if (!$agreeTerms) {
        $isValid = false;
        $errors['agreeTerms'] = 'Vous n\'avez pas accepté les termes du réglement !';
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
