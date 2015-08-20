<?php
/**
 * Categories model.
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 */

namespace Model;

use Silex\Application;
/**
 * Class CategoriessModel
 *
* @category Model
* @package  Model
* @author   Adrianna Dąbkowska
* @email    adrianna.dabkowska@uj.edu.pl
* @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
* @uses Silex\Application
*/
class CategoriesModel
{
    /**
     * Db object.
     *
     * @access protected
     * @var Silex\Provider\DoctrineServiceProvider $_db
     */
    protected $_db;

    /**
     * Object constructor.
     *
     * @access public
     * @param Silex\Application $app Silex application object
     */
    public function __construct(Application $app)
    {
        $this->_db = $app['db'];
    }

    /**
     * Gets all categories.
     *
     * @access public
     * @return array Result
     */
    public function getAll()
    {
        try {
            $query = '
              SELECT
                idcategory, category_name
              FROM
                ad_categories
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Gets single category data.
     *
     * @access public
     * @param integer $id Record Id
     * @return array Result
     */
    public function getCategory($id)
    {
        try {
            if (($id != '') && ctype_digit((string)$id)) {
                $query = '
                  SELECT
                    *
                  FROM
                    ad_categories
                  NATURAL JOIN
                    ad_moderator_category
                  WHERE
                    idcategory= ?
                ';
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
     * Edit category.
     *
     * @param  Integer $iduser
     *
     * @access public
     * @return Void
     */
    public function edit($data)
    {
        if (isset($data['idcategory']) && ctype_digit((string)$data['idcategory'])) {
                $query = '
              UPDATE
                ad_moderator_category
              NATURAL JOIN
                ad_categories
              SET
                category_name=?,
                iduser=?
              WHERE
                idcategory= ?;
              ';
                $this->_db->executeQuery(
                    $query, array(
                    $data['category_name'],
                    $data['iduser'],
                    $data['idcategory']
                )
                );

        } else {
            $query = '
              INSERT INTO
                `ad_moderator_category` (`idcategory`)
              VALUES (?);
            ';
            $this->_db
                ->executeQuery(
                    $query,
                    array(
                        $data['idcategory'])
                );
        }


    }

    /**
     * Puts category to database.
     *
     * @param  Array $data Associative array contains all necessary information
     *
     * @access public
     * @return Void
     */
    public function add($data)
    {
            $categories = "
              INSERT INTO
                `ad_categories` ( `category_name`)
              VALUES
                (?)
            ";
            $this->_db
                ->executeQuery(
                    $categories,
                    array(
                        $data['category_name'],
                    )
                );

            $sql = "SELECT
                      idcategory, category_name
                    FROM
                      ad_categories

                    WHERE
                      category_name ='" . $data['category_name'] . "';";

            $category = $this->_db->fetchAssoc($sql);


            $addid = 'INSERT INTO
                          ad_moderator_category ( idcategory )
                      VALUES
                        (?)';
            $this->_db->executeQuery($addid, array($category['idcategory']));


    }



    /**
     * Get all categories on page
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     * @param integer $pagesCount Number of all pages
     * @retun array Result
     */
    public function getCategoriesPage($page, $limit)
    {
        $sql = '
          SELECT
            *
          FROM
            ad_categories
          NATURAL JOIN
            ad_moderator_category
           LEFT join
            ad_users
          ON
            ad_moderator_category.iduser=ad_users.iduser
          ORDER BY
            login
          DESC
          LIMIT :start, :limit
        ';
        $statement = $this->_db->prepare($sql);
        $statement->bindValue('start', ($page-1)*$limit, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Counts category pages.
     *
     * @access public
     * @param integer $limit Number of records on single page
     * @return integer Result
     */
    public function countCategoriesPages($limit)
    {
        $pagesCount = 0;
        $sql = '
          SELECT COUNT(*)
          AS
            pages_count
          FROM
            ad_categories
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
     * Gets categories for pagination.
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     *
     * @return array Result
     */
    public function getPaginatedCategories($page, $limit)
    {
        $pagesCount = $this->countCategoriesPages($limit);
        $page = $this->getCurrentPageNumber($page, $pagesCount);
        $categories = $this->getCategoriesPage($page, $limit);
        return array(
            'categories' => $categories,
            'paginator' => array(
                'page' => $page,
                'pagesCount' => $pagesCount)
        );
    }


    /**
     * Delete single category data.
     *
     * @access public
     * @param integer $id Record Id
     * @return array Result
     */
    public function deleteCategory($id)
    {
        if (isset($id) && ctype_digit((string)$id)) {
            $query = '
              DELETE FROM
                ad_categories
              WHERE
                idcategory= ?
            ';
            $success = $this->_db->executeQuery($query, array($id));
            if ($success) {
                $queryTwo = '
                  DELETE FROM
                    ad_moderator_category
                  WHERE
                    idcategory = ?
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

    /**
     * Gets ads from one category
     *
     * @access public
     * @param array $ads ads data
     * @return array Result
     */
    public function getAdsListByIdcategory($id)
    {
        $sql = '
            SELECT
              *
            FROM
              ads
            NATURAL JOIN
             ad_categories
            WHERE
              idcategory = ?
        ';
        return $this->_db->fetchAll($sql, array($id));
    }


    /**
     * Check if category id exists
     *
     * @param $idcategory id category from request
     *
     * @access public
     * @return bool True if exists.
     */
    public function checkCategoryId($idcategory)
    {
        $sql = '
          SELECT
            *
          FROM
            ad_categories
          WHERE
            idcategory=?
        ';
        $result = $this->_db->fetchAll($sql, array($idcategory));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}