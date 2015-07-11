<?php
/**
 * User form.
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
use Model\UsersModel;

/**
 * Class UserForm.
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
class UserForm extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $data)
    {
        return $builder->add(
            'iduser',
            'hidden',
            array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
            ->add(
                'login', 'text', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'max' => 45,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 6',
                                'maxMessage' =>
                                    'Maksymalna ilość znaków to 45',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Nick nie jest poprawny',
                            )
                        )
                    )
                )
            )
            ->add(
                'password', 'password', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'minMessage' => 'Minimalna ilość znaków to 6',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Hasło nie jest poprawne',
                            )
                        )
                    )
                )
            )
            ->add(
                'confirm_password', 'password', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 6',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Hasło nie jest poprawne',
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
                       /* new Assert\Regex(
                            array(
                                'pattern' =>
                                    "/^[a-żA-Ż0-9\.\-_]+\@[a-żA-Ż0-9\.\-_]+\.[a-ż]{2,}/",
                                'message' => 'Email nie jest poprawny'
                            )
                        )*/
                    )
                )
            )

            ->add(
                'password', 'password', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 6',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Hasło nie jest poprawne',
                            )
                        )
                    )
                )
            )
            ->add(
                'new_password', 'password', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 6',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Hasło nie jest poprawne',
                            )
                        ),
                    )
                )
            )
            ->add(
                'confirm_new_password', 'password', array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(
                            array(
                                'min' => 6,
                                'minMessage' =>
                                    'Minimalna ilość znaków to 6',
                            )
                        ),
                        new Assert\Type(
                            array(
                                'type' => 'string',
                                'message' => 'Hasło nie jest poprawne',
                            )
                        ),
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
        return 'user';
    }

}