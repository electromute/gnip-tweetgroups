<?
class Twitter_Profile {
    static private $proURL = "http://twitter.com/users/show";
    public $profileXML = '';
    public $username = '';
    
    public function __construct($username) {
        $this->username = $username;
        $this->profileXML = Twitter_Profile::$proURL."/".$this->username.".xml";
    }
    
    function getUserPicURL(){
        $xml = @file_get_contents($this->profileXML);
        if($xml) {
            $xml_element = new SimpleXMLElement($xml);
            $userPicURL = strlen(strval($xml_element->profile_image_url)) ? strval($xml_element->profile_image_url) : null;
            return $userPicURL;
        } else {
            return FALSE;
        }
    }
    
    function getUserProtectedStatus(){
        $xml = @file_get_contents($this->profileXML);
        if($xml) {
            $xml_element = new SimpleXMLElement($xml);
            $status = strlen(strval($xml_element->protected)) ? strval($xml_element->protected) : null;
            return $status;
        } else {
            return FALSE;
        }
    }
    
    function getUserXML(){
        $xml = @file_get_contents($this->profileXML);
        if($xml) {
            return $xml;
        } else {
            return FALSE;
        }
    }

}

?>