<?php
class Services_Gnip_Rule {
    var $type;
    var $value;


    /**
     * Creates a Services_Gnip_Rule object.
     * 
     * @param string $type
     * @param string $value
     */
    public function __construct($type, $value) {
        $this->type = trim($type);
        $this->value = trim($value);
    }


    /**
     * Converts the rules to properly formatted XML.
     * 
     * @return XML formatted rule data
     */
    public function toXML() {
        $doc = new GnipDOMDocument();
        $ruleRoot = $doc->createElement('rule', $this->value);
        $doc->appendChild($ruleRoot);
        $attrName = $doc->createAttribute('type');
        $ruleRoot->appendChild($attrName);
        $attrVal = $doc->createTextNode($this->type);
        $attrName->appendChild($attrVal);
        return trim($doc->asXML());    
    }


    /**
     * Converts XML formatted rule to Services_Gnip_Rule object.
     * 
     * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule
     */
    public function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        return new Services_Gnip_Rule($xml_element["type"], strval($xml_element));
    }


    /**
     * Returns the URL to send create rule request to belonging
     * to a given filter and publisher.
     * 
     * @param string $publisherName name of publisher
     * @param string $filterName name of filter
     * @return string URL
     */
    public function getCreateUrl($publisherName, $filterName) {
        return "/publishers/" . $publisherName . "/filters/" . $filterName . "/rules.xml";
    }

    /**
     * Returns the URL of a given filter by name belonging to 
     * a given publisher.
     * 
     * @param string $publisherName name of publisher
     * @param string $filterName name of filter
     * @return string URL
     */
    public function getUrl($publisherName, $filterName) {
        return "/publishers/" . $publisherName ."/filters/" . $filterName ."/rules.xml";
    }

}
?>