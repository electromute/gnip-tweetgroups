<?php
class Services_Gnip_Publisher {
    public $supported_rule_types;
    public $name;

    /**
     * Creates a Services_Gnip_Publisher object. Each publisher must have at 
     * least one rule type.
     * The current supported rule types are:
     * Actor 
     * To
     * Regarding
     * Source
     * Tag
     * 
     * @param string $name
     * @param array $supported_rule_types array of Services_Gnip_Rule_Type objects
     */
    function __construct($name, $supported_rule_types = array()) { 
        $this->name = trim($name);
        $this->supported_rule_types = $supported_rule_types;
    }


    /**
     * Converts the publisher to properly formatted XML.
     * 
     * @return XML formatted publisher data
     */
    function toXML() {
        $xml = new GnipSimpleXMLElement("<publisher/>");
        $xml->addAttribute('name', $this->name);        
        $child = $xml->addChild("supportedRuleTypes");
        foreach($this->supported_rule_types as $rule_type){
            $child->addChild('type', $rule_type->type);
        }
        return trim($xml->asXML());
    }
    


    /**
     * Converts XML formatted publisher to Services_Gnip_Publisher object.
     * 
     * @param string $xml XML data
     * @return object Services_Gnip_Publisher
     */
    function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        if ($xml_element->getName() != 'publisher') { throw new Exception("expected publisher"); }
        $publisher = new Services_Gnip_Publisher($xml_element["name"], array());
        $supportedRuleTypes = $xml_element->supportedRuleTypes;
        if($supportedRuleTypes) {
            foreach($supportedRuleTypes->children() as $rule_type){
                $publisher->supported_rule_types[] = Services_Gnip_Rule_Type::fromXML($rule_type->asXML());
            }
        }
        return $publisher;
    }


    /**
     * Returns the URL to send create publisher requests.
     * 
     * @return string URL
     */
    public static function getCreateUrl() {
        return "/publishers";
    }


     /**
     * Returns the URL of a given publisher by name.
     * 
     * @return string URL
     */
    public function getUrl() {
        return "/publishers/".$this->name . ".xml";
    }
    
    /**
    * Returns the URL of a given publisher by name.
    *
    * @return string URL
    */
    public function getPublishToUrl() {
        return  "/publishers/".$this->name."/activity.xml";
    }


     /**
     * Returns the URL of publisher list.
     * 
     * @return string URL
     */
    public static function getIndexUrl() {
        return "/publishers.xml";
    }
    
    
    /**
     * Returns the URL of activity bucket.
     * 
     * @param string $when timestamp of bucket
     * @return string URL
     */
    public function getActivityUrl($when) {
        return "/publishers/".$this->name."/activity/".$when.".xml";
    }
    
    /**
     * Returns the URL of notification bucket.
     * 
     * @param string $when timestamp of bucket
     * @return string URL
     */
    public function getNotificationUrl($when) {
        return "/publishers/".$this->name."/notification/".$when.".xml";
    }

     /**
     * Add one or more ruleTypes from a Services_Gnip_Publisher object.
     *
     * @param array $ruleTypes
     */
    public function addRuleTypes($ruleTypes) {
        foreach ((array) $ruleTypes as $ruleType){
            $this->supported_rule_types[] = $ruleType;
        }
    }

     /**
     * Removes one or more ruleTypes from a Services_Gnip_Publisher object.
     *
     * @param array $ruleTypes
     */
    public function removeRuleTypes($ruleTypes) {
        foreach ((array) $ruleTypes as $ruleType){
            $key = array_search($ruleType, $this->supported_rule_types);
            unset($this->supported_rule_types[$key]);
        }
    }
}
?>