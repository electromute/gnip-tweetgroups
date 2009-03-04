<?php
class Services_Gnip_Rule_Type {
    var $type;

    /**
     * Creates a Services_Gnip_Rule_Type object.
     * 
     * @param string $type
     */
    public function __construct($type) {
        $this->type = trim($type);
    }


    /**
     * Converts the rule types to properly formatted XML.
     * 
     * @return XML formatted rule type data
     */
    public function toXML() {
        $xml = new GnipSimpleXMLElement("<type/>");
        $xml[0] = $this->type;
        return trim($xml->asXML());
    }


    /**
     * Converts XML formatted rule type to Services_Gnip_Rule_Type object.
     * 
     * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule_Type
     */
    public static function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        return new Services_Gnip_Rule_Type($xml_element[0]);
    }
}
?>