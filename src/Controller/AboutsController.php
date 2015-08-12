<?php
/**
 * Advertisement service Abouts controller.
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
use Model\AboutsModel;
use Form\AboutForm;

/**
 * Class AboutsController
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
 * @uses Model\AboutsModel
 * @uses Form\AboutForm
 */
class AboutsController implements ControllerProviderInterface
{
    /**
     *
     * AboutsModel object.
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
        $this->_model = new AboutsModel($app);
        $aboutsController = $app['controllers_factory'];
        $aboutsController->match('/edit', array($this, 'editAction'));
        $aboutsController->match('/edit/', array($this, 'editAction'))
            ->bind('abouts_edit');
        $aboutsController->get('/about', array($this, 'viewAction'));
        $aboutsController->get('/about/', array($this, 'viewAction'));
        $aboutsController->get('/{page}', array($this, 'viewAction'))
            ->value('page', 1)
            ->bind('abouts_view');
        return $aboutsController;
    }

    /**
     * View About me
     *
     * @param Application $app     application object
     * @param Request     $request request
     *
     * @access public
     * @return mixed Generates page or redirects.
     */
    public function viewAction(Application $app, Request $request)
    {
        $about = $this->_model->getAboutId('aboutme');
        $idabout = $about['idabout'];
        $about = $this->_model->getAbout($idabout);
        return $app['twig']->render(
            'about/view.twig', array(
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