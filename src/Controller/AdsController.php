<?php
/**
 * Ads controller.
 *
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 */
namespace Controller;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AdsController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @access public
     * @param Silex\Application $app Silex application
     */
    public function connect(Application $app)
    {
        $adsController = $app['controllers_factory'];
        $adsController->get('/', array($this, 'indexAction'))->bind('/ads');
        $adsController->get('/add', array($this, 'addAction'))->bind('/ads/add');
        $adsController->get('/edit/{id}', array($this, 'editAction'))->bind('/ads/edit');
        $adsController->get('/delete/{id}', array($this, 'deleteAction'))->bind('/ads/delete');
        $adsController->get('/view/{id}', array($this, 'viewAction'))->bind('/ads/view');
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
        $view = array();
        return $app['twig']->render('ads/index.twig', $view);
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
        $view = array();
        return $app['twig']->render('ads/add.twig', $view);
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
        $view = array();
      	$view['id'] = $request->get('id', '');
      	return $app['twig']->render('ads/edit.twig', $view);
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
        $view = array();
      	$view['id'] = $request->get('id', '');
        return $app['twig']->render('ads/delete.twig', $view);
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
        $view = array();
      	$view['id'] = $request->get('id', '');
        return $app['twig']->render('ads/view.twig', $view);
    }
}
