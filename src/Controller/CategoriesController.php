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
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @return CategoriesController Result
     */
    public function connect(Application $app)
    {
        $adsController = $app['controllers_factory'];
        $adsController->match('/add', array($this, 'addAction'))
            ->bind('categories_add');
        $adsController->match('/add/', array($this, 'addAction'));
        $adsController->match('/edit/{id}', array($this, 'editAction'))
            ->bind('categories_edit');
        $adsController->match('/edit/{id}/', array($this, 'editAction'));
        $adsController->match('/delete/{id}', array($this, 'deleteAction'))
            ->bind('categories_delete');
        $adsController->match('/delete/{id}/', array($this, 'deleteAction'));
        $adsController->get('/view/{id}', array($this, 'viewAction'))
            ->bind('categories_view');
        $adsController->get('/view/{id}/', array($this, 'viewAction'));
        $adsController->get('/index', array($this, 'indexAction'));
        $adsController->get('/', array($this, 'indexAction'));
        $adsController->get('/index/', array($this, 'indexAction'));
        $adsController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)->bind('categories_index');
        return $adsController;
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
        $categoriesModel = new CategoriesModel($app);
        $this->_view = array_merge(
            $this->_view, $categoriesModel->getPaginatedCategories($page, $pageLimit)
        );
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
        return $app['twig']->render('categories/view.twig', $this->_view);
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

        return $app['twig']->render('categories/add.twig', $this->_view);
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

        return $app['twig']->render('categories/edit.twig', $this->_view);
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

        $categoriesModel = new CategoriesModel($app);
        $id = (int) $request->get('id', 0);
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

        return $app['twig']->render('categories/delete.twig', $this->_view);
    }




}