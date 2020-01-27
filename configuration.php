<?php

use Symfony\Component\Form\Forms;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/RegistrationType.php';

/**
 * CREATION D'UNE FABRIQUE DE FORMULAIRE :
 * ------------
 * Pour créer et gérer des forlulaires avec le composant symfony/form, il faut créer une fabrique qui nous permettra de les construire.
 * 
 * Les fabriques de formulaire sont des objets qui implémentent l'interface FormFactoryInterface
 */

$formFactory = Forms::createFormFactory();
