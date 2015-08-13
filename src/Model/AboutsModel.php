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
     * @param $name About name
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
        return $this->_db->fetchall($sql);
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
                    $data['idabout'],
                    $data['email'],
                    $data['content']
                )
            );
    }

    /* Save about.
    *
    * @access public
    * @param array $ad About data
    * @retun mixed Result
    */
    public function saveAbout($about)
    {
        if (isset($about['idabout'])
            && ($about['idabout'] != '')
            && ctype_digit((string)$about['idabout'])) {
            $id = $about['idabout'];
            unset($about['idabout']);
            return $this->_db->update('about', $about, array('idabout' => $id));
        } else {
            return $this->_db->insert('about', $about);
        }

    }
}