<?php
/**
 * Comments model.
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
 * Class CommentsModel
 *
 * @category Model
 * @package  Model
 * @author   Adrianna Dąbkowska
 * @email    adrianna.dabkowska@uj.edu.pl
 * @link     wierzba.wzks.uj.edu.pl/~13_dabkowska
 * @uses Silex\Application
 * @uses Doctrine\DBAL\DBALException
 */
class CommentsModel
{
    /**
     * Db object.
     *
     * @access protected
     * @var $_db Doctrine\DBAL
     */
    protected $_db;

    /**
     * Object constructor.
     *
     * @access public
     * @param Application $app Silex application object
     */
    public function __construct(Application $app)
    {
        $this->_db = $app['db'];
    }

    /**
     * Get all comments for ad
     *
     * @param $id ad id
     *
     * @access public
     *
     * @return array Comment Array
     */
    public function getCommentsList($id)
    {
        try {
            $query = '
              SELECT
                *
              FROM
                ad_comments
              WHERE
                idad = ?
            ';
            return $this->_db->fetchAll($query, array($id));
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }

    }

    /**
     * Gets one comment.
     *
     * @param Integer $idcomment
     *
     * @access public
     * @return array Associative array with comments
     */
    public function getComment($idcomment)
    {
        try {
            if (($idcomment != '') && ctype_digit((string)$idcomment)) {
                $query = '
                  SELECT
                    *
                  FROM
                    ad_comments
                  WHERE
                    idcomment = ? LIMIT 1
                ';
                $result = $this->_db->fetchAssoc($query, array((int)$idcomment));
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





    /* Save comment.
     *
     * @access public
     * @param array $ad Ad data
     * @retun mixed Result
     */
    public function saveComment($comment)
    {
        if (isset($comment['idad'])
            && ($comment['idad'] != '')
            && ctype_digit((string)$comment['idad'])) {
            $id = $comment['idad'];
            unset($comment['idad']);
            return $this->_db->update('ad_comments', $comment, array('idad' => $id));
        } else {
            return $this->_db->insert('ad_comments', $comment);
        }

    }

    /**
     * Delete single ad data.
     *
     * @access public
     * @param integer $idad Record Id
     * @return array Result
     */
    public function deleteComment($idcomment)
    {
        try {
            if (($idcomment != '') && ctype_digit((string)$idcomment) ) {
                $query = '
                  DELETE
                    *
                  FROM
                    ad_comments
                  WHERE
                    idcomment= ?
                ';
                return $this->_db->delete('comments', array('idcomment' => $idcomment));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";
        }
    }

    /**
     * Add single comment data.
     *
     * @access public
     * @param integer $idad Record Id
     * @param int $comment_date Date of a comment
     * @param string $contence Contence of a comment
     * @param int $iduser User
     * @return array Result
     */
    public function addComment($contence, $comment_date, $idad, $iduser)
    {
        try {
            if (($idad != '') && ctype_digit((string)$idad)
                && ($$contence != '') && ctype_digit((string)$$contence)){
                $query = '
                  INSERT INTO
                    `ad_comments` (`idad`, `contence`, `comment_date`, `iduser`)
                  VALUES
                    (' . $idad . ', ' . $contence . ', ' . $comment_date . ', ' . $iduser . ');
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
     * Check if comment id exists
     *
     * @param $idcomment id comment
     *
     * @access public
     * @return bool true if exists.
     */
    public function checkCommentId($idcomment)
    {
        $query = '
          SELECT
            *
          FROM
            ad_comments
          WHERE
            idcomment=?';
        $result = $this->_db->fetchAll($query, array($idcomment));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}
