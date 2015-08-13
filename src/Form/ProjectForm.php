<?php
/**
 * About form.
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
class AboutForm extends AbstractType
{
    /**
     * AboutForm object.
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
    public function buildForm(FormBuilderInterface $builder, array $data)
    {
        return  $builder->add(
            'idabout',
            'hidden',
            array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
            ->add(
                'name', 'text', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 3,
                                'max' => 45,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 3',
                                'maxMessage' => '
                                Maksymalna ilość znaków to {{ limit }}',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Tekst nie jest poprawny',
                            )
                        )
                    )
                )
            )
            ->add(
                'surname', 'text', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 3,
                                'max' => 45,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 3',
                                'maxMessage' =>
                                    'Maksymalna ilość znaków to {{ limit }}',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Tekst nie jest poprawny',
                            )
                        )
                    )
                )
            )
            ->add(
                'content', 'textarea',array('required' => false), array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 5,
                                'minMessage' => 'Minimalna ilość znaków to 5',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Tekst nie jest poprawny',
                            )
                        )
                    )
                )
            )
            ->add(
                'email', 'email', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 5
                            )
                        ),
                        new Assert\Regex(
                            array(
                                'pattern' =>
                                    "/^[a-zA-Z0-9\.\-_]+\@[a-zA-Z0-9\.\-_]+\.[a-z]{2,4}/",
                                'message' => 'Email niepoprawny'
                            )
                        )
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
        return 'aboutForm';
    }

}