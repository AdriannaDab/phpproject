<?php

/**
 * Advertisement service ads controller.
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Controller
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 **/

namespace Controller;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Model\AdsModel;
use Form\AdForm;
use Model\CategoriesModel;
use Model\UsersModel;

/**
 * Class AdsController
 *
 * @category Controller
 * @package  Controller
 * @author   Adrianna Dabkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~12_dabkowska
 * @uses Silex\Application
 * @uses Silex\ControllerProviderInterface
 * @uses Symfony\Component\HttpFoundation\Request
 * @uses Symfony\Component\Validator\Constraints
 * @uses Model\AdsModel
 * @uses Model\CategoriesModel
 * @uses Model\UsersModel
 */

class AdsController implements ControllerProviderInterface
{
    /**
     * AdsModel object.
     *
     * @var $_model
     * @access protected
     */
    protected $_model;

    /**
     * CategoryiesModel object.
     *
     * @var $_category
     * @access protected
     */
    protected $_category;

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
        $this->_model = new AdsModel($app);
        $this->_category = new CategoriesModel($app);
        $this->_user = new UsersModel($app);
        $adsController = $app['controllers_factory'];
        $adsController->match('/add', array($this, 'addAction'))
            ->bind('ads_add');
        $adsController->match('/add/', array($this, 'addAction'));
        $adsController->match('/edit/{id}', array($this, 'editAction'))
            ->bind('ads_edit');
        $adsController->match('/edit/{id}/', array($this, 'editAction'));
        $adsController->match('/delete/{id}', array($this, 'deleteAction'))
            ->bind('ads_delete');
        $adsController->match('/delete/{id}/', array($this, 'deleteAction'));
        $adsController->get('/view/{id}', array($this, 'viewAction'))
        ->bind('ads_view');
        $adsController->get('/view/{id}/', array($this, 'viewAction'));
        $adsController->get('/ads', array($this, 'indexAction'));
        $adsController->get('/ads/', array($this, 'indexAction'));
        $adsController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)->bind('/ads/');
        return $adsController;
    }
    /**
     * Index action.
     *
     * @access public
     * @param Application $app     application object
     * @param Request     $request request
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     *
     */

    public function indexAction(Application $app, Request $request)
    {
        $pageLimit = 3;
        $page = (int) $request->get('page', 1);
        $adsModel = new AdsModel($app);
        $this->_view = array_merge(
            $this->_view, $adsModel->getPaginatedAds($page, $pageLimit)
        );
        return $app['twig']->render('ads/index.twig', $this->_view);
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
        $adsModel = new AdsModel($app);
        $this->_view['ad'] = $adsModel->getAd($id);
        try {
            return $app['twig']->render('ads/view.twig', $this->_view);
        } catch (AdException $e) {
            echo 'Caught AdException: ' . $e->getMessage() . "\n";
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
        $data = array(
            'ad_name' => 'Advertisement',
            'ad_contence' => 'Contence',
        );

        $form = $app['form.factory']
            ->createBuilder(new AdForm($app), $data)->getForm();
        $form->remove('id');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $adsModel = new AdsModel($app);
            $adsModel->saveAd($data);
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
        }

        $app['session']->getFlashBag()->add(
            'message', array(
                'type' => 'success', 'content' => $app['translator']->trans('New ad added.')
            )
        );
        $this->_view['form'] = $form->createView();
        try {
            return $app['twig']->render('ads/add.twig', $this->_view);
        } catch (AdException $e) {
            echo 'Caught AdException: ' . $e->getMessage() . "\n";
        }

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
        $adsModel = new AdsModel($app);
        $id = (int) $request->get('id', 0);
        $ad = $adsModel->getAd($id);

        if (count($ad)) {
            $form = $app['form.factory']
                ->createBuilder(new AdForm($app), $ad)->getForm();
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $adsModel = new AdsModel($app);
                $adsModel->saveAd($data);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'success', 'content' => $app['translator']->trans('Ad edited.')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('/ads/'), 301
                );
            }

            $this->_view['form'] = $form->createView();
            $this->_view['id'] = $id;

        } else {
            return $app->redirect(
                $app['url_generator']->generate('ads_add'), 301
            );
        }
        try {
            return $app['twig']->render('ads/edit.twig', $this->_view);
        } catch (AdException $e) {
            echo 'Caught AdException: ' . $e->getMessage() . "\n";
        }

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
        $adsModel = new AdsModel($app);
        $id = (int) $request->get('id', 0);
        $ad = $adsModel->getAd($id);
        $this->_view['ad'] = $ad;

        if (count($ad)) {
            $form = $app['form.factory']
                ->createBuilder(new AdForm($app), $ad)->getForm();
            $form->remove('ad_name');
            $form->remove('ad_contence');
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $adsModel = new AdsModel($app);
                $adsModel->deleteAd($data['id']);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger', 'content' => $app['translator']->trans('Ad deleted.')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('ads_index'), 301
                );
            }

            $this->_view['form'] = $form->createView();

        } else {
            return $app->redirect(
                $app['url_generator']->generate('ads_add'), 301
            );
        }
        try {
            return $app['twig']->render('ads/delete.twig', $this->_view);
        } catch (AdException $e) {
            echo 'Caught AdException: ' . $e->getMessage() . "\n";
        }

    }

}