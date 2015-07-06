<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', E_ALL);
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$app = new Silex\Application();
$app['debug']=true;

// Translator
use Symfony\Component\Translation\Loader\YamlFileLoader;
$app->register(
    new Silex\Provider\TranslationServiceProvider(), array(
        'locale' => 'pl',
        'locale_fallbacks' => array('pl'),
    )
);
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', dirname(dirname(__FILE__)) . '/config/locales/pl.yml', 'pl');
    return $translator;
}));

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => dirname(dirname(__FILE__)) . '/src/views',
));

// Form
$app->register(new Silex\Provider\FormServiceProvider());

// Validator
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

// Session
$app->register(new Silex\Provider\SessionServiceProvider());

// Doctride
$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => '13_dabkowska',
            'user'      => '13_dabkowska',
            'password'  => '8691Prodigy!',
            'charset'   => 'utf8',
        ),
    )
);


//Url Generator
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


//Security - rozszerze potem
$app->register(
    new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'unsecured' => array(
                'anonymous' => true,
            ),
        ),
    )
);


$app->get('/', function () use ($app) {
    return $app->redirect($app["url_generator"]->generate("/ads/"));
})->bind('/');

date_default_timezone_set('Europe/Warsaw');

$app->mount('/ads/', new Controller\AdsController());
$app->mount('/categories/', new Controller\CategoriesController());
$app->mount('/comments/', new Controller\CommentsController());
$app->mount('/photos/', new Controller\PhotosController());

$app->run();
