<?php
/**
 * Albums model.
 *
 * @link http://epi.uj.edu.pl
 * @author epi(at)uj(dot)edu(dot)pl
 * @copyright EPI 2015
 */

namespace Model;

use Silex\Application;

class AlbumsModel
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
     * Gets all albums.
     *
     * @access public
     * @return array Result
     */
    public function getAll()
    {
        $query = 'SELECT id, title, artist FROM albums';
        return $this->_db->fetchAll($query);
    }

    /**
     * Gets single album data.
     *
     * @access public
     * @param integer $id Record Id
     * @return array Result
     */
    public function getAlbum($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'SELECT id, title, artist FROM albums WHERE id= ?';
            return $this->_db->fetchAssoc($query, array((int)$id));
        } else {
            return array();
        }
    }

}
