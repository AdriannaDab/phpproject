<?php
/**
 * Users model.
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 */

namespace Model;

use Silex\Application;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class Users.
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 * @uses Doctrine\DBAL\DBALException;
 * @uses Symfony\Component\Security\Core\Exception\UnsupportedUserException;
 * @uses Symfony\Component\Security\Core\Exception\UsernameNotFoundException; *
 */
class UsersModel
{

    /**
     * Silex application object
     *
     * @access protected
     * @var $_app Silex\Application
     */
    protected $_app;

    /**
     * Db object.
     *
     * @access protected
     * @var Silex\Provider\DoctrineServiceProvider $db
     */
    protected $_db;

    /**
     * Object constructor.
     *
     * @access public
     * @param Silex\Application $app Silex application
     */
    public function __construct(Application $app)
    {
        $this->_db = $app['db'];
    }

    /**
     * Loads user by login.
     *
     * @access public
     * @param string $login User login
     * @throws UsernameNotFoundException
     * @return array Result
     */
    public function loadUserByLogin($login)
    {
        $user = $this->getUserByLogin($login);

        if (!$user || !count($user)) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)//tlumaczenie dodaj
            );
        }

        $roles = $this->getUserRoles($user['iduser']);

        if (!$roles || !count($roles)) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)//tlumaczenie dodaj
            );
        }

        return array(
            'login' => $user['login'],
            'password' => $user['password'],
            'roles' => $roles
        );

    }

    /**
     * Gets user data by login.
     *
     * @access public
     * @param string $login User login
     *
     * @return array Result
     */
    public function getUserByLogin($login)
    {
        try {
            $query = '
              SELECT
                `iduser`, `login`, `password`, `idrole`
              FROM
                `ad_users`
              WHERE
                `login` = :login
            ';
            $statement = $this->_db->prepare($query);
            $statement->bindValue('login', $login, \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : current($result);
        } catch (\PDOException $e) {
            return array();
        }
    }

    /**
     * Gets user roles by User ID.
     *
     * @access public
     * @param integer $userId User ID
     *
     * @return array Result
     */
    public function getUserRoles($userId)
    {
        $roles = array();
        try {
            $query = '
                SELECT
                    `ad_roles`.`role_name` as `role`
                FROM
                    `ad_users`
                INNER JOIN
                    `ad_roles`
                ON `ad_users`.`idrole` = `ad_roles`.`idrole`
                WHERE
                    `ad_users`.`iduser` = :user_id
                ';
            $statement = $this->_db->prepare($query);
            $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if ($result && count($result)) {
                $result = current($result);
                $roles[] = $result['role'];
            }
            return $roles;
        } catch (\PDOException $e) {
            return $roles;
        }
    }



    /**
     *
     * Get information about user
     *
     * @param $id user id
     *
     * @access public
     * @return array Associative array with information about user
     */
    public function getUser($id)
    {
        try {
            $query = "
              SELECT
                *
              FROM
                ad_users
              NATURAL JOIN
                ad_user_data
              NATURAL JOIN
                ad_cities, ad_provinces, ad_countries
              WHERE
                iduser=?
            ";
            return $this->_db->fetchAssoc($query, array((int)$id));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Puts one user to database.
     *
     * @param  Array $data Associative array contains all necessary information
     *
     * @access public
     * @return Void
     */
    public function register($data)
    {
        $check = $this->getUserByLogin($data['login']);
        if (!$check) {
            $users = "
              INSERT INTO
                `ad_users` (`login`, `email`, `password`)
              VALUES
                (?,?,?);
            ";
            $this->_db
                ->executeQuery(
                    $users,
                    array(
                        $data['login'],
                        $data['email'],
                        $data['password'])
                );
            $query = "
              SELECT
                *
              FROM
                ad_users
              WHERE
                login ='" . $data['login'] . "';
            ";
            $user = $this->_db->fetchAssoc($query);
            $addRole = '
              INSERT INTO
                ad_users ( iduser, idrole )
              VALUES
                (?, ?)';
            $this->_db->executeQuery($addRole, array($user['iduser'], 2));
        }
    }

    /**
     * Updates information about user.
     *
     * @param Array $data Associative array contains all necessary information
     *
     * @access public
     * @return Void
     */
    public function editUser($data)
    {
        if (isset($data['iduser']) && ctype_digit((string)$data['iduser'])) {
            $query = '
              UPDATE
                ad_users
              SET
                email = ?,
              WHERE
                iduser = ?
            ';
            $this->_db->executeQuery(
                $query, array(
                    $data['email'],
                    $data['iduser']
                )
            );
        } else {
            $query = '
              INSERT INTO
                `ad_users` (`email`, `password`)
              VALUES (?,?);
            ';
            $this->_db
                ->executeQuery(
                    $query,
                    array(
                        $data['login'],
                        $data['email'],
                        $data['password'],)
                );
        }
    }

    /**
     * Delete user
     *
     * @param Integer $id user id
     *
     * @access public
     * @return bool true if deleted
     */
    public function deleteUser($id)
    {
        if (isset($id) && ctype_digit((string)$id)) {
            $query = '
              DELETE FROM
                ad_users
              WHERE
                iduser = ?
            ';
            $success = $this->_db->executeQuery($query, array($id));
            if ($success) {
                $queryTwo = '
                  DELETE FROM
                    ad_users_data
                  WHERE
                    iduser = ?
                ';
                $successTwo = $this->_db->executeQuery($queryTwo, array($id));
                if ($successTwo) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    public function changePassword($data, $id)
    {
        try{
            $query = '
              UPDATE
                `ad_users`
              SET
                `password`=?
              WHERE
                `iduser`= ?
            ';
            $this->_db->executeQuery($query, array($data['new_password'], $id));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }


    /**
     * Gets user by id.
     *
     * @param Integer $id
     *
     * @access public
     * @return Array Information about searching user.
     */
    public function getUserById($id)
    {
        try{
            $query = '
              SELECT
                *
              FROM
                ad_users
              WHERE
                `iduser` = ? Limit 1
            ';
            return $this->_db->fetchAssoc($query, array((int)$id));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }


    /**
     * Connected user with his role.
     *
     * @param  Integer $iduser
     *
     * @access public
     * @return Void
     */
    public function addRole($iduser)
    {
        try{
            $query = '
              INSERT INTO
                `ad_users` (`iduser`, `idrole`)
              VALUES (?,?);
            ';
            $this->_db->executeQuery($query, array($iduser, '2'));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Confirm user. Change his role
     *
     * @param  Integer $id
     *
     * @access public
     * @return Void
     */
    public function confirmUser($id)
    {
        try{
            $query = '
              UPDATE
                `ad_users`
              SET
                `idrole`="2"
              WHERE `iduser`= ?;
            ';
            $this->_db->executeQuery($query, array($id));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Get current logged user id
     *
     * @param $app
     *
     * @access public
     * @return mixed
     */
    public function getIdCurrentUser($app)
    {
        $login = $this->getCurrentUser($app);
        $iduser = $this->getUserByLogin($login);
        return $iduser['iduser'];
    }

    /**
     * Get information about actual logged user
     *
     * @param $app
     *
     * @access protected
     * @return mixed
     */
    protected function getCurrentUser($app)
    {
        $token = $app['security']->getToken();
        if (null !== $token) {
            $user = $token->getUser()->getUsername();
        }
        return $user;
    }

    /**
     * Check if user is logged
     *
     * @param Application $app
     *
     * @access public
     * @return bool
     */
    public function _isLoggedIn(Application $app)
    {
        if ('anon.' !== $user = $app['security']->getToken()->getUser()) {
            return true;
        } else {
            return false;
        }
    }

}
