<?php
/**
 * Advertisement service Admins controller.
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
use Model\AdminsModel;
use Form\UserForm;

/**
 * Class AdminsController
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
class AdminsController implements ControllerProviderInterface
{
    /**
     *
     * AdminsModel object.
     *
     * @var $_model
     * $access protected
     */
    protected $_model;

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
        $this->_model = new AdminsModel($app);
        $adminsController = $app['controllers_factory'];
        $adminsController->match('/delete', array($this, 'deleteAction'));
        $adminsController->match('/delete/', array($this, 'deleteAction'))
            ->bind('admins_delete');
        $adminsController->match('/role', array($this, 'roleAction'));
        $adminsController->match('/role/', array($this, 'roleAction'))
            ->bind('admins_role');
        $adminsController->get('/view/{id}/', array($this, 'viewAction'));
        $adminsController->get('/view/{id}', array($this, 'viewAction'))
            ->bind('admins_view');
        $adminsController->get('/admins', array($this, 'indexAction'));
        $adminsController->get('/admins/', array($this, 'indexAction'));
        $adminsController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)
            ->bind('admins_index');
        return $adminsController;
    }

    /**
     * Index action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function indexAction(Application $app, Request $request)
    {
        $pageLimit = 10;
        $page = (int) $request->get('page', 1);
        try {
            $adminsModel = new AdminsModel($app);
            $this->_view = array_merge(
                $this->_view, $adminsModel->getPaginatedUsers($page, $pageLimit)

            );
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Admin Exeption'));
        }
        return $app['twig']->render('admin/index.twig', $this->_view);
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
            $id = (int)$request->get('id', 0);
            $adminsModel = new AdminsModel($app);
            $admin = $this->_view['admin'] = $adminsModel->getUser($id);
            if (count($admin)) {
                return $app['twig']->render(
                    'admin/view.twig', array(
                        'admin' => $admin
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => 'User data not found'
                    )
                );
            }
        } catch (AdminException $e) {
                echo $app['translator']->trans('Caught Admin Exception ') .  $e->getMessage() . "\n";
        } return $app->redirect(
            $app['url_generator']->generate(
                'admins_index'
            ), 301
        );

    }


    /**
     * Role action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function roleAction(Application $app, Request $request)
    {
        try {
            $adminsModel = new AdminsModel($app);
            $id = (int) $request->get('id', 0);
            $admin = $adminsModel->getUserId($id);
            if (count($admin)) {
                $data = array(
                    'iduser' => $id,
                    'idrole' => $admin['idrole'],
                );
                $form = $app['form.factory']
                    ->createBuilder(new UserForm($app), $data)->getForm();
                $form->remove('login');
                $form->remove('firstname');
                $form->remove('surname');
                $form->remove('email');
                $form->remove('password');
                $form->remove('confirm_password');
                $form->remove('new_password');
                $form->remove('confirm_new_password');
                $form->remove('street');
                $form->remove('city_name');
                $form->remove('idcity');
                $form->remove('idprovince');
                $form->remove('idcountry');
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $adminsModel = new AdminsModel($app);
                    $adminsModel->changeRole($data);
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'success',
                            'content' => $app['translator']
                                ->trans('Role changed')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate('admins_index', array('id' => $admin['iduser'])), 301
                    );
                }
            } else {
                return $app->redirect(
                    $app['url_generator']->generate('admins_index'), 301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Admin Exeption'));
        } return $app['twig']->render('admin/role.twig', array(
            'form' => $form->createView()));
    }


    /**
     * Delete action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function deleteAction(Application $app, Request $request)
    {
        try{
            $adminsModel = new AdminsModel($app);
            $id = (int) $request->get('id', 0);
            $this->_view['admin']= $adminsModel->getUserId($id);
            if ($this->_view['admin']) {
                $this->_view['admin']= $adminsModel->getAdsListByIduser($id);
                if (!$this->_view['admin']) {
                    $this->_view['admin']= $adminsModel->getDeleteUser($id);
                    $admin = $adminsModel->getDeleteUser($id);
                    $this->_view['admin'] = $admin;
                    if (count($admin)) {
                        $form = $app['form.factory']
                            ->createBuilder(new UserForm($app), $admin)->getForm();
                        $form->remove('login');
                        $form->remove('firstname');
                        $form->remove('surname');
                        $form->remove('email');
                        $form->remove('password');
                        $form->remove('confirm_password');
                        $form->remove('new_password');
                        $form->remove('confirm_new_password');
                        $form->remove('street');
                        $form->remove('city_name');
                        $form->remove('idrole');
                        $form->remove('idcity');
                        $form->remove('idprovince');
                        $form->remove('idcountry');
                        $form->handleRequest($request);
                        if ($form->isValid()) {
                            $data = $form->getData();
                            $adminsModel = new AdminsModel($app);
                            $adminsModel->deleteUser($data['iduser']);
                            $app['session']->getFlashBag()->add(
                                'message', array(
                                    'type' => 'danger',
                                    'content' => $app['translator']
                                        ->trans('User deleted')
                                )
                            );
                            return $app->redirect(
                                $app['url_generator']->generate('admins_index'), 301
                            );
                        }
                        $this->_view['form'] = $form->createView();
                    } else {
                        return $app->redirect(
                            $app['url_generator']->generate('admins_index'), 301
                        );
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('Can not delete user with ads')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            'admins_index'
                        ), 301
                    );
                }
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('Did not found user')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'admins_index'
                    ), 301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Admin Exeption'));
        } return $app['twig']->render('admin/delete.twig', $this->_view);
    }
}