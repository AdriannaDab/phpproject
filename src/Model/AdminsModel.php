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
        $users = $this->getUsersPage($page, $limit);
        return array(
            'users' => $users,
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






}