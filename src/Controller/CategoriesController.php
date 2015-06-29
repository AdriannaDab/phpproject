<?php
/**
 * Categories controller.
 *
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 */

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Model\CategoriesModel;
use Form\CategoryForm;

/**
 * Class CategoriesController.
 *
 * @package Controller
 * @implements ControllerProviderInterface
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
     *
     * CategriesModel object
     *
     * @var $_model
     * @access protected
     */
    protected $_model;

    /**
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @return CategoriesController Result
     */
    public function connect(Application $app)
    {
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
        $categoriesController->get('/', array($this, 'indexAction'));
        $categoriesController->get('/index/', array($this, 'indexAction'));
        $categoriesController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)->bind('categories_index');
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
        $pageLimit = 10;
        $page = (int) $request->get('page', 1);
        try {
        $categoriesModel = new CategoriesModel($app);
        $this->_view = array_merge(
            $this->_view, $categoriesModel->getPaginatedCategories($page, $pageLimit)
        );
        } catch (\PDOException $e) {
            $app['session']->getFlashBag()->add(
                'message',
                array(
                    'type' => 'error',
                    'content' => $app['translator']
                        ->trans('Categories error. Error code: '.$e->getCode())
                )
            );
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
        $id = (int)$request->get('id', null);
        $categoriesModel = new CategoriesModel($app);
        $this->_view['category'] = $categoriesModel->getCategory($id);
        $check = $this->_view['category'] = $categoriesModel->checkCategoryId($id);
        if ($check) {
            $ad = $this->_view['category'] = $categoriesModel->getAdsListByIdcategory($id);
            return $app['twig']
                ->render('categories/view.twig', array('ads' => $ad));
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono kategorii'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
        }
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
        $data = array(
            'category_name' => 'Name',
        );

        $form = $app['form.factory']
            ->createBuilder(new CategoryForm(), $data)->getForm();
        $form->remove('id');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $categoriesModel = new CategoriesModel($app);
            $categoriesModel->saveCategory($data);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans('New category added.')
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('categories_index'), 301
            );
        }

        $this->_view['form'] = $form->createView();
        } catch (Exception $e) {
            $app->abort($e->getCode(), $app['translator']->trans('Error'));
        }return $app['twig']->render('categories/add.twig', $this->_view);
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
        $this->_view['category'] = $category;

        if (count($category)) {

            $form = $app['form.factory']
                ->createBuilder(new CategoryForm(), $category)->getForm();
            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();
                $categoriesModel = new CategoriesModel($app);
                $categoriesModel->saveCategory($data);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'success', 'content' => $app['translator']->trans('Category edited.')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('categories_index'), 301
                );
            }

            $this->_view['form'] = $form->createView();
            $this->_view['id'] = $id;

        } else {
            return $app->redirect(
                $app['url_generator']->generate('categories_add'), 301
            );
        }
        } catch (Exception $e) {
            $app->abort($e->getCode(), $app['translator']->trans('Error'));
        }return $app['twig']->render('categories/edit.twig', $this->_view);
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
                ->createBuilder(new CategoryForm(), $category)->getForm();
            $form->remove('category_name');
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $categoriesModel = new CategoriesModel($app);
                $categoriesModel->deleteCategory($data['idcategory']);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger', 'content' => $app['translator']->trans('Category deleted.')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('categories_index'), 301
                );
            }

            $this->_view['form'] = $form->createView();

        } else {
            return $app->redirect(
                $app['url_generator']->generate('categories_add'), 301
            );
        }

                } else {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'danger',
                            'content' => 'Can not delete category with ads'
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate(
                            'categories_index'
                        ), 301
                    );
                }
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => 'Did not found category'
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'categories_index'
                    ), 301
                );
            }
        } catch (Exception $e) {
            $app->abort($e->getCode(), $app['translator']->trans('Error'));
        }
        return $app['twig']->render('categories/delete.twig', $this->_view);
    }




}