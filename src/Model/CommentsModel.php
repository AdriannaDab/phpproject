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
     * @param  integer $id id
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
                idcomment, contence, comment_date, idad, iduser, login
              FROM
                ad_comments
              NATURAL JOIN
                ad_users
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
     * @param Integer $idad
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
     * @param array $comment Comment data
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
            if (($idcomment != '') && ctype_digit((string)$idcomment)) {
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
}
