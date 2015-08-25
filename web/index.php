<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', E_ALL);
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = new Silex\Application();
$app['debug']=true;

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => dirname(dirname(__FILE__)) . '/src/views',

));

// Translator
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

// Form
$app->register(new Silex\Provider\FormServiceProvider());

// Validator
$app->register(new Silex\Provider\ValidatorServiceProvider());


// Session
$app->register(new Silex\Provider\SessionServiceProvider());

// Doctrine
$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'baza!',
            'user'      => 'nazwa!',
            'password'  => 'haslo!',
            'charset'   => 'utf8',
            'driverOptions' => array(
                1002=>'SET NAMES utf8'
            ),

        ),
    )
);


//Url Generator
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

//Security
$app->register(
    new Silex\Provider\SecurityServiceProvider(),
    array(
        'security.firewalls' => array(
            'admin' => array(
                'pattern' => '^.*$',
                'form' => array(
                    'login_path' => 'auth_login',
                    'check_path' => 'auth_login_check',
                    'default_target_path'=> '/ads',
                    'username_parameter' => 'loginForm[login]',
                    'password_parameter' => 'loginForm[password]',
                ),
                'anonymous' => true,
                'logout' => array(
                    'logout_path' => 'auth_logout',
                    'default_target_path' => '/ads'
                ),
                'users' => $app->share(
                    function() use ($app)
                    {
                        return new Provider\UserProvider($app);
                    }
                ),
            ),
        ),
        'security.access_rules' => array(

            array('^/auth/.+$|^/users/register/.*$|^/ads/?[1-9"\']*?$|^/ads/view/[1-9"\']*$|^/categories/?$|^/categories/view/.*$|
                    ^/users/add|^/comments/view/.*$|^/about/?$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('/ads.*$|^/comments.*$|^/categories.*$|^/users.*$|^/photos.*$', 'ROLE_USER'),
            array('/ads.*$|^/comments.*$|^/categories.*$|^/users.*$|^/photos.*$', 'ROLE_MODERATOR'),
            array('^/.+$', 'ROLE_ADMIN')
        ),
        'security.role_hierarchy' => array(
            'ROLE_MODERATOR' => array('ROLE_USER'),
            'ROLE_ADMIN' => array('ROLE_USER'),
        ),
    )
);

$app->error(
    function (
        \Exception $e, $code
    ) use ($app) {

        if ($e instanceof Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $code = (string)$e->getStatusCode();
        }

       if ($app['debug']) {
          return;
       }

        // 404.html, or 40x.html, or 4xx.html, or error.html
        $templates = array(
            'errors/'.$code.'.twig',
            'errors/'.substr($code, 0, 2).'x.twig',
            'errors/'.substr($code, 0, 1).'xx.twig',
            'errors/default.twig',
        );

        return new Response(
            $app['twig']->resolveTemplate($templates)->render(
                array('code' => $code)
            ),
            $code
        );

    }
);

$app->get('/', function () use ($app) {
    return $app->redirect($app["url_generator"]->generate("/ads/"));
})->bind('/');

date_default_timezone_set('Europe/Warsaw');

$app->mount('/ads/', new Controller\AdsController());
$app->mount('/categories/', new Controller\CategoriesController());
$app->mount('/comments/', new Controller\CommentsController());
$app->mount('/photos/', new Controller\PhotosController());
$app->mount('/users/', new Controller\UsersController());
$app->mount('/admin/', new Controller\AdminsController());
$app->mount('/about', new Controller\AboutsController());
$app->mount('auth', new Controller\AuthController());

$app->run();
