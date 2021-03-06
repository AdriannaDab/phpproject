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
            ->bind('comments_add');
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
     * @param Silex\Application $app Silex application
     * @param Symfony\Component\HttpFoundation\Request $request Request object
     * @return string Output
     *
     */
    public function indexAction(Application $app, Request $request)
    {
        try {
            $idad = (int)$request->get('idad', 0);
            $adsModel = new AdsModel($app);
            $category = $adsModel->getCategory($idad);
            $checkAd = $this->_ads = $adsModel->checkAdsId($idad);
            if ($checkAd) {
                $commentsModel = new CommentsModel($app);
                $comments = $this->_model = $commentsModel->getCommentsList($idad);
                $_isLogged = $this->_user->_isLoggedIn($app);
                if ($_isLogged) {
                    $access = $this->_user->getIdCurrentUser($app);
                    $moderator = $this->_user->getModeratorById($access, $category['idcategory']);
                } else {
                    $access = 0;
                    $moderator = false;
                }
                return $app['twig']->render(
                    'comments/index.twig',
                    array(
                        'comments' => $comments,
                        'idad' => $idad,
                        'access' => $access,
                        'moderator' => $moderator
                    )
                );
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']->trans('Comment not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Comments Exeption'));
        }  return $app->redirect(
            $app['url_generator']->
            generate(
                '/ads/'
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
            $idad = (int)$request->get('idad', 0);
            $adsModel = new AdsModel($app);
            $check = $this->_ads = $adsModel->checkAdsId($idad);
            if ($check) {
                if ($this->_user->_isLoggedIn($app)) {
                    $iduser = $this->_user->getIdCurrentUser($app);
                } else {
                    $iduser = 0;
                }
                $data = array(
                    'comment_date' => date('Y-m-d\TH:i:sO'),
                    'idad' => $idad,
                    'iduser' => $iduser
                );
                $form = $app['form.factory']
                    ->createBuilder(new CommentForm($app), $data)->getForm();
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $commentsModel = new CommentsModel($app);
                    $commentsModel->saveComment($data);
                    try {
                        $app['session']->getFlashBag()->add(
                            'message',
                            array(
                                'type' => 'success',
                                'content' => $app['translator']->trans('New comment added')
                            )
                        );
                        return $app->redirect(
                            $app['url_generator']->
                            generate(
                                'ads_view',
                                array(
                                    'id' => $data['idad']
                                )
                            ),
                            301
                        );
                    } catch (Exception $e) {
                        echo $app['translator']->trans('Caught Add Exception: ') . $e->getMessage() . "\n";
                    }
                }
                $this->_view['form'] = $form->createView();
                return $app['twig']->render('comments/add.twig', $this->_view);
            } else {
                $app['session']->getFlashBag()->add(
                    'message',
                    array(
                        'type' => 'danger',
                        'content' => $app['translator']->trans('Comment not found')
                    )
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Comments Exeption'));
        }  return $app->redirect(
            $app['url_generator']->
            generate(
                '/ads/'
            ),
            301
        );
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
            $commentsModel = new CommentsModel($app);
            $id = (int)$request->get('id', 0);
            $comment = $commentsModel->getComment($id);
            $idcurrentuser = $this->_user->getIdCurrentUser($app);
            if ($comment['iduser']==$idcurrentuser) {
                if (count($comment)) {
                    $form = $app['form.factory']
                        ->createBuilder(new CommentForm($app), $comment)->getForm();
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $commentsModel = new CommentsModel($app);
                        $commentsModel->saveComment($data);
                        $app['session']->getFlashBag()->add(
                            'message',
                            array(
                                'type' => 'success',
                                'content' => $app['translator']
                                    ->trans('Comment edited')
                            )
                        );
                        $this->_view['id'] = $id;
                        return $app->redirect(
                            $app['url_generator']->
                            generate(
                                'ads_view',
                                array(
                                    'id' => $comment['idad']
                                )
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
                            'comments_add',
                            array(
                                'idad' => $id
                            )
                        ),
                        301
                    );
                }
            } else {
                $app->abort(403, $app['translator']->trans('Forbidden'));
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Comments Exeption'));
        }  return $app['twig']->render('comments/edit.twig', $this->_view);
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
            $commentsModel = new CommentsModel($app);
            $id = (int)$request->get('id', 0);
            $comment = $commentsModel->getComment($id);
            $this->_view['comment'] = $comment;
            if (count($comment)) {
                $form = $app['form.factory']
                    ->createBuilder(new CommentForm($app), $comment)->getForm();
                $form->remove('contence');
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $commentsModel = new  CommentsModel($app);
                    $commentsModel->deleteComment($data['idcomment']);
                    $app['session']->getFlashBag()->add(
                        'message',
                        array(
                            'type' => 'danger',
                            'content' => $app['translator']
                                ->trans('Comment deleted')
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->
                        generate(
                            'ads_view',
                            array(
                                'id' => $comment['idad']
                            )
                        ),
                        301
                    );
                }
                $this->_view['form'] = $form->createView();
            } else {
                return $app->redirect(
                    $app['url_generator']->
                    generate(
                        'comments_add'
                    ),
                    301
                );
            }
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Caught Comments Exeption'));
        } return $app['twig']->render('comments/delete.twig', $this->_view);
    }
}
