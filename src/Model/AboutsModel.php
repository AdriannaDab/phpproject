<?php
/**
 * Abouts model.
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
 * Class AboutsModel
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 */
class AboutsModel
{
    /**
     * Database access object.
     *
     * @access protected
     * @var $_db Doctrine\DBAL
     */
    protected $_db;
    /**
     * Constructor
     *
     * @access public
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->_db = $app['db'];
    }
    /**
     * Get About
     *
     *
     * @access public
     * @return array Array with about attributes and values
     */
    public function getAbout()
    {
        $sql = 'SELECT
                  *
                FROM
                  ad_about
                WHERE
                  idabout = 1';
        $result = $this->_db->fetchall($sql);
        return $result[0];
    }

    /**
     * Updates about.
     *
     * @param Array $data Associative array.
     *
     * @access public
     * @return Void
     */
    public function editAbout($data)
    {

        $sql = 'UPDATE
                      ad_about
                    SET
                      firstname = ?, surname = ?, content = ?, email = ?
                    WHERE
                      idabout = 1';
            $this->_db->executeQuery(
                $sql,
                array(
                    $data['firstname'],
                    $data['surname'],
                    $data['content'] ,
                    $data['email']

                )
            );
    }
}
