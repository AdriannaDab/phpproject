<?php
/**
 * Category form.
 *
 * PHP version 5
 *
 * @category Form
 * @package  Form
 * @author   Adrianna Dabkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Model\InformationModel;

/**
 * Class CategoryForm.
 *
 * @category Form
 * @package  Form
 * @author   Adrianna Dabkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses use Symfony\Component\Form\AbstractType
 * @uses Symfony\Component\Form\FormBuilderInterface
 * @uses Symfony\Component\OptionsResolver\OptionsResolverInterface
 * @uses Symfony\Component\Validator\Constraints as Assert
 */
class CategoryForm extends AbstractType
{
    /**
     * AdsForm object.
     *
     * @var $app
     * @access protected
     */
    protected $app;

    /**
     * Object constructor.
     *
     * @access public
     * @param Silex\Application $app Silex application
     */
    public function __construct($app)
    {
        $this-> app = $app;
    }

    /**
     * Form builder.
     *
     * @access public
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return  $builder->add(
            'idcategory',
            'hidden',
            array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
            ->add(
                'category_name',
                'text',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'min' => 5,
                            'minMessage' => 'Minimalna ilość znaków to 5'
                        )),
                        new Assert\Regex(array(
                            'pattern' => '/^(([A-ZĄĆĘŁŚŻŹ])+|[0-9]).*/',
                            'match'   => true,
                            'message' => 'Kategoria musi zaczynać się od wielkiej litery lub cyfry',
                        ))
                    )
                )
            )
            ->add(
                'iduser',
                'choice',
                array(
                    'constraints' => array(
                        new Assert\NotBlank()
                    ),
                    'choices' => $this->getUser($this->app)
                )
            );

    }

    /**
     * Gets form name.
     *
     * @access public
     *
     * @return string
     */
    public function getName()
    {
        return 'category';
    }

    /**
     * Form builder for moderators list.
     *
     * @access public
     * @param FormBuilderInterface $builder
     * @param array $dict
     *
     * @return FormBuilderInterface
     */
    private function getUser($app)
    {
        $usersModel = new InformationModel($app);
        $users = $usersModel ->getAllModerators();
        $dict = array();
        foreach ($users as  $user){
            $dict [ $user ['iduser']] = $user['login'];
        }return $dict;
    }

}
