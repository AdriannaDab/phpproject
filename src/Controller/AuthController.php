<?php
/**
 * Auth controller.
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Controller
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 **/

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Form\LoginForm;

/**
 * Class AuthController.
 *
 * @category Controller
 * @package  Controller
 * @author   Adrianna Dabkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 * @uses Silex\ControllerProviderInterface
 * @uses Symfony\Component\HttpFoundation\Request
 * @uses Symfony\Component\Validator\Constraints
 * @uses Form\LoginForm;
 */
class AuthController implements ControllerProviderInterface
{
    /**
     * Data for view.
     *
     * @access protected
     * @var array $view
     */
    protected $view = array();

    /**
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @return AlbumsController Result
     */
    public function connect(Application $app)
    {
        $authController = $app['controllers_factory'];
        $authController->match('login', array($this, 'loginAction'))
            ->bind('auth_login');
        $authController->get('logout', array($this, 'logoutAction'))
            ->bind('auth_logout');
        return $authController;
    }

    /**
     * Login action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function loginAction(Application $app, Request $request)
    {
        $user = array(
            'login' => $app['session']->get('_security.last_username')
        );
        $form = $app['form.factory']->createBuilder(new LoginForm(), $user)
            ->getForm();
        $this->view = array(
            'form' => $form->createView(),
            'error' => $app['security.last_error']($request)
        );
        return $app['twig']->render('auth/login.twig', $this->view);
    }

    /**
     * Logout action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function logoutAction(Application $app, Request $request)
    {
        $app['session']->clear();
        return $app['twig']->render('auth/logout.twig', $this->view);
    }
}