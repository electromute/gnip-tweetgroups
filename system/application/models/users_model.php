<?php
class Users_model extends Model {

    var $username = '';
    var $dateadded = '';
    var $active  = '';
    var $admin = '';


    function Users_model()
    {
        parent::Model();
        $this->load->helper('timestamp');
    }
    
    function userAlreadyExists()
    {
        $this->db->where("username", $this->input->post('username'));
        $query = $this->db->get("actorRules");
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    

    function insertUser()
    {
        $this->load->plugin('twitterprofile');
        $this->twitprofile = new Twitter_Profile($this->input->post('username'));
        $data = array(
                       'username' => $this->input->post('username'),
                       'picURL' => $this->twitprofile->getUserPicURL(),
                       'active' => 1,
                       'admin' => 0,
                       'dateadded' => timestamp(),
                       'lastupdated' => timestamp()
                    );

        $this->db->insert('actorRules', $data);
        if ($this->db->affected_rows() >= 1){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function updateUserPicURL($username, $newURL) {
        $data = array('picURL' => $newURL,
                        'lastupdated' => timestamp());
        $this->db->where('username', $username);
        $this->db->update('actorRules', $data);
    }
    
    function getAllUsers() {
        $this->db->order_by('admin desc, username asc, active desc');
        $query = $this->db->get("actorRules");
        return ($query->result());
    }
    
    function getUserMeta(){
        $query = $this->db->get_where('actorRules', array('active' => 1, 'admin' => 0));
        return ($query->result());
    }
    
    function getAdminMeta(){
        $query = $this->db->get_where('actorRules', array('active' => 1, 'admin' => 1));
        return ($query->result());
    }
    
    function getUserPictures() {
        $this->db->select('username, picURL');
        $query = $this->db->get_where('actorRules', array('active' => 1));
        return ($query->result());
    }
    
    function updateActiveStatus($username, $statusvalue) {
        $data = array('active' => $statusvalue,
                        'lastupdated' => timestamp());
        $this->db->where('username', $username);
        $this->db->update('actorRules', $data);
        // delete user from admin as well
        $this->db->where('username', $username);
        $this->db->delete('admin');        
    }
    
    function updateAdminStatus($username, $statusvalue) {
        $data = array('admin' => $statusvalue,
                        'lastupdated' => timestamp());
        $this->db->where('username', $username);
        $this->db->update('actorRules', $data);
        if ($statusvalue == 1){
            $verificationcode = md5(md5($username).md5(timestamp()));
            
            $this->db->select('username');
            $query = $this->db->get_where('admin', array('username' => $username));
            if(!$query->result()){
                $data = array('username' => $username,
                    'activationcode' => $verificationcode);
                $this->db->insert('admin', $data);
            }
            return $verificationcode;
        } else {
            // delete user from admin as well
            $this->db->where('username', $username);
            $this->db->delete('admin');
        }
    }
}
?>