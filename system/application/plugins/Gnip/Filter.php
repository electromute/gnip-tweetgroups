<?php
class Services_Gnip_Filter {
    public $name;
    public $fullData;
    public $postURL;
    public $rules;
    

    /**
     * Creates a Services_Gnip_Filter object.
     * 
     * @param string $name
     * @param boolean $fullData default is false
     * @param string $postURL default is empty string
     * @param array $rules array of Services_Gnip_Rule objects, default is empty
     */
    public function __construct($name, $fullData = 'false', $postURL = '', $rules = array()) {
        $this->name = trim($name);
        $this->fullData = trim($fullData);
        $this->postURL = trim($postURL);
        $this->rules = $rules;
    }


    /**
     * Adds one or more rules to a Services_Gnip_Filter object.
     * 
     * @param array $rules
     */
    public function addRules($rules) {
        foreach ((array) $rules as $rule) {
            $this->rules[] = $rule;
        }
    }


    /**
     * Removes one or more rules from a Services_Gnip_Filter object.
     * 
     * @param array $rules
     */
    public function removeRules($rules) {
        foreach ((array) $rules as $rule) {
            $key = array_search($rule, $this->rules);
            unset($this->rules[$key]);
        }
    }


    /**
     * Converts the filter to properly formatted XML.
     * 
     * @return XML formatted filter data
     */
    public function toXML() {
        $xml = new GnipSimpleXMLElement("<filter/>");
        $xml->addAttribute('name', $this->name);
        $xml->addAttribute('fullData', $this->fullData);
        $xml->addOptionalChild('postURL', $this->postURL);
        foreach($this->rules as $rule){
            $rule_node = $xml->addChild('rule', $rule->value);
            $rule_node->addAttribute('type', $rule->type);
        }
        return trim($xml->asXML());
    }


    /**
     * Converts XML formatted filter to Services_Gnip_Filter object.
     * 
     * @param string $xml XML data
     * @return object Services_Gnip_Filter
     */
    public function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        $f = new Services_Gnip_Filter($xml_element["name"], $xml_element["fullData"]);
        $f->postURL = strval($xml_element->postURL);
        foreach($xml_element->rule as $rule_node){
            $f->rules[] = Services_Gnip_Rule::fromXML($rule_node->asXML());
        }
        return $f;
    }


    /**
     * Returns the URL to send create filter request to belonging
     * to a given publisher.
     * 
     * @param string $publisherName name of publisher
     * @return string URL
     */
    public function getCreateUrl($publisherName) {
        return "/publishers/" . $publisherName . "/filters.xml";
    }


    /**
     * Returns the URL of the filter object belonging to a given publisher.
     *
     * @param string $publisherName name of publisher
     * @return string URL
     *
     */
    public function getUrl($publisherName) {
        return "/publishers/".$publisherName."/filters/".$this->name.".xml";
    }

    /**
     * Returns the URL of the filter activity bucket for this filter on the given publisher.
     * 
     * @param string $publisherName name of the publisher
     * @param string $when timestamp of bucket
     * @return string URL
     */
    public function getActivityUrl($publisherName, $when) {
        return "/publishers/".$publisherName."/filters/".$this->name."/activity/".$when.".xml";
    }

    /**
     * Returns the URL of the notification activity bucket for this filter on the given publisher.
     * 
     * @param string $when timestamp of bucket
     * @param string $publisherName name of the publisher
     * @return string URL
     */
    public function getNotificationUrl($publisherName, $when) {
        return "/publishers/".$publisherName. "/filters/".$this->name."/notification/".$when.".xml";
    }


    /**
     * Returns the URL of filter list for a publisher you have created
     * filters on.
     * 
     * @param string $publisherName name of publisher
     * @return string URL
     */
    public function getIndexUrl($publisherName) {
        return "/publishers/" . $publisherName ."/filters.xml";
    }

}
?>