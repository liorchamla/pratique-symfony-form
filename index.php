<?php

/**
 * DEUXIEME PARTIE : EXPLOITER LE COMPOSER SYMFONY/VALIDATOR AVEC LE COMPOSANT SYMFONY/FORM
 * -----------------------
 * Après avoir installé le composant (composer require symfony/form) nous bénéficions de ses fonctionnalités
 * 
 * Désormais, nous connaissons les bases concernant le composant symfony/form et nous avions vu qu'il ne s'occupait pas de
 * validation en lui-même, mais qu'il préférait donner cette tâche à une librairie faite dans ce but.
 * 
 * Nous allons donc voir comment exploiter le composant symfony/validator (composer require symfony/validator) avec
 * le composant symfony/form.
 * 
 * ATTENTION :
 * -------------
 * Des changements très importants vont avoir dans le fichier configuration.php où nous créons un FormFactory
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
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/RegistrationType.php';

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
 * Les formulaires créés avec le composant symfony/form sont représentés par des objets de la classe Form.
 * Ces objets sont particulièrement complexes à construire : une grande puissance demande une grande complexité !
 * 
 * MAIS PAS DE SOUCIS : On nous donne un outil qui nous permet de construire ces formulaires hyper simplement, dites bonjour au FormBuilder.
 * 
 * Le builder nous permet de configurer un formulaire sans peine, et nous est fourni de différente façon par notre fabrique de 
 * formulaire !
 * 
 * Enfin, on peut pré-configurer un builder de sorte qu'il se servie d'une classe FormType déjà existante (voir le fichier RegistrationType pour plus d'informations)
 */
$builder = $formFactory->createBuilder(RegistrationType::class);

/** 
 * CONSTRUIRE UN FORMULAIRE AVEC LE FORMBUILDER :
 * ----------------
 * Malgré le fait qu'on ait donné un formulaire prédéfinir au builder lors de sa création, nous pouvons nous en servir
 * pour enrichir ou modifier le formulaire.
 * 
 * On peut donc ajouter des champs ou en supprimer (par rapport à ce qui était configuré dans le RegistrationType) !
 * 
 * Ici par exemple, nous souhaitons ajouter un champ "agreeTerms" qui sera une checkbox en utilisant toujours la méthode 
 * $builder->add(...). On pourrait aussi supprimer des champs avec la méthode $builder->remove()
 */
$builder->add('agreeTerms', CheckboxType::class);

/**
 * IMPORTANT A SAVOIR :
 * -------------------
 * Ici, nous utilisons un formulaire préconfiguré (RegistrationType) et donc le nom du formulaire devient par défaut "registration".
 * 
 * Ce qui veut dire que les champs s'appellent désormais "registration[firstName]" ou "registration[lastName]".
 */

/** @var Form */
$form = $builder->getForm();

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
    // Exemple de validation (remplacé ci-dessous)
    // $isValid = true;
    // $errors = [];

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

    // Ce champ est placé en supplément car il ne sera pas utilisé dans le formulaire d'édition
    $validationConstraints->fields['agreeTerms'] = new Assert\Required([
        new Assert\NotBlank(['message' => 'Vous n\'avez pas accepté les termes du réglement'])
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

    // Remplace l'ancien :
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
    // if (!$data['agreeTerms']) {
    //     $isValid = false;
    //     $errors['agreeTerms'] = 'Vous n\'avez pas accepté les termes du réglement !';
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
