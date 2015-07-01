<?php
/**
 * Ads model.
 *
 * PHP version 5
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
 * Class AdsModel
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 */
class AdsModel
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
     * Gets all ads.
     *
     * @access public
     * @return array Ads Array
     */
    public function getAll()
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ads
              ORDER BY
                ad_date
              DESC
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Gets single ad data.
     *
     * @access public
     * @param integer $id Record Id
     * @return array Array contains information about single ad
     */
    public function getAd($id)
    {
        try {
            if (($id != '') && ctype_digit((string)$id)) {
                $query = '
                  SELECT
                    *
                  FROM
                    ads
                  WHERE
                    idad= ?
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
     * Get all ads on page
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     * @param integer $pagesCount Number of all pages
     * @retun array Result
     */
    public function getAdsPage($page, $limit, $pagesCount)
    {
        $sql = '
          SELECT
            idad, ad_name, ad_contence
          FROM
            ads
          ORDER BY
            ad_date
          DESC LIMIT :start, :limit
        ';
        $statement = $this->_db->prepare($sql);
        $statement->bindValue('start', ($page-1)*$limit, \PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Counts ad pages.
     *
     * @access public
     * @param integer $limit Number of records on single page
     * @return integer Result
     */
    public function countAdsPages($limit)
    {
        $pagesCount = 0;
        $sql = '
          SELECT COUNT(*)
          AS
            pages_count
          FROM
            ads
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
     */
    public function getCurrentPageNumber($page, $pagesCount)
    {
        return (($page <= 1) || ($page > $pagesCount)) ? 1 : $page;
    }

    /**
     * Gets ads for pagination.
     *
     * @access public
     * @param integer $page Page number
     * @param integer $limit Number of records on single page
     *
     * @return array Result
     */
    public function getPaginatedAds($page, $limit)
    {
        $pagesCount = $this->countAdsPages($limit);
        $page = $this->getCurrentPageNumber($page, $pagesCount);
        $ads = $this->getAdsPage($page, $limit, $pagesCount);
        return array(
            'ads' => $ads,
            'paginator' => array(
                'page' => $page,
                'pagesCount' => $pagesCount)
        );
    }

    /* Save ad.
     *
     * @access public
     * @param array $ad Ad data
     * @retun mixed Result
     */
    public function saveAd($ad)
    {
        if (isset($ad['idad'])
            && ($ad['idad'] != '')
            && ctype_digit((string)$ad['idad'])) {
            $id = $ad['idad'];
            unset($ad['idad']);
            return $this->_db->update('ads', $ad, array('idad' => $id));
        } else {
            return $this->_db->insert('ads', $ad);
        }

    }

    /**
     * Delete single ad data.
     *
     * @access public
     * @param integer $idad Record Id
     * @return array Result
     */
    public function deleteAd($idad)
    {
        try {
            if (($idad != '') && ctype_digit((string)$idad) ) {
                $query = '
                  DELETE
                    *
                  FROM
                    ads
                  WHERE
                    idad= ?
                ';
                return $this->_db->delete('ads', array('idad' => $idad));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Add single ad data.
     *
     * @access public
     * @param integer $idad Record Id
     * @param string $ad_name Title of an ad
     * @param string $ad_contence Contence of an ad
     * @return array Result
     */
    public function addAd($idad, $ad_name, $ad_contence)
    {
        try {
            if (($idad != '') && ctype_digit((string)$idad)
                && ($$ad_name != '') && ctype_digit((string)$$ad_name)
                && ($ad_contence != '') && ctype_digit((string)$ad_contence)) {
                $query = '
                  INSERT INTO
                    `ads` (`idad`, `ad_name`, `ad_contence`)
                  VALUES
                    (' . $idad . ', ' . $ad_name . ', ' . $ad_contence . ');
                ';
                return $this->_db->fetchAssoc($query, array((int)$idad));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * Gets all categories.
     *
     * @access public
     * @return array Result
     */
    public function getAllCategories()
    {
        try {
            $query = '
              SELECT
                idcategory, category_name
              FROM
                ads_categories
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Check if ad id exists
     *
     * @param $idad id comment
     *
     * @access public
     * @return bool true if exists.
     */
    public function checkAdsId($idAd)
    {
        $query = '
          SELECT
            *
          FROM
            ads
          WHERE
            idad=?';
        $result = $this->_db->fetchAll($query, array($idAd));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
