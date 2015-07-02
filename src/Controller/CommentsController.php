<?php

/**
 * Advertisement service comments controller.
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
use Model\CommentsModel;
use Model\AdsModel;
use Form\CommentForm;
use Model\UsersModel;

/**
 * Class CommentsController
 *
 * @category Controller
 * @package  Controller
 * @author   Adrianna Dabkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 * @uses Silex\ControllerProviderInterface
 * @uses Symfony\Component\HttpFoundation\Request
 * @uses Model\CommentsModel
 * @uses Model\ComentForm
 * @uses Model\UsersModel
 */

class CommentsController implements ControllerProviderInterface
{
    /**
     * CommentsModel object.
     *
     * @var $_model
     * @access protected
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
     * AdsModel object
     *
     * @access protected
     * @var $_ads
     */
    protected $_ads;

    /**
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->_model = new CommentsModel($app);
        $this->_user = new UsersModel($app);
        $this->_ads = new AdsModel($app);
        $commentController = $app['controllers_factory'];
        $commentController->match('/add/{idad}', array($this, 'addAction'))
            ->bind('/comments/add');
        $commentController->match('/edit/{id}', array($this, 'editAction'))
            ->bind('comments_edit');
        $commentController->match('/edit/{id}/', array($this, 'editAction'));
        $commentController->match('/delete/{id}', array($this, 'deleteAction'))
            ->bind('comments_delete');
        $commentController->match('/delete/{id}/', array($this, 'deleteAction'));
        $commentController->get('/{idad}/', array($this, 'indexAction'))
            ->bind('comments');
        return $commentController;
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
            $idAd = (int)$request->get('idad', 0); //pobieramy id z geta
            $adsModel = new AdsModel($app);
            $checkAd = $this->_ads = $adsModel->checkAdsId($idAd);
              // $checkAd = $this->_ads->checkAdsId($idAd); //używając funkcje z modelu sprawdzamy czy jest ogłószenie o takim id
                if ($checkAd) { //jeśli tak to działamy dalej
                    $commentsModel = new CommentsModel($app);
                    $comments = $this->_model = $commentsModel->getCommentsList($idAd); //pobieramy listę komentarzy
            //$_isLogged = $this->_user->_isLoggedIn($app);
            //if ($_isLogged) {
            //    $access = $this->_user->getIdCurrentUser($app);
            //} else {
            //   $access = 0;
            //}
            return $app['twig']->render('comments/index.twig', array(
                    'comments' => $comments,
                    'idad' => $idAd//, 'access' => $access
                )
            );
        } else { //jeśli nie to wyrzucamy komunikat i przekierowujemy
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => $app['translator']->trans('Comment not found')
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
        //var_dump('add');die();
        $idad = (int)$request->get('idad', 0);
        $adsModel = new AdsModel($app);
        $check = $this->_ads = $adsModel->checkAdsId($idad);
        if ($check) {
            //if ($this->_user->_isLoggedIn($app)) {
            //    $iduser = $this->_user->getIdCurrentUser($app);
            //} else {
            //    $iduser = 0;
            //}
            $data = array(
                'comment_date' => date('Y-m-d'),
                'contence' => 'Contence',
                'idad' => $idad,
            );
            $form = $app['form.factory']
                ->createBuilder(new CommentForm($app), $data)->getForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $commentsModel = new CommentsModel($app);
                $commentsModel->saveComment($data);
                try {
                    $this->_model->addComment($data);
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'success',
                            'content' => $app['translator']->trans('New comment added.')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate('/ads/'), 301
                    );
                } catch (Exception $e) {
                    echo $app['translator']->trans('Caught Add Exception: ') . $e->getMessage() . "\n";
                }
            }
            $this->_view['form'] = $form->createView();
            return $app['twig']->render('comments/add.twig', $this->_view);
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => $app['translator']->trans('Comment not found')
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
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
    public function edit(Application $app, Request $request)
    {
        $id = (int)$request->get('idad', 0);
        $check = $this->_model->checkCommentId($id);
        if ($check) {
           // $idCurrentUser = $this->_user->getIdCurrentUser($app);
            $comment = $this->_model->getComment($id);
            if (count($comment)) {
                $data = array(
                    'idcomment' => $id,
                    'ad_date' => date('Y-m-d'),
                    'idad' => $comment['idad'],
                    //'iduser' => $comment['iduser'],
                    //'idCurrentUser' => $idCurrentUser,
                    'content' => $comment['content'],
                );
                $form = $app['form.factory']
                    ->createBuilder(new CommentForm($app), $data)->getForm();
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    try {
                        $this->_model->editComment($data);
                        $app['session']->getFlashBag()->add(
                            'message', array(
                                'type' => 'success',
                                'content' => $app['translator']->trans ('Komanetarz został zmieniony')
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->generate('/ads/'), 301
                        );
                    } catch (Exception $e) {
                        echo $app['translator']->trans('Caught Edit Exception: ') .  $e->getMessage() . "\n";
                    }
                }
                return $app['twig']->render(
                    'comments/edit.twig', array(
                        'form' => $form->createView()
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => $app['translator']->trans ('Nie znaleziono komentarza')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('/comments/add'), 301
                );
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => $app['translator']->trans ('Nie znaleziono komentarza')
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
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
        $id = (int)$request->get('idad', 0);
        $check = $this->_model->checkCommentId($id);
        if ($check) {
            $comment = $this->_model->getComment($id);
            $data = array();
            if (count($comment)) {
                $form = $app['form.factory']
                    ->createBuilder(new CommentForm($app), $data)->getForm();
                $form->remove('contence');
                $form->handleRequest($request);
                if ($form->isValid()) {
                        $data = $form->getData();
                        try {
                            $this->_model->deleteComment($data);
                            $app['session']->getFlashBag()->add(
                                'message', array(
                                    'type' => 'success',
                                    'content' => $app['translator']->trans('Komantarz został usunięty')
                                )
                            );
                            return $app->redirect(
                                $app['url_generator']->generate('/ads/'), 301
                            );
                        } catch (\Exception $e) {
                            echo $app['translator']->trans('Caught Edit Exception: ') .  $e->getMessage() . "\n";
                        }
                }
                return $app['twig']->render(
                    'comments/delete.twig', array(
                        'form' => $form->createView()
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'danger',
                        'content' => $app['translator']->trans ('Nie znaleziono komentarza')
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('/ads/'), 301
                );
            }
        } else {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'danger',
                    'content' => $app['translator']->trans('Nie znaleziono komentarza')
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('/ads/'), 301
            );
        }
    }

}