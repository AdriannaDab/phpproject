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
    public function getComment($idad)
    {
        try {
            if (($idad != '') && ctype_digit((string)$idad)) {
                $query = '
                  SELECT
                    *
                  FROM
                    ad_comments
                  WHERE
                    idcomment = ? LIMIT 1
                ';
                $result = $this->_db->fetchAssoc($query, array((int)$idad));
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
        if (isset($comment['idcomment'])
            && ($comment['idcomment'] != '')
            && ctype_digit((string)$comment['idcomment'])) {
            $id = $comment['idcomment'];
            unset($comment['idcomment']);
            return $this->_db->update('ad_comments', $comment, array('idcomment' => $id));
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
                return $this->_db->delete('ad_comments', array('idcomment' => $idcomment));
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
    public function addComment($data )
    {
        $query = 'INSERT INTO ad_comments
            (contence, comment_date, idad)
            VALUES (?,?,?)';
        $this->_db
            ->executeQuery(
                $query,
                array(
                    $data['contence'],
                    $data['comment_date'],
                    $data['idad'],
                    // $data['iduser']
                )
            );
    }
/*

        try {
            if (($idcomment != '') && ctype_digit((string)$idcomment)
                && ($$contence != '') && ctype_digit((string)$$contence)){
                $query = '
                  INSERT INTO
                    `ad_comments` (`idcomment`, `contence`, `comment_date`, `idad`)
                  VALUES
                    (' . $idcomment . ', ' . $contence . ', ' . $comment_date . ', ' .$idad .');
                ';
                return $this->_db->fetchAssoc($query, array((int)$idcomment));
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }*/

    /**
     * Check if comment id exists
     *
     * @param $idcomment id comment
     *
     * @access public
     * @return bool true if exists.
     */
    public function checkCommentId($id)
    {
        $query = '
          SELECT
            *
          FROM
            ad_comments
          WHERE
            idcomment=?';
        $result = $this->_db->fetchAll($query, array($id));
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}
