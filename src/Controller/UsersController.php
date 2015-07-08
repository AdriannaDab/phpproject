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
        $usersController->match('/register/', array($this, 'registerAction'))
            ->bind('users_register');
        $usersController->match('/edit/', array($this, 'editAction'))
            ->bind('users_edit');
        $usersController->match('/delete/', array($this, 'deleteAction'))
            ->bind('users_delete');
        $usersController->match('/password/', array($this, 'passwordAction'))
            ->bind('users_password');
        $usersController->get('/view/', array($this, 'viewAction'))
            ->bind('users_view');
        return $usersController;
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
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->getUser($id);
        if (count($user)) {
            return $app['twig']->render(
                'users/view.twig', array(
                    'user' => $user
                )
            );
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono użytkownika'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/'
                ), 301
            );
        }
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
        $data = array(
            'signupdate' => date('Y-m-d'),
        );
        $form = $app['form.factory']
            ->createBuilder(new UserForm($app), $data)->getForm();
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
                        $model = $this->_model->register($data);
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'success',
                                'content' => 'Konto zostało stworzone'
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate(
                                '/auth/login'
                            ), 301
                        );
                    } catch (\Exception $e) {
                        $errors[] = 'Coś poszło niezgodnie z planem';
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'warning',
                            'content' => 'Hasła nie są takie same'
                        )
                    );
                    return $app['twig']->render(
                        'users/register.twig', array(
                            'form' => $form->createView()
                        )
                    );
                }
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => 'Użytkownik o tym loginie już istnieje'
                    )
                );
                return $app['twig']->render(
                    'users/register.twig', array(
                        'form' => $form->createView()
                    )
                );
            }
        }
        return $app['twig']->render(
            'users/register.twig', array(
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
    public function edit(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->getUserById($id);
        if (count($user)) {
            $data = array(
                'iduser' => $id,
                'email' => $user['email'],
                'homesite' => $user['homesite']
            );
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $password = $app['security.encoder.digest']
                    ->encodePassword("{$data['password']}", '');
                if ($password == $user['password']) {
                    try {
                        $model = $this->_model->editUser($data);
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'success',
                                'content' => 'Informacje zostały zmienione'
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate(
                                '/users/view'
                            ), 301
                        );
                    } catch (\Exception $e) {
                        $errors[] = 'Coś poszło niezgodnie z planem';
                    }
                }
            }
            return $app['twig']->render(
                'users/edit.twig', array(
                    'form' => $form->createView()
                )
            );
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono użytkownika'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/users/view'
                ), 301
            );
        }
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
    public function delete(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->getUserById($id);
        $data = array();
        if (count($user)) {
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($app['security']->isGranted('ROLE_ADMIN')) {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'danger',
                            'content' => 'Nie można usunąć konta admina'
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            '/'
                        ), 301
                    );
                } else {
                    try {
                        $model = $this->_model->deleteUser($id);
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'success',
                                'content' => 'Konto zostało usunięte'
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate(
                                '/'
                            ), 301
                        );
                    } catch (\Exception $e) {
                        $errors[] = 'Coś poszło niezgodnie z planem';
                    }
                    return $app->redirect(
                        $app['url_generator']->generate(
                            '/users/edit'
                        ), 301
                    );
                }
                return $app['twig']->render(
                    'users/delete.twig', array(
                        'form' => $form->createView()
                    )
                );
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono użytkownika'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/'
                ), 301
            );
        }
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
    public function password(Application $app, Request $request)
    {
        $id = $this->_model->getIdCurrentUser($app);
        $user = $this->_model->getUserById($id);
        if (count($user)) {
            $data = array();
            $form = $app['form.factory']
                ->createBuilder(new UserForm($app), $data)->getForm();
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
                                'message', array(
                                    'type' => 'success',
                                    'content' => 'Hasło zostałoz mienione'
                                )
                            );
                            return $app->redirect(
                                $app['url_generator']->generate(
                                    '/auth/login'
                                ), 301
                            );
                        } catch (\Exception $e) {
                            $errors[] = 'Coś poszło niezgodnie z planem';
                        }
                    } else {
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'warning',
                                'content' => 'Hasła są różne'
                            )
                        );
                        return $app['twig']->render(
                            'users/edit.twig', array(
                                'form' => $form->createView()
                            )
                        );
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'danger',
                            'content' => 'Hasło nie jest poprawne'
                        )
                    );
                }
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono użytkownika'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/auth/login'
                ), 301
            );
        }
        return $app['twig']->render(
            'users/edit.twig', array(
                'form' => $form->createView()
            )
        );
    }


}