<?php
/**
 * Admins model.
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
 * Class Admins.
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
class AdminsModel
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
     * Connected user with his role.
     *
     * @param  Integer $iduser
     *
     * @access public
     * @return Void
     */
    public function addRole($iduser)
    {
        try {
            $query = '
              INSERT INTO
                `ad_users` (`iduser`, `idrole`)
              VALUES (?,?);
              ';
            $this->_db->executeQuery($query, array($iduser, '2'));
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * Connected user with his role.
     *
     * @param  $data
     *
     * @access public
     * @return Void
     */
    public function changeRole($data)
    {
        try {
            if (isset($data['iduser']) && ctype_digit((string)$data['iduser'])) {
                $query = '
              UPDATE
                ad_users
              SET
               idrole=?
              WHERE
                iduser = ?;
              ';
                $this->_db->executeQuery($query, array($data['idrole'], $data['iduser']));
            } else {
                $query = '
              INSERT INTO
                `ad_users` (`idrole`)
              VALUES (?);
            ';
                $this->_db
                    ->executeQuery(
                        $query,
                        array(
                            $data['idrole'])
                    );

            }
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * Gets users for pagination.
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     *
     * @return array Result
     */
    public function getPaginatedUsers($page, $limit)
    {
        $pagesCount = $this->countUsersPages($limit);
        $page = $this->getCurrentPageNumber($page, $pagesCount);
        $admins = $this->getUsersPage($page, $limit);
        return array(
            'admins' => $admins,
            'paginator' => array(
                'page' => $page,
                'pagesCount' => $pagesCount)
        );
    }

    /**
     * Counts user pages.
     *
     * @access public
     * @param integer $limit Number of records on single page
     * @return integer Result
     */
    public function countUsersPages($limit)
    {
        $pagesCount = 0;
        $sql = '
          SELECT COUNT(*)
          AS
            pages_count
          FROM
            ad_users
          NATURAL JOIN
            ad_user_data
          NATURAL JOIN
            ad_roles
        ';
        $result = $this->_db->fetchAssoc($sql);
        if ($result) {
            $pagesCount =  ceil($result['pages_count']/$limit);
        }
        return $pagesCount;
    }

    /**
     * Returns current page number.
     *
     * @access public
     * @param integer $page Page number
     * @param integer $pagesCount Number of all pages
     * @return integer Page number
     *
     */
    public function getCurrentPageNumber($page, $pagesCount)
    {
        return (($page <= 1) || ($page > $pagesCount)) ? 1 : $page;
    }

    /**
     * Get all users on page
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     * @param integer $pagesCount Number of all pages
     * @retun array Result
     */
    public function getUsersPage($page, $limit)
    {
        $sql = '
          SELECT
            *
          FROM
            ad_users
          LEFT JOIN
            ad_user_data
          ON
            ad_users.iduser=ad_user_data.iduser
          NATURAL JOIN
            ad_roles
          LIMIT :start, :limit
        ';
        $statement = $this->_db->prepare($sql);
        $statement->bindValue('start', ($page-1)*$limit, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
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
            if (($id != '') && ctype_digit((string)$id)) {
                $query = "
              SELECT
                *
              FROM
                ad_users
              NATURAL JOIN
                ad_user_data
              NATURAL JOIN
                ad_cities
              NATURAL JOIN
                 ad_provinces
              NATURAL JOIN
                 ad_countries
              WHERE
                iduser=?
                ";
                $result = $this->_db->fetchAssoc($query, array((int)$id));
                if (!$result) {
                    return array();
                } else {
                    return $result;
                }
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
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
    public function getUserId($id)
    {
        try {
            if (($id != '') && ctype_digit((string)$id)) {
                $query = "
              SELECT
                *
              FROM
                ad_users
              WHERE
                iduser=?
                ";
                $result = $this->_db->fetchAssoc($query, array((int)$id));
                if (!$result) {
                    return array();
                } else {
                    return $result;
                }
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
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
    public function getDeleteUser($id)
    {
        try {
            if (($id != '') && ctype_digit((string)$id)) {
                $query = "
              SELECT
                *
              FROM
                ad_users
              NATURAL JOIN
                ad_user_data
              WHERE
                iduser=?
                ";
                $result = $this->_db->fetchAssoc($query, array((int)$id));
                if (!$result) {
                    return array();
                } else {
                    return $result;
                }
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Check if users id exists
     *
     * @param $iduser Check id user from request
     *
     * @access public
     * @return bool True if exists.
     */
    public function checkUserId($iduser)
    {
        $sql = '
          SELECT
            *
          FROM
            ad_users
          WHERE
            iduser=?
        ';
        $result = $this->_db->fetchAll($sql, array($iduser));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets ad list from one user
     *
     * @access public
     * @param $id users ads data
     * @return array Result
     */
    public function getAdsListByIduser($id)
    {
        $sql = '
            SELECT
              *
            FROM
              ads
            NATURAL JOIN
             ad_users
            WHERE
              iduser = ?
        ';
        return $this->_db->fetchAll($sql, array($id));
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
                    ad_user_data
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
}
