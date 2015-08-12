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
    public function getAbout($idabout)
    {
        $sql = 'SELECT
                  *
                FROM
                  ad_about
                WHERE
                  idabout = 1';
        return $this->_db->fetchall($sql, array($idabout));
    }
    /**
     * Get about id
     *
     * @param $name
     *
     * @access public
     * @return Array Associative array with id about
     */
    public function getAboutId($name)
    {
        $sql = 'SELECT * FROM ad_about WHERE idabout = 1';
        return $this->_db->fetchAssoc($sql, array($name));
    }

    /**
     * Update about
     *
     * @param array $data Array with about information
     *
     * @access public
     * @return bool true if updeted
     */
    public function updateAbout($data)
    {
        $aboutValues = 'SELECT
                          *
                        FROM
                          ad_about
                        WHERE
                          idabout = 1';
        $attriubutesNames = $this->_db
            ->fetchAll($aboutValues, array($data['idabout']));
        foreach ($attriubutesNames as $attribute) {
            foreach ($data as $key => $value) {
                if ($attribute['title'] == $key) {
                    $sql = 'UPDATE ad_about
                        SET
                          content = ?
                        WHERE
                          idabout = ?';
                    $this->_db->executeQuery(
                        $sql, array(
                            $value,
                            $data['idabout'],
                            $attribute['idattribute']
                        )
                    );
                }
            }
        }
        return true;
    }
}