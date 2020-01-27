<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
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
 * 
 * LECTURE DE FICHIERS DE CONFIGURATION YAML :
 * ----------
 * Nous pouvons donc utiliser la méthode Validation::createValidatorBuilder() pour configurer notre propre validateur avec
 * des détails tels que par exemple ... le fait de devoir lire des fichiers de configuration YAML !
 * 
 * LECTURE DES ANNOTATIONS DANS LES CLASSES :
 * -----------
 * Une autre façon d'expliquer les contraintes qui pèsent sur les propriétés d'une classe en plus du fichier YAML, ce sont les
 * annotations. Pour pouvoir les utiliser, il faudra utiliser Validation::createValidatorBuilder() afin de le configurer et
 * qu'il soit au courant qu'on veut utiliser les annotations !
 * 
 * ATTENTION :
 * ---------
 * Utiliser les annotations demande à ce qu'on créé un chargeur (loader) d'annotations. Cet objet nous est procuré par la librarie
 * doctrine/annotations et il faudra le configurer afin qu'il charge les classes d'annotation rencontrées
 */

// On se sert de l'autoloader qui expliquera à Doctrine où sont les classes correspondantes aux annotations qu'il 
// va rencontrer :
$loader = require  __DIR__ . '/vendor/autoload.php';

// On offre ce loader à Doctrine via son registre d'annotations :
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$validator = Validation::createValidatorBuilder()
    ->addYamlMapping('validation.yml')
    ->enableAnnotationMapping()
    ->getValidator();

// Remplace l'ancien :
// $validator = Validation::createValidator();

/**
 * MISE EN PLACE DE LA SECURITE PAR CSRF :
 * ----------------
 * Dans la plupart des cas, vous ne souhaitez pas qu'une formulaire puisse être soumis par l'extérieur de votre propre site.
 * 
 * Or pour l'instant, rien n'empêche quelqu'un d'envoyer une simple requête HTTP en POST vers le fichier index.php ou edit.php
 * afin de soumettre des données. Ce qui ouvre une brêche de sécurité assez inquiétante (sauf bien sur si c'est ce que vous 
 * aviez prévu dès le départ, comme dans le cas d'une API ouverte ou ce genre de choses).
 * 
 * Il est donc possible d'utiliser le composant symfony/security-csrf (composer require symfony/security-csrf) afin d'en doter
 * nos formulaires.
 * 
 * Une fois le composant installé, on peut désormais mettre en place cette politique de sécurisation sur notre FormFactory
 * afin que nos formulaires bénéficient désormais de cette protection !
 * 
 * LES OBJETS DE BASE (ET LA SESSION) :
 * ------------------
 * La politique de protection CSRF consiste à stoquer un jeton dans la session lors de la génération du formulaire
 * puis à vérifier que ce jeton est bien présent et valide lors de la réception du formulaire.
 * 
 * Pour ce faire, on a besoin :
 * 1) D'une brique qui se charge de générer des chaines aléatoire (le UriSafeTokenGenerator). C'est
 * un objet qui implémente l'interface TokenGeneratorInterface (donc vous pourriez créer le votre ;))
 * 2) D'une brique qui se charge de manipuler la session : si vous n'utilisez pas HttpFoundation, ce sera le NativeSessionTokenStorage
 * mais si vous utilisez HttpFoundation, vous pourriez utiliser le SessionTokenStorage, dans tous les cas, un objet qui 
 * implémente la TokenStorageInterface (là encore, vous pourriez même créer votre propre implémentation)
 * 3) D'une brique qui se charge de tout mettre en relation et de gérer le tout : le CsrfTokenManager, objet qui implémente
 * l'interface TokenManagerInterface, ce qui fait que vous pourriez là aussi créer le votre :)
 */
session_start();

$csrfGenrator = new UriSafeTokenGenerator();
$csrfStorage = new NativeSessionTokenStorage();
$csrfManager = new CsrfTokenManager($csrfGenrator, $csrfStorage);

/**
 * CONFIGURATION DE LA FORMFACTORY
 * --------------
 * Si l'on veut que le validateur soit utilisé avec nos formulaire, il faut que la fabrique de formulaire soit au courant
 * On va donc devoir lui ajouter une extension qui comporte notre validateur
 * 
 * Si l'on veut aussi que la FormFactory dote tous nos formulaires de la protection CSRF, alors on pourra aussi charger
 * l'extension CsrfExtension en lui passant notre CsrfTokenManager !
 * 
 * IMPORTANT :
 * ----------
 * A partir de ce moment là, tous vos formulaires seront soumis à la protection CSRF, ce qui ne vous empêche pas de la
 * désactiver au cas par cas si nécessaire, en passant l'option ['csrf_protection' => false] lors de la création d'un
 * FormBuilder via la FormFactory
 * 
 * A SAVOIR AUSSI :
 * ------------
 * Afin que chaque formulaire sache bien comment gérer cette protection, il faudra donner au FormBuilder un certain nombre
 * d'informations essentielles :
 * 1) L'identifiant du token que l'on souhaite utiliser (cela peut-être n'importe quoi)
 * 2) Le nom du champ du formulaire qui contiendra le token et qui sera validé lors de la soumission
 * 3) Le message d'erreur à afficher dans le cas où le token n'est pas fourni ou qu'il ne correspond pas
 */
$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new ValidatorExtension($validator))
    ->addExtension(new CsrfExtension($csrfManager))
    ->getFormFactory();
