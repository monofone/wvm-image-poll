<?php

namespace Blage;

use Doctrine\DBAL\Connection;

/**
 * Description of ImageService
 *
 * @author srohweder
 */
class ImageService
{

    /**
     *
     * @var Doctrine\DBAL\Connection
     */
    protected $db;
    protected $settings;

    public function __construct(Connection $db, $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    public function isImageVotedByUser($imageid, $userid)
    {
        $sql = "SELECT COUNT(*) as voted FROM images_voted_by_user WHERE userid = :userid AND imageid = :imageid";
        $result = $this->db->fetchAssoc($sql, array('imageid' => $imageid, 'userid' => $userid));
        if ($result !== false) {
            return (bool) $result['voted'];
        } else {
            return false;
        }
    }

    public function getCountedVotes($userid)
    {
        $sql = "SELECT COUNT(*) as voted FROM images_voted_by_user WHERE userid = :userid";
        $result = $this->db->fetchAssoc($sql, array('userid' => $userid));

        return (int) $result['voted'];
    }

    public function voteImage($imageid, $userid)
    {
        if (!$this->isImageVotedByUser($imageid, $userid) && $this->getCountedVotes($userid) < $this->settings['max_votes']) {
            try {
                $this->db->insert('images_voted_by_user', array('imageid' => $imageid, 'userid' => $userid));
            } catch (\Exception $e) {

                return false;
            }

            return true;
        }

        return false;
    }
}
