<?php
/**
 * Advertisement service Users controller.
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Model\UsersModel;
use Form\UserForm;

/**
 * Class UsersController
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
 * @uses Model\UsersModel
 * @uses Form\UsersForm
 */
class UsersController implements ControllerProviderInterface
{
    /**
     *
     * UsersModel object.
     *
     * @var $_model
     * $access protected
     */
    protected $_model;

    /**
     * UsersModel object.
     *
     * @var $_user
     * @access protected
     */
    protected $_user;

    /**
     * Data for view.
     *
     * @access protected
     * @var array $_view
     */
    protected $_view = array();

    /**
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->_model = new UsersModel($app);
        $usersController = $app['controllers_factory'];
        $usersController->match('/register', array($this, 'registerAction'));
        $usersController->match('/register/', array($this, 'registerAction'))
            ->bind('users_register');
        $usersController->match('/data', array($this, 'dataAction'));
        $usersController->match('/data/', array($this, 'dataAction'))
            ->bind('users_data');
        $usersController->match('/newdata', array($this, 'newdataAction'));
        $usersController->match('/newdata/', array($this, 'newdataAction'))
            ->bind('users_newdata');
        $usersController->match('/edit', array($this, 'editAction'));
        $usersController->match('/edit/', array($this, 'editAction'))
            ->bind('users_edit');
        $usersController->match('/delete', array($this, 'deleteAction'));
        $usersController->match('/delete/', array($this, 'deleteAction'))
            ->bind('users_delete');
        $usersController->match('/password', array($this, 'passwordAction'));
        $usersController->match('/password/', array($this, 'passwordAction'))
            ->bind('users_password');
        $usersController->get('/show', array($this, 'showAction'));
        $usersController->get('/show/', array($this, 'showAction'))
            ->bind('users_show');
        $usersController->get('/more/{id}', array($this, 'moreAction'));
        $usersController->get('/more/{id}/', array($this, 'moreAction'))
            ->bind('users_more');
        $usersController->get('/view', array($this, 'viewAction'));
        $usersController->get('/view/', array($this, 'viewAction'))
            ->bind('users_view');
        $usersController->get('/index/{id}', array($this, 'indexAction'));
        $usersController->get('/index/{id}/', array($this, 'indexAction'))
            ->bind('users_index');
        return $usersController;
    }

    /**
     * Index user's profile
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function indexAction(Application $app, Request $request)
    {
        try {
            $id = (int)$request->get('id', 0);
            $usersModel = new UsersModel($app);
            $this->_view['user'] = $usersModel->checkUser($id);
            if (!($this->_view['user'])) {
                throw new NotFoundHttpException("User not found");
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Users Exeption'));
        }  return $app['twig']->render('users/index.twig', $this->_view);
    }



    /**
     * View user's profile
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function viewAction(Application $app, Request $request)
    {
        try {
            $id = $this->_model->getIdCurrentUser($app);
            $user = $this->_model->checkUser($id);
            if (count($user)) {
                return $app['twig']->render(
                    'users/view.twig',
                    array(
                        'user' => $user
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('User data not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Users Exeption'));
        } return $app->redirect(
            $app['url_generator']->generate(
                'users_data'
            ),
            301
        );
    }

    /**
     * More action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function moreAction(Application $app, Request $request)
    {
        try {
            $id = (int)$request->get('id', 0);
            $usersModel = new UsersModel($app);
            $user = $this->_model->checkUser($id);
            if ($user) {
                $this->_view = $usersModel->getAdsListByIduser($id);
                return $app['twig']
                    ->render('users/show.twig', array('ads' => $this->_view));
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('User not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Users Exeption'));
        } return $app->redirect(
            $app['url_generator']->
            generate(
                'users_view'
            ),
            301
        );

    }

    /**
     * Show action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function showAction(Application $app, Request $request)
    {
        try {
            $usersModel = new UsersModel($app);
            $id = $this->_model->getIdCurrentUser($app);
            $user = $this->_model->checkUser($id);
            if ($user) {
                $this->_view = $usersModel->getAdsListByIduser($id);
                return $app['twig']
                    ->render('users/show.twig', array('ads' => $this->_view));
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('User data not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Users Exeption'));
        }  return $app->redirect(
            $app['url_generator']->
            generate(
                'users_view'
            ),
            301
        );
    }

    /**
     * Register new user
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function registerAction(Application $app, Request $request)
    {
        $data = array();
        $form = $app['form.factory']
            ->createBuilder(new UserForm($app), $data)->getForm();
        $form->remove('new_password');
        $form->remove('confirm_new_password');
        $form->remove('firstname');
        $form->remove('surname');
        $form->remove('street');
        $form->remove('city_name');
        $form->remove('idrole');
        $form->remove('idcity');
        $form->remove('idprovince');
        $form->remove('idcountry');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $check = $this->_model
                ->getUserByLogin($data['login']);
            if (!$check) {
                if ($data['password'] === $data['confirm_password']) {
                    $data['password']
                        = $app['security.encoder.digest']
                        ->encodePassword("{$data['password']}", '');
                    try {
                        $usersModel = new UsersModel($app);
                        $usersModel->register($data);
                        $app['session']->getFlashBag()->add(
                            'message',
                            array(
                                'type' => 'success',
                                'content' => $app['translator']
                                    ->trans('Account has been created')
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate(
                                'users_data'
                            ),
                            301
                        );
                    } catch (\Exception $e) {
                        $app->abort(404, $app['translator']->trans('Someting went wrong'));
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('Passwords are the same')
                        )
                    );
                    return $app['twig']->render(
                        'users/register.twig',
                        array(
                            'form' => $form->createView()
                        )
                    );
                }
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('User with this login already exists')
                    )
                );
                return $app['twig']->render(
                    'users/register.twig',
                    array(
                        'form' => $form->createView()
                    )
                );
            }
        }
        return $app['twig']->render(
            'users/register.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Register new user data
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function dataAction(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->checkUser($id);
        if (count($user)) {
            $data = array(
                'iduser' => $id,
                'firstname' => $user['firstname'],
                'surname' => $user['surname'],
                'street' => $user['street']
            );
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->remove('idrole');
            $form->remove('password');
            $form->remove('confirm_password');
            $form->remove('login');
            $form->remove('email');
            $form->remove('city_name');
            $form->remove('new_password');
            $form->remove('confirm_new_password');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $usersModel = new UsersModel($app);
                    $usersModel->registerData($data);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'success',
                            'content' => $app['translator']
                                ->trans('Data saved')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            '/ads/'
                        ),
                        301
                    );
                } catch (\Exception $e) {
                    $app->abort(404, $app['translator']->trans('Someting went wrong'));
                }
            }
        }
        return $app['twig']->render(
            'users/registerdata.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Register new user data
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function newdataAction(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->checkUser($id);
        if (count($user)) {
            $data = array(
                'iduser' => $id,
                'city_name' => $user['city_name'],
            );
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->remove('idrole');
            $form->remove('firstname');
            $form->remove('surname');
            $form->remove('street');
            $form->remove('password');
            $form->remove('confirm_password');
            $form->remove('login');
            $form->remove('email');
            $form->remove('new_password');
            $form->remove('confirm_new_password');
            $form->remove('idcity');
            $form->remove('idprovince');
            $form->remove('idcountry');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $usersModel = new UsersModel($app);
                    $usersModel->registerNewData($data);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'success',
                            'content' => $app['translator']
                                ->trans('Data saved')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            'users_data'
                        ),
                        301
                    );
                } catch (\Exception $e) {
                    $app->abort(404, $app['translator']->trans('Someting went wrong'));
                }
            }
        }
        return $app['twig']->render(
            'users/registernewdata.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Edit information about user
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     *
     */
    public function editAction(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->checkUser($id);
        if (count($user)) {
            $data = array(
                'iduser' => $id,
                'login' => $user['login'],
                'firstname' => $user['firstname'],
                'surname' => $user['surname'],
                'email' => $user['email'],
                'street' => $user['street'],
                'idcity' => $user['idcity'],
                'idprovince' => $user['idprovince'],
                'idcountry' => $user['idcountry']
            );
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->remove('idrole');
            $form->remove('password');
            $form->remove('confirm_password');
            $form->remove('new_password');
            $form->remove('confirm_new_password');
            $form->remove('city_name');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $usersModel = new UsersModel($app);
                    $usersModel->editUser($data);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'success',
                            'content' => $app['translator']
                                ->trans('Information changed')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->
                        generate(
                            'users_view'
                        ),
                        301
                    );
                } catch (\Exception $e) {
                    $app->abort(404, $app['translator']->trans('Someting went wrong'));
                }
            }
        }
        return $app['twig']->render(
            'users/edit.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * Delete user's account
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function deleteAction(Application $app, Request $request)
    {
        try {
            $id = $this->_model->getIdCurrentUser($app);
            $user = $this->_model->getUserById($id);
            $this->_view['user'] = $user;
            if (count($user)) {
                $form = $app['form.factory']
                    ->createBuilder(new UserForm($app), $user)->getForm();
                $form->remove('login');
                $form->remove('firstname');
                $form->remove('surname');
                $form->remove('email');
                $form->remove('password');
                $form->remove('confirm_password');
                $form->remove('new_password');
                $form->remove('confirm_new_password');
                $form->remove('street');
                $form->remove('idrole');
                $form->remove('city_name');
                $form->remove('idcity');
                $form->remove('idprovince');
                $form->remove('idcountry');
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $usersModel = new  UsersModel($app);
                    $usersModel->deleteUser($data['iduser']);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('User deleted')
                        )
                    );
                    return $app->
                    redirect(
                        $app['url_generator']->
                        generate(
                            'users_view'
                        ),
                        301
                    );
                }
                $this->_view['form'] = $form->createView();
            } else {
                return $app->redirect(
                    $app['url_generator']->
                    generate(
                        'users_view'
                    ),
                    301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Users Exeption'));
        } return $app['twig']->render('users/delete.twig', $this->_view);
    }

    /**
     * Change passoword
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function passwordAction(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->getUserById($id);
        if (count($user)) {
            $data = array();
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->remove('login');
            $form->remove('firstname');
            $form->remove('surname');
            $form->remove('email');
            $form->remove('idrole');
            $form->remove('street');
            $form->remove('city_name');
            $form->remove('idcity');
            $form->remove('idprovince');
            $form->remove('idcountry');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $oldPassword = $app['security.encoder.digest']
                    ->encodePassword("{$data['password']}", '');
                if ($oldPassword === $user['password']) {
                    if ($data['new_password']===$data['confirm_new_password']
                        && $data['password'] === $data['confirm_password']
                    ) {
                        $data['new_password'] = $app['security.encoder.digest']
                            ->encodePassword("{$data['new_password']}", '');
                        try {
                            $model = $this->_model->changePassword($data, $id);
                            $app['session']->getFlashBag()->add(
                                'message',
                                array(
                                    'type' => 'success',
                                    'content' => $app['translator']
                                        ->trans('Password changed')
                                )
                            );
                            return $app->
                            redirect(
                                $app['url_generator']->
                                generate(
                                    'auth_login'
                                ),
                                301
                            );
                        } catch (\Exception $e) {
                            $app->abort(404, $app['translator']->trans('Something went wrong'));
                        }
                    } else {
                        $app['session']->getFlashBag()->add(
                            'message',
                            array(
                                'type' => 'warning',
                                'content' => $app['translator']
                                    ->trans('Passwords are different')
                            )
                        );
                        return $app['twig']->render(
                            'users/edit.twig',
                            array(
                                'form' => $form->createView()
                            )
                        );
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('Password is wrong')
                        )
                    );
                }
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message',
                array(
                    'type' => 'danger',
                    'content' => $app['translator']
                        ->trans('User not found')
                )
            );
            return $app->
            redirect(
                $app['url_generator']->
                generate(
                    'auth_login'
                ),
                301
            );
        }
        return $app['twig']->render(
            'users/password.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
