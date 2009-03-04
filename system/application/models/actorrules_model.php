<?php
class Actorrules_model extends Model {

    var $username = '';
    var $dateadded = '';
    var $active  = '';
    var $admin = '';


    function Actorrules_model()
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
        $data = array(
                       'username' => $this->input->post('username'),
                       'picURL' => $this->getUserPicURL($this->input->post('username')),
                       'active' => 1,
                       'admin' => 0,
                       'dateadded' => timestamp()
                    );

        $this->db->insert('actorRules', $data);
        if ($this->db->affected_rows() >= 1){
            return TRUE;
        } else {
            return FALSE;
        }

    }
    
    function getUserMeta(){
        $query = $this->db->get_where('actorRules', array('active' => 1, 'admin' => 0));
        return ($query->result());
    }
    
    function getAdminMeta(){
        $query = $this->db->get_where('actorRules', array('active' => 1, 'admin' => 1));
        return ($query->result());
    }
    
    function getUserPicURL($username){
        $config =& get_config();
        $twitterpicURL = $config['gnip']['twitterphoto']."/".$username.".xml";
        $xml = file_get_contents($twitterpicURL);
        $xml_element = new SimpleXMLElement($xml);
        $userPic = strlen(strval($xml_element->profile_image_url)) ? strval($xml_element->profile_image_url) : null;
        return $userPic;
    }
}
?>