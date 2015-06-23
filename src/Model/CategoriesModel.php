<?php
/**
 * Categories model.
 *
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 */

namespace Model;

use Silex\Application;

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
     * @param Silex\Application $app Silex application
     */
    public function __construct(Application $app)
    {
        $this->_db = $app['db'];
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
        $sql = 'SELECT idcategory, category_name FROM ad_categories LIMIT :start, :limit';
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
        $sql = 'SELECT COUNT(*) as pages_count FROM ad_categories';
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
     * Gets all categories.
     *
     * @access public
     * @return array Result
     */
    public function getAll()
    {
        try {
            $query = 'SELECT idcategory, category_name FROM ad_categories';
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
                $query = 'SELECT idcategory, category_name FROM ad_categories WHERE idcategory= ?';
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
     * Add single category data.
     *
     * @access public
     * @param integer $id Record Id
     * @param string $title Record Title
     * @param string $artist Record Artist
     * @return array Result
     */

    /**
     * Save category.
     *
     * @access public
     * @param array $category Category data
     * @retun mixed Result
     */
    public function saveCategory($category)
    {
        if (isset($category['id'])
            && ($category['id'] != '')
            && ctype_digit((string)$category['id'])) {
            $id = $category['id'];
            unset($category['id']);
            return $this->_db->update('ads_categories', $category, array('id' => $id));
        } else {
            return $this->_db->insert('ads_categories', $category);
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
                $query = 'DELETE FROM ad_categories WHERE idcategory= ?';
                return $this->_db->delete('ads_categories', array('id' => $id));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
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
            'paginator' => array('page' => $page, 'pagesCount' => $pagesCount)
        );
    }

}