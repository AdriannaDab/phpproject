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
        $adminsController->get('/admin', array($this, 'indexAction'));
        $adminsController->get('/admin/', array($this, 'indexAction'));
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
        $pageLimit = 20;
        $page = (int) $request->get('page', 1);
        try {
            $adminsModel = new AdminsModel($app);
            $this->_view = array_merge(
                $this->_view, $adminsModel->getPaginatedUsers($page, $pageLimit)

            );
        } catch (\PDOException $e) {
            $app['session']->getFlashBag()->add(
                'message',
                array(
                    'type' => 'error',
                    'content' => $app['translator']
                        ->trans('Error code: '.$e->getCode())
                )
            );
        }
        return $app['twig']->render('admin/index.twig', $this->_view);
    }
}