<?php
/**
 * Photos model.
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
 * Class PhotosModel
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 */

class PhotosModel
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
     * Get all photos
     *
     * @access public
     * @return Array array with information about photo
     */
    public function getPhotos()
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_photos
              LEFT JOIN
                ads
              ON
              ad_photos.idad = ads.idad
            ';

            return $this->_db->fetchAll($query);
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Get photo by Ad id
     *
     * @param $idad Ad id
     *
     * @access public
     * @return array Ad array with photos
     */
    public function getPhotosByAd($idad)
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_photos
              WHERE
                idad = ?
            ';
            return $this->_db->fetchAll($query, array($idad));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Get photo by name
     *
     * @param $name photo name
     *
     * @access public
     * @return mixed
     */
    public function getPhotoByName($name)
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_photos
              WHERE
                photo_name=?
            ';
            return $this->_db->fetchAssoc($query, array($name));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Save photo
     *
     * @param $name new photo name
     * @param date array with information about photo
     *
     * @access public
     * @return void
     */
    public function savePhoto($name, $data)
    {
        try {
            $query = '
              INSERT INTO
                `ad_photos` (`photo_name`, `photo_alt`, `idad`, `iduser`)
              VALUES
                (?,?,?,?)
            ';
            $this->_db->executeQuery(
                $query,
                array(
                    $name,
                    $data['photo_alt'],
                    $data['idad'],
                    $data['iduser'],
                )
            );
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Remove photo
     *
     * @param $name photo name
     *
     * @access public
     * @return array Result
     */
    public function removePhoto($name)
    {
        try {
            $query = '
              DELETE FROM
                `ad_photos`
              WHERE
                photo_name = ?
            ';
            $this->_db->executeQuery($query, array($name));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Create new name photo
     *
     * @param $name original name
     *
     * @access public
     * @return string photo name
     */
    public function createName($name)
    {
        $newName = '';
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $newName = $this->_randomString(32) . '.' . $ext;
        while (!$this->_isUniqueName($newName)) {
            $newName = $this->_randomString(32) . '.' . $ext;
        }
        return $newName;
    }

    /**
     * Check if photo name exists
     *
     * @param $name photo name
     *
     * @access public
     * @return bool true if exists
     */
    public function checkPhotoName($name)
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_photos
              WHERE
                photo_name=?
            ';
            $result = $this->_db->fetchAll($query, array($name));
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Check if name id unique
     *
     * @param $name
     *
     * @access public
     * @return bool
     */
    protected function _isUniqueName($name)
    {
        try {
            $query = '
              SELECT COUNT(*)
              AS
                files_count
              FROM
                ad_photos
              WHERE
                photo_name = ?
            ';
            $result = $this->_db->fetchAssoc($query, array($name));
            return !$result['files_count'];
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }


    public function getPhotosMod($idModerator = null)
    {
        $select = 'SELECT * FROM ad_photos';
        if ($idModerator) {
            $select .= ' LEFT JOIN
                            ads
                        ON
                            ad_photos.idad = ads.idad
                        LEFT JOIN
                            ad_moderator_category
                        ON
                            ads.idcategory = ad_moderator_category.idcategory
                        WHERE
                            ad_moderator_category.iduser  = ? ';
            $result = $this->_db->fetchALl($select, array($idModerator));

        } else {
            $result = $this->_db->fetchALl($select);
        }
        return $result;
    }



    public function getMod($iduser)
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_moderator_category
              WHERE
                iduser=?
            ';

            return $this->_db->fetchAssoc($query, array((int)$iduser));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }



    public function getPhotosUser($iduser)
    {
        $query = 'SELECT * FROM ad_photos WHERE iduser=?';
        return $this->_db->fetchAll($query, array($iduser));
    }


    /**
     * Get random string
     *
     * @param integer $length number of how long shout be photo name
     *
     * @access protected
     * @return string file name
     */
    protected function _randomString($length)
    {
        $string = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $length; $i++) {
            $string .= $keys[array_rand($keys)];
        }
        return $string;
    }
}
