<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

require_once __DIR__ . '/vendor/autoload.php';


// On se sert de l'autoloader qui expliquera à Doctrine où sont les classes correspondantes aux annotations qu'il 
// va rencontrer :
$loader = require  __DIR__ . '/vendor/autoload.php';

// On offre ce loader à Doctrine via son registre d'annotations :
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$validator = Validation::createValidatorBuilder()
    ->addYamlMapping('validation.yml')
    ->enableAnnotationMapping()
    ->getValidator();

// Mise en place de la protection CSRF
session_start();

$csrfGenrator = new UriSafeTokenGenerator();
$csrfStorage = new NativeSessionTokenStorage();
$csrfManager = new CsrfTokenManager($csrfGenrator, $csrfStorage);

/**
 * CREATION DU MOTEUR TWIG ET CONFIGURATION DE L'EXTENSION POUR LES FORMULAIRES
 * --------------
 * Cette partie est assez complexe mais elle consiste en gros à créer le moteur Twig en lui expliquant où se trouvent
 * les fichiers Twig qu'on va vouloir afficher (nos vues, mais aussi les thèmes de formulaires du bridge).
 * 
 * Ensuite, il s'agira d'enrichir Twig avec les fonctions offertes par le symfony/twig-bridge
 * 
 * ATTENTION :
 * -----------
 * L'utilisation du theme par défaut (form_div_layout.html.twig) nécessite aussi d'installer le composant 
 * symfony/translation (composer require symfony/translation) car il utilise le filtre "trans"
 */

// 1) Localiser les templates de l'application elle-même :
$viewsDirectory = __DIR__ . '/views';

// 2) Localiser le template de formulaire qu'on veut utiliser en intérogeant la classe AppVariable
$reflexion = new ReflectionClass(AppVariable::class);
$bridgeDirectory = dirname($reflexion->getFileName());
$templatesDirectory = $bridgeDirectory . '/Resources/views/Form';

// 3) Création du moteur Twig en spécifiant les dossiers de templates 
$twig = new Environment(new FilesystemLoader([
    $viewsDirectory, // Le dossier où se trouvent nos fichiers twig, y compris le custom theme
    $templatesDirectory
]));

// 4) Définir le thème de formulaire par défaut (il en existe plusieurs, dont certains pour bootstrap 3 et 4 ou encore Foundation)
$formTheme = 'form_div_layout.html.twig';
$customFormTheme = 'custom_theme.html.twig';

// 5) On peut désormais désormais enrichir Twig avec les fonctions fournies par le bridge (donc spécialement pour le
// composant symfony/form) en n'oubliant pas d'inclure le gestionnaire de la protection CSRF afin que Twig puisse
// gérer le rendu du token dans le formulaire :
$formEngine = new TwigRendererEngine([$formTheme, $customFormTheme], $twig);
$twig->addRuntimeLoader(new FactoryRuntimeLoader([
    FormRenderer::class => function () use ($formEngine, $csrfManager) {
        return new FormRenderer($formEngine, $csrfManager);
    }
]));

$twig->addExtension(new FormExtension());

// 6) Attention, pour utiliser la plupart des thèmes de formulaires du Twig Bridge, on a besoin du composant Translation
// (composer require symfony/translation)
$translator = new Translator('fr_FR');
$twig->addExtension(new TranslationExtension($translator));

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
