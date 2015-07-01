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
                    idcategory, category_name
                  FROM
                    ad_categories
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
            idcategory, category_name
          FROM
            ad_categories
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
     * trzeba jeszcze to rzutować na integera!
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
     * Save category.
     *
     * @access public
     * @param array $category Category data
     * @retun mixed Result
     */
    public function saveCategory($category)
    {
        if (isset($category['idcategory'])
            && ($category['idcategory'] != '')
            && ctype_digit((string)$category['idcategory'])) {
            $id = $category['idcategory'];
            unset($category['idcategory']);
            return $this->_db->update('ad_categories', $category, array('idcategory' => $id));
        } else {
            return $this->_db->insert('ad_categories', $category);
        }
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
        try {
            if (($id != '') && ctype_digit((string)$id) ) {
                $query = '
                  DELETE
                    *
                  FROM
                    ad_categories
                  WHERE
                    idcategory= ?
                ';
                return $this->_db->delete('ad_categories', array('idcategory' => $id));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
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