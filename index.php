<?php

/**
 * PREMIERE PARTIE : UTILISATION DU COMPOSANT SYMFONY/FORM
 * -----------------------
 * Après avoir installé le composant (composer require symfony/form) nous bénéficions de ses fonctionnalités
 * 
 * Les points problématiques soulevés dans le cours initial (en PHP NATIF) ne sont pas tous réglés par le composant :
 * - L'extraction des données à partir du POST est fastidieuse et demande une attention particulière ✅
 * => Ce problème peut être réglé plus simplement avec le composant 
 * - La réutilisation de ce code est complexe ✅
 * => Ce problème peut être réglé plus simplement avec le composant
 * - La testabilité est nulle (ou pratiquement impossible) ✅
 * => Ce problème peut être réglé plus simplement avec le composant
 * - La validation est très compliquée et fastidieuse aussi ❌
 * => Le composant ne prend pas en compte la validation MAIS peut faire appel à une librairie de validation tierce
 * - Le formulaire n'est pas sécurisé contre les attaques CSRF (on peut tout à fait l'appeler à partir d'une autre source que le site lui-même) ❌
 * => Le composant ne prend pas en comptre la sécurité CSRF MAIS peut faire appel à une librairie tierce !
 */

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;

require __DIR__ . '/vendor/autoload.php';

/**
 * CREATION D'UNE FABRIQUE DE FORMULAIRE :
 * ------------
 * Pour créer et gérer des forlulaires avec le composant symfony/form, il faut créer une fabrique qui nous permettra de les construire.
 * 
 * Les fabriques de formulaire sont des objets qui implémentent l'interface FormFactoryInterface
 */

$formFactory = Forms::createFormFactory();

/**
 * RENCONTRE AVEC LE FORMBUILDER :
 * -----------------
 * Les formulaires créés avec le composant symfony/form sont représentés par des objets de la classe Form.
 * Ces objets sont particulièrement complexes à construire : une grande puissance demande une grande complexité !
 * 
 * MAIS PAS DE SOUCIS : On nous donne un outil qui nous permet de construire ces formulaires hyper simplement, dites bonjour au FormBuilder.
 * 
 * Le builder nous permet de configurer un formulaire sans peine, et nous est fourni de différente façon par notre fabrique de 
 * formulaire !
 */
$builder = $formFactory->createBuilder();

/** 
 * CONSTRUIRE UN FORMULAIRE AVEC LE FORMBUILDER :
 * ----------------
 * Un formulaire, ce n'est qu'un ensemble de champs que l'on veut afficher, traiter, valider. 
 * Le builder va donc nous fournir une API (un ensemble de méthodes) pour configurer et créer le formulaire de nos rêves.
 * 
 * On peut évidemment décrire la structure et les types de champs que l'on souhaite traiter, mais aussi préciser l'action, la méthode et bien d'autres choses.
 * 
 * Chaque champ est représenté par un Type de champ (TextType, EmailType, TextareaType, CheckboxType, ChoiceType, ...). 
 * Le composant fourni des dizaines de types prédéfinis mais on peut en créer autant qu'on le veut.
 * 
 * IMPORTANT A SAVOIR :
 * --------------------
 * Par défaut, le builder va construire UN FORMULAIRE NOMME, il va donc donner un "nom" à notre formulaire ("form" par défaut)
 * Ce qui veut dire que ce qu'il s'attend à traiter ce ne sera pas par exemple un champ "firstName", mais un champ "form[firstName]"
 * 
 * Evidemment, on peut choisir le nom que l'on donne au fomulaire en appelant la méthode $formFactory->createNamedBuilder('nom_que_je_veux') au lieu de $formFactory->createBuilder() qui, je le rappelle, donnera le nom "form" par défaut.
 * 
 * On peut aussi choisir de ne pas utiliser de nom particulier (et donc de bien traiter un champ "firstName" plutôt que "form[firstName]) en utilisant la méthode $formFactory->createNamedBuilder('') avec une chaine vide :)
 * 
 * Pour ce cours, nous nous conformerons à ce que vous verrez le plus souvent et garderons le nom par défaut : "form"
 * Ce qui veut dire que nous devons mettre à jour les attributs "name" de nos champs HTML en "form[firstName]", "form[lastName]" etc.
 */

/** @var Form */
$form = $builder
    ->add('firstName', TextType::class)
    ->add('lastName', TextType::class)
    ->add('email', EmailType::class)
    ->add('phone', TextType::class)
    ->add('position', ChoiceType::class, [
        'placeholder' => 'Choisissez un poste',
        'choices' => [
            'Développeur' => 'developer',
            'Testeur' => 'tester'
        ]
    ])
    ->add('agreeTerms', CheckboxType::class)
    ->getForm();

/**
 * TRAITEMENT DE LA REQUETE 
 * ------------------
 * Maintenant que le formulaire est créé, on peut l'utiliser pour examiner la requête HTTP et :
 * 1) Savoir si le formulaire a été soumis
 * 2) Prenre en compte les valeurs soumises et les extraire de la réquête
 * 
 * Pour ce faire, on utilise la méthode handleRequest() du formulaire. 
 * 
 * Par défaut, le composant symfony/form nous propose un objet de la classe NativeRequestHandler dont 
 * le but est d'examiner les superglobales $_GET et $_POST à la recherche des informations du formulaire.
 * 
 * Plus tard, nous verrons que nous pourrons aussi travailler avec le composant symfony/http-foundation et sa classe Request
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
    if (!$data['agreeTerms']) {
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
