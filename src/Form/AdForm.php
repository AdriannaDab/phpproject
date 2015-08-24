<?php
/**
 * Ad form.
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
use Silex\Application;
use Model\CategoriesModel;

/**
 * Class AdForm.
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
class AdForm extends AbstractType
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
     * @param array $data
     *
     * @return FormBuilderInterface
     */
    public function buildForm(FormBuilderInterface $builder, array $data)
    {
        return  $builder->add(
            'idad',
            'hidden',
            array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
            ->add(
                'ad_name',
                'text',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'min' => 6,
                            'max' => 45,
                            'minMessage' => 'Minimalna ilość znaków to 6',
                            'maxMessage' => 'Maksymalna ilość znaków to {{ limit }}',
                         )),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Nazwa nie jest poprawna.',
                            )
                        )
                    ),
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Ad name'
                    ),
                )
            )
            ->add(
                'ad_contence',
                'textarea',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'min' => 10,
                            'max' => 150,
                            'minMessage' => 'Minimalna ilość znaków to 10',
                            'maxMessage' => 'Maksymalna ilość znaków to {{ limit }}',
                        )),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Treść nie jest poprawna.',
                            )
                        )
                    ),
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Ad contence'
                    ),
                )
            )
           ->add(
               'idcategory',
               'choice',
               array(
                   'constraints' => array(
                       new Assert\NotBlank()
                   ),
                   'attr' => array(
                       'class' => 'form-control'
                   ),
                    'choices' => $this->getCategories($this->app)
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
        return 'adForm';
    }

    /**
     * Form builder for categories list.
     *
     * @access public
     * @param FormBuilderInterface $builder
     * @param array $dict
     *
     * @return FormBuilderInterface
     */
    private function getCategories($app)
    {
        $categoriesModel = new CategoriesModel($app);
        $categories = $categoriesModel ->getAll();
        $dict = array();
        foreach ($categories as  $category){
            $dict [ $category ['idcategory']] = $category['category_name'];
        }return $dict;
    }

}