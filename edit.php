<?php

/**
 * BIENVENUE DANS CE COURS SUR LES FORMULAIRES !
 * -----------------------
 * Dans ce fichier, on aborde un formulaire pré-rempli par des valeurs particulières pour voir ce qui change par rapport à un formulaire vierge
 */

use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * MISE EN PLACE DE LA VALIDATION DES DONNEES GRACE AU COMPOSANT SYMFONY/VALIDATOR
     * -----------------
     * Le composant de validation de symfony (composer require symfony/validation) est livré avec deux parties essentielles :
     * 1) Des règles de validation représentées sous forme de différentes classes qui décrivent des contraintes 
     * 2) Un validateur qui va analyser des données au regard de ces règles et voir si tout colle
     * 
     * Le validateur permet 3 types de validations :
     * 1) La validation d'une SIMPLE valeur au regard d'une ou plusieurs règles
     * 2) La validation d'un tableau entier au regard d'une ou plusieurs règles
     * 3) La validation d'un objet (nécessite un fichier de configuraiton YAML (donc le composant symfony/yaml) ou des annotations (donc les librairies doctrine/annotations et doctrine/cache)
     * 
     * Ici, nous souhaitons valider le tableau $data (extrait de la requête HTTP par le formulaire) avec certaines règles simples.
     * 
     * Commençons par définir les règles (notez que le namespace Assert a été importé en tant qu'alias en haut de ce fichier):
     */

    $validationConstraints = new Assert\Collection([ // Nous avons une collection de règles
        'firstName' => [
            new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
            new Assert\Length(['min' => 3, 'minMessage' => 'Le prénom doit contenir au moins 3 caractères'])
        ],
        'lastName' => [
            new Assert\NotBlank(['message' => 'Le nom de famille est obligatoire']),
            new Assert\Length(['min' => 3, 'minMessage' => 'Le nom de famille doit contenir au moins 3 caractères'])
        ],
        'email' => [
            new Assert\NotBlank(['message' => 'L\'adresse email est obligatoire']),
            new Assert\Email(['message' => 'L\'adresse email n\'est pas au format valide'])
        ],
        'phone' => new Assert\NotBlank(['message' => 'Le numéro de téléphone est obligatoire']),
        'position' => [
            new Assert\NotBlank(['message' => 'Vous devez choisir une position']),
            new Assert\Regex(['pattern' => '/^developer|tester$/', 'message' => 'Le poste choisi n\'existe pas'])
        ]
    ]);

    /**
     * CREATION DU VALIDATEUR :
     * ------------------
     * Pour pouvoir vérifier si le tableau $data se conforme aux règles que l'on a mis en place, on dispose d'un objet
     * $validator qui va faire ces vérifications puis nous livrer une liste des éventuelles erreurs.
     * 
     * Créons ce $validator et lançons le cycle de validation 
     */

    $validator = Validation::createValidator();

    /**
     * VALIDATION DU TABLEAU PAR RAPPORT AUX REGLES :
     * ----------------
     * En utilisatn la méthode $validator->validate($data, $validationConstraints) on demande au validator de vérifier
     * chacune des données du tableau $data par rapport aux contraintes qui correspondent.
     * 
     * La méthode nous répond un objet de la classe ConstraintViolationList qui est bien pratique pour repérer, exploiter, compter
     * les erreurs de validation qui ont eu lieu.
     * 
     * Ce tableau contient des objets de la classe ConstraintViolation qui représentent chacun une violation de règle. On peut donc
     * le transformer en un tableau plat qui ressemblera plus à ce qu'on avait auparavant
     */
    $violations = $validator->validate($data, $validationConstraints);
    $isValid = $violations->count() === 0;

    $errors = [];

    foreach ($violations as $violation) {
        // Par défaut, le propertyPath sera "[firstName]" par exemple, on veut donc supprimer les crochets autour
        $fieldName = str_replace(['[', ']'], '', $violation->getPropertyPath());
        $message = $violation->getMessage();

        $errors[$fieldName] = $message;
    }
    //Remplace l'ancien :
    // if (!$data['firstName']) {
    //     $isValid = false;
    //     $errors['firstName'] = 'Le prénom est obligatoire';
    // }
    // if ($data['firstName'] && mb_strlen($data['firstName']) < 3) {
    //     $isValid = false;
    //     $errors['firstName'] = 'Le prénom doit avoir au moins 3 caractères';
    // }
    // if (!$data['lastName']) {
    //     $isValid = false;
    //     $errors['lastName'] = 'Le nom de famille est obligatoire';
    // }
    // if ($data['lastName'] && mb_strlen($data['lastName']) < 3) {
    //     $isValid = false;
    //     $errors['lastName'] = 'Le nom de famille doit avoir au moins 3 caractères';
    // }
    // if (!$data['email']) {
    //     $isValid = false;
    //     $errors['email'] = 'L\'email est obligatoire !';
    // }
    // if ($data['email'] && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    //     $isValid = false;
    //     $errors['email'] = 'L\'email n\'est pas au format valide !';
    // }
    // if (!$data['phone']) {
    //     $isValid = false;
    //     $errors['phone'] = 'Le téléphone est obligatoire !';
    // }
    // if (!$data['position']) {
    //     $isValid = false;
    //     $errors['position'] = 'La position souhaitée est obligatoire !';
    // }
    // if ($data['position'] && !in_array($data['position'], ['developer', 'tester'])) {
    //     $isValid = false;
    //     $errors['position'] = 'La position que vous avez choisi n\'est pas valide !';
    // }

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
