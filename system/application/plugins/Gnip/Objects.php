<?php
class Services_Gnip_Source
{
    var $value;


    /**
     * Constructor.
     * 
     * @param string $type
     * 
     * Creates a Services_Gnip_Rule object.
     */
    public function __construct($value)
    {
        $this->value = trim($value);
    }


    /**
     * To XML.
     * 
     * @return XML formatted rule data
     *
     * Converts the rules to properly formatted XML.
     */
    public function toXML()
    {
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
     * From XML.
     * 
     * @param $xml SimpleXMLElelement XML data
     * @return object Services_Gnip_Rule
     *
     * Converts XML formatted rule to Services_Gnip_Rule object.
     */
    public function fromXML(SimpleXMLElement $xml){
        return new Services_Gnip_Rule($xml["type"], strval($xml));
    }


}
?>