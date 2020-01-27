<?php

use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * CREATION D'UNE FABRIQUE DE FORMULAIRE :
 * ------------
 * Pour créer et gérer des forlulaires avec le composant symfony/form, il faut créer une fabrique qui nous permettra de les construire.
 * 
 * Les fabriques de formulaire sont des objets qui implémentent l'interface FormFactoryInterface
 * 
 * PERSONNALISER LA FABRIQUE DE FORMULAIRES :
 * ------------
 * Si l'on souhaite que nos futurs formulaires bénéficient d'une fonciguration particulière, nous devons configurer la fabrique
 * de formulaires.
 * 
 * Par exemple, si nous souhaitons que nos formulaires bénéficient d'une validation, nous devons indiquer à la FormFactory
 * qu'elle possède une extension permettant de valider (évidemment).
 * 
 * LES EXTENSIONS DU COMPOSANT FORM :
 * ------------
 * En effet, le composant symfony/form est extensible ! Il ne possède pas en lui même nombre de choses qu'on aimerait pourtant
 * lui déléguer : validations, traductions, sécurité, etc.
 * 
 * Les extensions reposent sur des classes qui implémentent l'interface FormExtensionInterface, ce qui veut dire que vous pouvez
 * tout à fait vous même créer une extension au système de formulaires de Symfony ;)
 */

/**
 * CREATION D'UN VALIDATEUR :
 * ----------
 * Le composant Validator vient avec deux apports principaux :
 * - Des contraintes de validation qui représente des règles et sont des classes 
 * - Un validateur qui va analyser une ou plusieurs données et voir si elles sont conformes aux règles prévues
 * 
 * Pour créer un validateur, on peut se servir de la méthode statique Validation::createValidator().
 * 
 * Il existe aussi une méthode Validation::createValidatorBuilder() qui nous permet de configurer le validateur, et c'est
 * notamment grâce à ça qu'on pourra configurer un validateur pour se servir d'un fichier de configuration YAML ou encore
 * d'annotations qui se trouvent sur une classe donnée, etc.
 */

$validator = Validation::createValidator();

/**
 * CONFIGURATION DE LA FORMFACTORY
 * --------------
 * Si l'on veut que le validateur soit utilisé avec nos formulaire, il faut que la fabrique de formulaire soit au courant
 * On va donc devoir lui ajouter une extension qui comporte notre validateur
 */
$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new ValidatorExtension($validator))
    ->getFormFactory();

// Remplace l'ancien :
// $formFactory = Forms::createFormFactory();
