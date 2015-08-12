<?php
/**
 * Advertisement service Projects controller.
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
use Model\ProjectsModel;
use Form\ProjectForm;

/**
 * Class ProjectsController
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
 * @uses Model\ProjectsModel
 * @uses Form\ProjectsForm
 */
class ProjectsController implements ControllerProviderInterface
{
    /**
     *
     * ProjectsModel object.
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
        $this->_model = new ProjectsModel($app);
        $projectsController = $app['controllers_factory'];
        $projectsController->match('/edit', array($this, 'editAction'));
        $projectsController->match('/edit/', array($this, 'editAction'))
            ->bind('projects_edit');
        $projectsController->match('/delete', array($this, 'deleteAction'));
        $projectsController->match('/delete/', array($this, 'deleteAction'))
            ->bind('projects_delete');
        $projectsController->get('/projects', array($this, 'indexAction'));
        $projectsController->get('/projects/', array($this, 'indexAction'));
        $projectsController->get('/{page}', array($this, 'indexAction'))
            ->value('page', 1)
            ->bind('projects_index');
        return $projectsController;
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
        try {
            $about = $this->_model->getAboutId('aboutme');
            $idabout = $about['idabout'];
            $about = $this->_model->getAbout($idabout);
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
        return $app['twig']->render(
            'about/index.twig', array(
                'about' => $about
            )
        );
    }


    /**
     * Edit about me
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirect.
     */
    public function editAction(Application $app, Request $request)
    {
        $about = $this->_model->getAbout('aboutme');
        $currentAbout = $this->_model->getAboutId('aboutme');
        $idabout = $currentAbout['idabout'];
        $data = array(
            'idabout' => $idabout,
            'name' => $this->getAttributeName($about, 'imie'),
            'surname' => $this->getAttributeName($about, 'nazwisko'),
            'email' => $this->getAttributeName($about, 'email'),
            'contence' => $this->getAttributeName($about, 'opis'),
        );
        $form = $app['form.factory']
            ->createBuilder(new AboutForm($app), $data)->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $model = $this->_model->updateAbout($data);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'success',
                        'content' => 'Informacje zostały dodane'
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate(
                        'about_me'
                    ), 301
                );
            } catch (\Exception $e) {
                $errors[] = 'Coś poszło niezgodnie z planem';
            }
        }
        return $app['twig']->render(
            'about/edit.twig', array(
                'form' => $form->createView()
            )
        );
    }

}