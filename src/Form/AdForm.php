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
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                        new Assert\Length(array('min' => 5))
                    )
                )
            )
            ->add(
                'ad_contence',
                'text',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 5))
                    )
                )
            )
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