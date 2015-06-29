<?php
/**
 * Album form.
 *
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AlbumForm.
 *
 * @package Form
 * @extends AbstractType
 */
class CategoryForm extends AbstractType
{
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
            'idcategory', 'hidden',
            array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
            ->add(
                'category_name', 'text',
                array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('min' => 5)),
                         new Assert\Regex(array(
                             'pattern' => '/^(([A-ZĄĆĘŁŚŻŹ])+|[0-9]).*/',
                             'match'   => true,
                             'message' => 'Kategoria musi zaczynać się od wielkiej litery lub cyfry',
                         ))
                    )
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

}
