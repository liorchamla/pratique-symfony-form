<?php

/**
 * TROISIEME PARTIE : EXPLOITER LA PUISSANCE DES DTO (MODELS) ET DU VALIDATOR A LA FOIS
 * -----------------------
 * Dans la dernière section, on a vu comment mettre en place la validation via le composant symfony/validator
 * On a aussi vu qu'on pouvait demander au formulaire de représenter ses données via un objet d'une classe que l'on spécifie
 * dans l'option data_class.
 * 
 * Et si nous pouvions réunir les deux notions en une seule ?
 * 
 * EXTRAIRE LES CONTRAINTES DE VALIDATION DANS UN FICHIER DE CONFIGURATION
 * -----------------------
 * Ce que l'on va pouvoir faire, c'est séparer les deux responsabilités : Pour l'instant, le poids de la validation pèse sur 
 * notre formulaire via la classe RegistrationType qui construit le formulaire. Ce que nous pouvons faire c'est :
 * - Ne plus s'occuper de la validation lors de la construction du formulaire mais uniquement lors de la soumission
 * - Extraire les contraintes de validation dans un fichier de configuration plus simple à écrire / maintenir
 * 
 * PASSER PAR LE COMPOSANT SYMFONY/YAML
 * ------------------
 * Pour pouvoir décrire la validation via un fichier de configuration YAML, on devra installer le composant symfony/yaml (composer
 * require symfony/yaml) afin que le composant de validation puisse lire ces fichiers !
 * 
 * Pour vraiment comprendre comment cela se passe il vous faudra examiner les fichiers suivants :
 * - configuration.php => On y met en place la configuration nécessaire pour le validateur
 * - validation.yml => La configuration de validation
 * - RegistrationType.php => On a supprimé toute la logique de validation (contraintes) du FormBuilder
 */

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/RegistrationType.php';
require __DIR__ . '/RegistrationData.php';

/**
 * MISE EN COMMUN DE LA CONFIGURATION DU FACTORY :
 * ----------------
 * La fabrique de formulaire (FormFactory) pourrait être utilisé dans d'autres fichiers, on peut donc le mettre
 * en place à part.
 */
require __DIR__ . '/configuration.php';

// L'objet dans lequel on va stocker à termes les informations du formulaire
$data = new RegistrationData();

/**
 * RENCONTRE AVEC LE FORMBUILDER ET DATA_CLASS:
 * -----------------
 * 
 * Dans ce cours, je vous montre que le FormBuilder peut prendre une option très importante : data_class
 * Elle désigne au FormBuilder sous quelle forme nous voulons représenter les données du formulaire ! On n'est donc plus cantonné
 * au simple tableau associatif, on peut tout à fait désormais obtenir un objet d'une classe donnée (y compris une entité) !
 * 
 * Notez que cette option peut être donnée dans la classe RegistrationType directement afin de factoriser ce code et de ne pas
 * avoir à préciser systématiquement lors de l'utilisation du RegistrationType qu'on doit utiliser un objet RegistrationData
 * 
 * Par ailleurs, vous voyez qu'on peut passer l'objet $data dès la construction du FormBuilder par notre FormFactory ! A partir
 * de ce moment là, le formulaire prend en charge cet objet et s'en sert pour trouver les données et aussi pour les stocker
 * lors de la soumission !
 */
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
    'constraints' => [
        // C'est la seule validation qu'on ajoute à la main car elle n'est pas utile lors du edit.php
        // On l'ajoute donc ici lors de l'ajout du champ, le reste des validations est décrit dans le fichier
        // validation.yml
        new Assert\NotBlank(['message' => 'Vous n\'avez pas accepté les termes du réglement'])
    ]
]);

/** @var Form */
$form = $builder->getForm();


/**
 * LIER LES DONNEES AU FORMULAIRE :
 * ------------
 * A partir du moment où nous travaillons avec un objet ou un tableau, nous pouvons lier ces données au formulaire
 * (vous êtes familiers de cela depuis le début dans le fichier edit.php)
 * 
 * On peut donc créer un objet $data qu'on donnera au formulaire et sur lequel il travaillera !
 */
$form->handleRequest();


/**
 * DEBUT DE L'ALGORITHME DE TRAITEMENT :
 * -----------------
 * Si le formulaire a été soumis, il faut alors extraire les données envoyées, les valider et ensuite faire le traitement voulu
 */
if ($form->isSubmitted() && $form->isValid()) {
    /**
     * UTILISATION DE MODELS :
     * --------------
     * A partir du moment où on utilise un model (une classe de données, qu'on appelle aussi DTO pour Data Transfer Object)
     * Le formulaire ne se contente plus de nous donner les informations soumises via un tableau associatif, il va carrément
     * créer et remplir un objet de la classe qu'on lui désigne en option "data_class".
     * 
     * Ici il s'agit d'une classe RegistrationData
     * 
     * IMPORTANT :
     * -------------
     * Notez que $data est objet, et que le formulaire agit donc par défaut PAR REFERENCE SUR L'OBJET $data, si bien que je n'ai
     * même pas forcément à appeler $form->getData() pour récupérer les données : le formulaire a travaillé directement avec
     * l'objet $data déjà existant (déclaré et passé au FormBuilder plus haut dans le fichier)
     * 
     * Nous pouvons donc commenter ici la récupération des données ($form->getData()) même si on rappellera qu'on peut toujours
     * l'utiliser si on le souhaite.
     */

    /** @var RegistrationData */
    // $data = $form->getData();

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
