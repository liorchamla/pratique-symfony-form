<?php

use App\Form\Model\RegistrationData;
use App\Form\RegistrationType;
use Symfony\Component\Form\Form;

require __DIR__ . '/config/configuration.php';

// On imagine qu'on récupère des valeurs à partir d'une base de données ou autre :
$data = new RegistrationData();
$data->firstName = 'LIOR';
$data->lastName = 'CHAMLA';
$data->email = 'lior@gmail.com';
$data->phone = '0612345678';
$data->position = 'developer';

// Important : si l'objet possède un $id, alors on n'aura pas le champ "agreeTerms"
$data->id = 123;

// Création du formulaire
$builder = $formFactory->createBuilder(RegistrationType::class, $data, [
    'data_class' => RegistrationData::class,
    'csrf_field_name' => 'csrf_token',
    'csrf_message' => 'Vous n\'avez pas respecté la politique de sécurité CSRF pour ce formulaire !',
]);

/** @var Form */
$form = $builder->getForm();

$form->handleRequest();

if ($form->isSubmitted() && $form->isValid()) {
    // Si tout va bien, on traite et on affiche le résultat
    // Traitement (enregistrement en base de données ou envoi de mail, peu importe)
    // TODO ...

    // Affichage
    include __DIR__ . '/views/result.html.php';
    return;
}

$twig->display('form.html.twig', [
    'form' => $form->createView()
]);
