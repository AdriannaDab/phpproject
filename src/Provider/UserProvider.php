<?php
/**
 * User provider.
 *
 * PHP version 5
 *
 * @category Provider
 * @package  Provider
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 **/

namespace Provider;

use Silex\Application;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Model\UsersModel;

/**
 * Class UserProvider.
 *
 * @category Epi
 * @package Provider
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version  Release: <package_version>
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @use Silex\Application
 * @use Symfony\Component\Security\Core\User\UserProviderInterface
 * @use Symfony\Component\Security\Core\User\UserInterface
 * @use Symfony\Component\Security\Core\User\User
 * @use Symfony\Component\Security\Core\Exception\UsernameNotFoundException
 * @use Symfony\Component\Security\Core\Exception\UnsupportedUserException
 * @use Model\UsersModel
 */
class UserProvider implements UserProviderInterface
{
    /**
     * Silex application.
     *
     * @access protected
     * @var Silex\Application $app
     */
    protected $app;

    /**
     * Object constructor.
     *
     * @access public
     * @param Silex\Application $app Silex application
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Load user by username.
     *
     * @access public
     * @param string $login User login
     *
     * @return User Result
     */
    public function loadUserByUsername($login)
    {
        $userModel = new UsersModel($this->app);
        $user = $userModel->loadUserByLogin($login);
        return new User(
            $user['login'],
            $user['password'],
            $user['roles'],
            true,
            true,
            true,
            true
        );
    }

    /**
     * Refresh user.
     *
     * @access public
     * @param UserInterface $user User
     *
     * @return User Result
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    get_class($user)
                )
            );
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Check if supports selected class.
     *
     * @access public
     * @param string $class Class name
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
