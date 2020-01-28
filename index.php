<?php

use App\Form\Model\RegistrationData;
use App\Form\RegistrationType;
use Symfony\Component\Form\Form;

require __DIR__ . '/config/configuration.php';

// L'objet dans lequel on va stocker à termes les informations du formulaire
$data = new RegistrationData();

// Construction du formulaire
$builder = $formFactory->createBuilder(RegistrationType::class, $data, [
    'data_class' => RegistrationData::class,
    'csrf_field_name' => 'csrf_token',
    'csrf_message' => 'Vous n\'avez pas respecté la politique de sécurité CSRF pour ce formulaire !',
]);

/** @var Form */
$form = $builder->getForm();

// Traitement de la requête
$form->handleRequest();

if ($form->isSubmitted() && $form->isValid()) {
    // Traitement (enregistrement en base de données ou envoi de mail, peu importe)
    // TODO ...

    // Affichage
    include __DIR__ . '/views/result.html.php';
    return;
}

$twig->display('form.html.twig', [
    'form' => $form->createView()
]);
