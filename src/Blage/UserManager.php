<?php

namespace Blage;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
/**
 * Description of UserManager
 *
 * @author srohweder
 */
class UserManager
{

    /**
     *
     * @var Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;
    /**
     *
     * @var Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(Connection $db, Session $session)
    {
        $this->session = $session;
        $this->db = $db;
    }

    /**
     * check for existing and logged in user
     *
     * @param string $username
     * @return bool logged in or not
     */
    public function checkUser($username)
    {
        $sql = "SELECT id FROM user WHERE username = :name";
        $userdata = $this->db->fetchAssoc($sql, array('name' => $username));
        if($userdata !== false && count($userdata) == 1){
            $this->session->set('loggedin', true);
            $this->session->set('username', $username);
            $this->session->set('userId', $userdata['id']);

            return true;
        }else{
            try{
                $this->db->insert('user', array('username' => $username));
                $userdata = $this->db->fetchAssoc($sql, array('name' => $username));
                $this->session->set('loggedin', true);
                $this->session->set('username', $username);
                $this->session->set('userId', $userdata['id']);

                return true;
            }catch(\Exception $e){
                $this->session->set('loggedin', null);

                return false;
            }
        }
    }

    public function check()
    {
        $ret = $this->session->get('loggedin');
        return $ret;
    }
}
