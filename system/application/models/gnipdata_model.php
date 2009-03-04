<?php
class Gnipdata_model extends Model {

    var $username = '';
    var $dateadded = '';
    var $active  = '';
    var $admin = '';
    var $rawxml = '';

    function Gnipdata_model() {
        parent::Model();
        $this->load->helper('timestamp');
    }
    
    function getTweets($number = 20, $offset = 0) {
    	$this->db->select('
                tweets.username,
                tweets.tweet, 
                tweets.time, 
                tweets.client, 
                tweets.replyto,
                tweets.URL,
                actorRules.picURL
                ');
        $this->db->join('actorRules', 
                'tweets.username = actorRules.username', 
                'left');
        $this->db->order_by('tweets.time', 'desc');
        $this->db->limit($number, $offset);
        $query = $this->db->get('tweets');
        return ($query);
    }
    
    function addTweet($data) {
        if (is_array($data)){
            $this->db->insert('tweets', $data);
            if ($this->db->affected_rows() >= 1){
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    function getMostRecentTweetID() {
        $this->db->select_max('id');
        $query = $this->db->get('tweets');
        $row = $query->row();
        return $row->id;
    }
    
    function insertBlock($xml) {
        $this->rawxml = $xml;
        $this->db->set('raw', $this->rawxml);
        $this->db->insert('rawxml');
    }

}
?>