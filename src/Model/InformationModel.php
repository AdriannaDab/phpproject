<?php
/**
 * Information model.
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
 * Class InformationModel
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 */
class InformationModel
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
     * Gets all cities.
     *
     * @access public
     * @return array Result
     */
    public function getAllCities()
    {
        try {
            $query = '
              SELECT
                idcity, city_name
              FROM
                ad_cities
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * Gets all provinces.
     *
     * @access public
     * @return array Result
     */
    public function getAllProvinces()
    {
        try {
            $query = '
              SELECT
                idprovince, province_name
              FROM
                ad_provinces
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
    /**
     * Gets all countries.
     *
     * @access public
     * @return array Result
     */
    public function getAllCountries()
    {
        try {
            $query = '
              SELECT
                idcountry, country_name
              FROM
                ad_countries
            ';
            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}