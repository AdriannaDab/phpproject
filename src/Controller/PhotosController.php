<?php
/**
 * Advertisement service Photoss controller.
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
use Model\UsersModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Form\PhotoForm;
use Model\PhotosModel;
use Model\AdsModel;


/**
 * Class PhotosController
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
 * @uses Model\PhotosModel
 * @uses Form\PhotoForm
 * @uses Model\AdsModel
 */
class PhotosController implements ControllerProviderInterface
{
    /**
     * PhotosModel object.
     *
     * @var $_model
     * @access protected
     */
    protected $_model;

    /**
     * AdsModel object.
     *
     * @var $_ads
     * @access protected
     */
    protected $_ads;

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
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->_model = new PhotosModel($app);
        $this->_ads = new AdsModel($app);
        $photosController = $app['controllers_factory'];
        $photosController->match('/upload/{idad}', array($this, 'uploadAction'))
            ->bind('photos_upload');
        $photosController->match('/manager/', array($this, 'managerAction'))
            ->bind('photos_manager');
        $photosController->match('/delete/{photo_name}', array($this, 'deleteAction'))
            ->bind('photos_delete');
        $photosController->get('/{page}/{idad}/', array($this, 'indexAction'))
            ->value('page', 1)
            ->bind('/photos/');
        return $photosController;

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
        $idad = (int)$request->get('idad', 0);
        $adsModel = new AdsModel($app);
        $check = $this->_ads = $adsModel->checkAdsId($idad);
        if ($check) {
            $photosModel = new PhotosModel($app);
            $photos = $this->_model=$photosModel->getPhotosByAd($idad);
            return $app['twig']->render(
                'photos/index.twig', array(
                    'photos' => $photos
                )
            );
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Ad not found'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/ads/'
                ), 301
            );
        }
    }

    /**
     * Upload action
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function uploadAction(Application $app, Request $request)
    {
        $idad = (int)$request->get('idad', 0);

        $userModel = new UsersModel($app);
        $iduser = $userModel->getIdCurrentUser($app);
        $adsModel = new AdsModel($app);
        $check = $this->_ads = $adsModel->checkAdsId($idad);

        if ($check) {
            $ad = $adsModel->getAd($idad);
            $idcategory = $ad['idcategory'];

            $date = date('Y-m-d H:i:s');
            $data = array(
                'idad' => $idad,
                'iduser' => $iduser,
                'idcategory'=>$idcategory,
                'photo_date'=>$date
            );
            $form = $app['form.factory']
                ->createBuilder(new PhotoForm($app), $data)->getForm();
            if ($request->isMethod('POST')) {
                $form->bind($request);
                if ($form->isValid()) {
                    try {
                        $files = $request->files->get($form->getName());
                        $data = $form->getData();
                        $path
                            = dirname(dirname(dirname(__FILE__))) .
                            '/web/media';
                        $originalFilename
                            = $files['file']->getClientOriginalName();
                        $newFilename
                            = $this->_model->createName($originalFilename);
                        $files['file']->move($path, $newFilename);
                        $this->_model->savePhoto($newFilename, $data);
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'success',
                                'content' => 'Zdjecie zostało pobrane'
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate(
                                '/ads/'
                            ), 301
                        );
                    } catch (Exception $e) {
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'danger',
                                'content' => 'Nie można pobrać zdjecia'
                            )
                        );
                    }
                } else {
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'danger',
                            'content' => 'Dane niepoprawne'
                        )
                    );
                }
            }
            return $app['twig']->render(
                'photos/upload.twig', array(
                    'form' => $form->createView(),
                    'idad' => $idad
                )
            );
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono zdjęcia'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    '/ads/'
                ), 301
            );
        }
    }

    /**
     * Delete photo
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @return mixed Generates page.
     */
    public function deleteAction(Application $app, Request $request)
    {
        $name = (string)$request->get('photo_name', 0);
        $photosModel = new PhotosModel($app);
        $check = $this->_model = $photosModel->checkPhotoName($name);
        if ($check) {
            $photo = $this->_model= $photosModel->getPhotoByName($name);
            $path = dirname(dirname(dirname(__FILE__))) . '/web/media/' . $name;
            if (count($photo)) {
                $data = array();
                $form = $app['form.factory']
                    ->createBuilder(new PhotoForm($app), $data)->getForm();
                $form->remove('photo_name');
                $form->remove('file');
                $form->remove('photo_alt');
                $form->handleRequest($request);
                if ($form->isValid()) {
                        $data = $form->getData();
                        try {
                            $model = unlink($path);
                            try {
                                $link = $this->_model = $photosModel->removePhoto($name);
                                $app['session']->getFlashBag()->add(
                                    'message', array(
                                        'type' => 'success',
                                        'content' =>
                                            'Zdjecie zostało usunięte'
                                    )
                                );
                                return $app->redirect(
                                    $app['url_generator']->generate(
                                        'photos_manager'
                                    ), 301
                                );
                            } catch (\Exception $e) {
                                $errors[] = 'Coś poszło niezgodnie z planem';
                            }
                        } catch (\Exception $e) {
                            $errors[] = 'Plik nie zstał usuniety';
                        }

                }
                return $app['twig']->render(
                    'photos/delete.twig', array(
                        'form' => $form->createView()
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => 'Nie znaleziono zdjęcia'
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'photos_manager'
                    ), 301
                );
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => 'Nie znaleziono zdjęcia'
                )
            );
            return $app->redirect(
                $app['url_generator']->generate(
                    'photos_manager'
                ), 301
            );
        }
    }

    /**
     * Photos control panel
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates Page.
     */
    public function managerAction(Application $app, Request $request)
    {

        $userModel = new UsersModel($app);
        $iduser = $userModel->getIdCurrentUser($app);
            $photosModel = new PhotosModel($app);
            $idModerator = $photosModel->getMod($iduser);
            $photos = $photosModel->getPhotosMod($idModerator);


            return $app['twig']->render(
                'photos/manager.twig', array(
                    'photos' => $photos
                )
            );
        }


}
