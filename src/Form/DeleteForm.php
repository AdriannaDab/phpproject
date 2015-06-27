<?php
/**
 * Ad form.
 *
 * @author EPI <epi@uj.edu.pl>
 * @link http://epi.uj.edu.pl
 * @copyright 2015 EPI
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
 * @category Epi
 * @package Form
 * @extends AbstractType
 * @use Symfony\Component\Form\AbstractType
 * @use Symfony\Component\Form\FormBuilderInterface
 * @use Symfony\Component\OptionsResolver\OptionsResolverInterface
 * @use Symfony\Component\Validator\Constraints as Assert
 */
class AdForm extends AbstractType
{
    protected $app;

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
                            'min' => 5,
                            'max' => 45,
                            'minMessage' => 'Minimalna ilość znaków to 3',
                            'maxMessage' => 'Maksymalna ilość znaków to {{ limit }}',
                        )),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Nazwa nie jest poprawna.',
                            )
                        )
                    )
                )
            )
            ->add(
                'ad_contence',
                'text',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'min' => 5,
                            'max' => 150,
                            'minMessage' => 'Minimalna ilość znaków to 3',
                            'maxMessage' => 'Maksymalna ilość znaków to {{ limit }}',
                        )),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Treść nie jest poprawna.',
                            )
                        )
                    )
                )
            )
            /* ->add(
                 'ad_date', 'date', array(
                     'input' => 'string',
                     'widget' => 'single_text',
                     'constraints' => array(
                         new Assert\Date()
                     )
                 )
             )*/
            ->add(
                'idcategory',
                'choice',
                array(
                    'constraints' => array(
                        new Assert\NotBlank()
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