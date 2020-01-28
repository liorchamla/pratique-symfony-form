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

require_once __DIR__ . '/../vendor/autoload.php';


// On se sert de l'autoloader qui expliquera à Doctrine où sont les classes correspondantes aux annotations qu'il 
// va rencontrer :
$loader = require  __DIR__ . '/../vendor/autoload.php';

// On offre ce loader à Doctrine via son registre d'annotations :
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$validator = Validation::createValidatorBuilder()
    ->addYamlMapping(__DIR__ . '/validation.yml')
    ->enableAnnotationMapping()
    ->getValidator();

// Mise en place de la protection CSRF
session_start();
$csrfGenrator = new UriSafeTokenGenerator();
$csrfStorage = new NativeSessionTokenStorage();
$csrfManager = new CsrfTokenManager($csrfGenrator, $csrfStorage);


// Mise en place de Twig
$viewsDirectory = __DIR__ . '/../views';

$reflexion = new ReflectionClass(AppVariable::class);
$bridgeDirectory = dirname($reflexion->getFileName());
$templatesDirectory = $bridgeDirectory . '/Resources/views/Form';

$twig = new Environment(new FilesystemLoader([
    $viewsDirectory,
    $templatesDirectory
]));

$formTheme = 'form_div_layout.html.twig';
$customFormTheme = 'custom_theme.html.twig';

$formEngine = new TwigRendererEngine([$formTheme, $customFormTheme], $twig);
$twig->addRuntimeLoader(new FactoryRuntimeLoader([
    FormRenderer::class => function () use ($formEngine, $csrfManager) {
        return new FormRenderer($formEngine, $csrfManager);
    }
]));

$twig->addExtension(new FormExtension());

$translator = new Translator('fr_FR');
$twig->addExtension(new TranslationExtension($translator));

// Mise en place de la FormFactory
$formFactory = Forms::createFormFactoryBuilder()
    ->addExtension(new ValidatorExtension($validator))
    ->addExtension(new CsrfExtension($csrfManager))
    ->getFormFactory();
