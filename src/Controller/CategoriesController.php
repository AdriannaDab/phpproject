<?php
/**
 * Categories controller.
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
use Model\CategoriesModel;
use Form\CategoryForm;
use Model\UsersModel;

/**
 * Class CategoriesController.
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
 * @uses Model\CategoriesModel
 * @uses Form\CategoryForm;
 * @uses Model\UsersModel
 */
class CategoriesController implements ControllerProviderInterface
{

   /**
    * Data for view.
    *
    * @access protected
    * @var array $_view
    */
    protected $_view = array();

    /**
     * UsersModel object.
     *
     * @var $_user
     * @access protected
     */
    protected $_user;


   /**
    * Routing settings.
    *
    * @access public
    * @param Silex\Application $app Silex application
    * @return CategoriesController Result
    */
    public function connect(Application $app)
    {
        $this->_user = new UsersModel($app);
        $categoriesController = $app['controllers_factory'];
        $categoriesController->match('/add', array($this, 'addAction'))
            ->bind('categories_add');
        $categoriesController->match('/add/', array($this, 'addAction'));
        $categoriesController->match('/edit/{id}', array($this, 'editAction'))
            ->bind('categories_edit');
        $categoriesController->match('/edit/{id}/', array($this, 'editAction'));
        $categoriesController->match('/delete/{id}', array($this, 'deleteAction'))
            ->bind('categories_delete');
        $categoriesController->match('/delete/{id}/', array($this, 'deleteAction'));
        $categoriesController->get('/view/{id}', array($this, 'viewAction'))
            ->bind('categories_view');
        $categoriesController->get('/view/{id}/', array($this, 'viewAction'));
        $categoriesController->get('/index', array($this, 'indexAction'));
        $categoriesController->get('/index/', array($this, 'indexAction'));
        $categoriesController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)
            ->bind('categories_index');
        return  $categoriesController;
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
            $categoriesModel = new CategoriesModel($app);
            $this->_view = array_merge(
                $this->_view,
                $categoriesModel->getPaginatedCategories($page, $pageLimit)
            );
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Categories Exeption'));
        }
        return $app['twig']->render('categories/index.twig', $this->_view);
    }

    /**
     * View action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function viewAction(Application $app, Request $request)
    {
        try {
            $id = (int)$request->get('id', 0);
            $categoriesModel = new CategoriesModel($app);
            $this->_view['category'] = $categoriesModel->getCategory($id);
            $this->_view['category'] = $categoriesModel->checkCategoryId($id);
            if ($this->_view['category']) {
                $this->_view['category'] = $categoriesModel->getAdsListByIdcategory($id);
                return $app['twig']
                    ->render('categories/view.twig', array(
                        'ads' => $this->_view['category']));
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('Category not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Categories Exeption'));
        } return $app->
            redirect(
                $app['url_generator']->
                generate(
                    'categories_index'
                ),
                301
            );
    }

    /**
     * Add action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function addAction(Application $app, Request $request)
    {
        try {
            $data = array();
            $form = $app['form.factory']
                ->createBuilder(new CategoryForm($app), $data)->getForm();
            $form->remove('iduser');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $categoriesModel = new CategoriesModel($app);
                $categoriesModel->add($data);
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'success',
                        'content' => $app['translator']
                            ->trans('New category added')
                    )
                );
                return $app->
                redirect(
                    $app['url_generator']->
                    generate(
                        'categories_index'
                    ),
                    301
                );
            }
            $this->_view['form'] = $form->createView();
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Categories Exeption'));
        } return $app['twig']->render('categories/add.twig', $this->_view);
    }

    /**
     * Edit action.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     */
    public function editAction(Application $app, Request $request)
    {
        try {
            $categoriesModel = new CategoriesModel($app);
            $id = (int) $request->get('id', 0);
            $category = $categoriesModel->getCategory($id);
            if (count($category)) {
                $form = $app['form.factory']
                    ->createBuilder(new CategoryForm($app), $category)->getForm();
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $categoriesModel = new CategoriesModel($app);
                    $categoriesModel->edit($data);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'success',
                            'content' => $app['translator']
                                ->trans('Category edited')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->
                        generate(
                            'categories_index'
                        ),
                        301
                    );
                }
                $this->_view['form'] = $form->createView();
                $this->_view['id'] = $id;
            } else {
                return $app->redirect(
                    $app['url_generator']->
                    generate(
                        'categories_add'
                    ),
                    301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Categories Exeption'));
        } return $app['twig']->render('categories/edit.twig', $this->_view);
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
        try {
            $categoriesModel = new CategoriesModel($app);
            $id = (int) $request->get('id', 0);
            $this->_view['category']= $categoriesModel->checkCategoryId($id);
            if ($this->_view['category']) {
                $this->_view['category']= $categoriesModel->getAdsListByIdcategory($id);
                if (!$this->_view['category']) {
                    $this->_view['category']= $categoriesModel->getCategory($id);
                    $category = $categoriesModel->getCategory($id);
                    $this->_view['category'] = $category;
                    if (count($category)) {
                        $form = $app['form.factory']
                            ->createBuilder(new CategoryForm($app), $category)->getForm();
                        $form->remove('category_name');
                        $form->remove('iduser');
                        $form->handleRequest($request);
                        if ($form->isValid()) {
                            $data = $form->getData();
                            $categoriesModel = new CategoriesModel($app);
                            $categoriesModel->deleteCategory($data['idcategory']);
                            $app['session']->getFlashBag()->add(
                                'message',
                                array(
                                    'type' => 'danger',
                                    'content' => $app['translator']
                                        ->trans('Category deleted')
                                )
                            );
                            return $app->redirect(
                                $app['url_generator']->
                                generate(
                                    'categories_index'
                                ),
                                301
                            );
                        }
                        $this->_view['form'] = $form->createView();
                    } else {
                        return $app->redirect(
                            $app['url_generator']->
                            generate(
                                'categories_add'
                            ),
                            301
                        );
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('Can not delete category with ads')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            'categories_index'
                        ),
                        301
                    );
                }
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('Did not found category')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'categories_index'
                    ),
                    301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Categories Exeption'));
        } return $app['twig']->render('categories/delete.twig', $this->_view);
    }
}
