<?php

/**
 * Advertisement service Ads controller.
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
use Model\AdsModel;
use Form\AdForm;
use Model\UsersModel;
use Model\PhotosModel;

/**
 * Class AdsController
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
 * @uses Model\AdsModel
 * @uses Model\UsersModel
 * @uses Model\PhotosModel
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
     * PhotosModel object.
     *
     * @var $_model
     * @access protected
     */
    protected $_photos;

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
        $this->_user = new UsersModel($app);
        $this->_photos = new PhotosModel($app);
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
            ->value('page', 1)
            ->bind('/ads/');
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
        try {
            $adsModel = new AdsModel($app);
            $this->_view = array_merge(
                $this->_view, $adsModel->getPaginatedAds($page, $pageLimit)
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
        $id = (int)$request->get('id', 0);
        $adsModel = new AdsModel($app);
        $category = $adsModel->getCategory($id);
        $ad=$this->_view['ad'] = $adsModel->getAdView($id);
        $_isLogged = $this->_user->_isLoggedIn($app);
        if ($_isLogged) {
            $access = $this->_user->getIdCurrentUser($app);
            $moderator = $this->_user->getModeratorById($access, $category['idcategory']);
        } else {
            $moderator = false;
            $access = 0;
        }
        if (!($this->_view['ad'])) {
            throw new NotFoundHttpException("Ad not found");
        }
        return $app['twig']->render('ads/view.twig', array(
            'access' => $access,
            'moderator' => $moderator,
            'ad' => $ad
        ));
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
        $id = $this->_user->getIdCurrentUser($app);
        $user = $this->_user->CheckUser($id);
        if ($user) {
            if ($this->_user->_isLoggedIn($app)) {
                $iduser = $this->_user->getIdCurrentUser($app);
            } else {
                $iduser = 0;
            }
        $data = array(
            'ad_date' => date('Y-m-d'),
            'iduser'=>$iduser
        );
        $form = $app['form.factory']
            ->createBuilder(new AdForm($app), $data)->getForm();
        $form->remove('id');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $adsModel = new AdsModel($app);
            $adsModel->saveAd($data);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success',
                    'content' => $app['translator']
                        ->trans('New ad added')
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
        }
        $this->_view['form'] = $form->createView();
        } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => 'User data not found'
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'users_data'
                    ), 301
                );
        }
        return $app['twig']->render('ads/add.twig', $this->_view);
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
                        'type' => 'success',
                        'content' => $app['translator']
                            ->trans('Ad edited')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('ads_view', array('id' => $ad['idad'])), 301
                );
            }
            $this->_view['form'] = $form->createView();
            $this->_view['id'] = $id;
        } else {
            return $app->redirect(
                $app['url_generator']->generate('ads_add'), 301
            );
        }
        return $app['twig']->render('ads/edit.twig', $this->_view);
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
            $form->remove('idcategory');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $adsModel = new AdsModel($app);
                $adsModel->deleteAd($data['idad']);
                $photosModel = new PhotosModel($app);
                $photos = $this->_photos = $photosModel
                    ->getPhotosByAd($data['idad']);
                foreach ($photos as $photo) {
                    $path
                        = dirname(dirname(dirname(__FILE__))).
                        '/web/media/'.$photo['photo_name'];
                    unlink($path);
                    $this->_photos = $photosModel
                        ->removePhoto($photo['photo_name']);
                }
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => $app['translator']
                            ->trans('Ad deleted')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('/ads/'), 301
                );
            }
            $this->_view['form'] = $form->createView();
        } else {
            return $app->redirect(
                $app['url_generator']->generate('ads_add'), 301
                );
            }
        return $app['twig']->render('ads/delete.twig', $this->_view);
    }

}