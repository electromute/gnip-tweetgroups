<?php
/**
 *  Extended to provide commmon XML serialization/customizations for Gnip
 * 
 */
class GnipDOMDocument extends DOMDocument
{

    /**
     * Convert object to XML.
     * 
     * @param object $doc DOMDocument
     * @param object $parent DOMElement
     * @param string $nodeName name of the node you want to convert to
     * @param array $arrayToConvert array (or 2d array) you are trying to convert
     *
     * Adds one or more properly formatted xml nodes to the parent
     */ 
    public function appendChildren($doc, $parent, $nodeName, $arrayToConvert){
        if (array_key_exists($nodeName, $arrayToConvert)){
            //one object was sent.
            $elem = $doc->createElement($nodeName, $arrayToConvert[$nodeName]);
            $parent->appendChild($elem);
            foreach ($arrayToConvert as $key => $val){
                if($key != $nodeName){
                    $attrName = $doc->createAttribute($key);
                    $elem->appendChild($attrName);
                    $attrVal = $doc->createTextNode($val);
                    $attrName->appendChild($attrVal); 
                }
            }
        } else { 
            //array of objects was sent
            foreach ($arrayToConvert as $key => $val){
                $elem = $doc->createElement($nodeName, $val[$nodeName]);
                $parent->appendChild($elem);
                foreach($val as $attrName => $attrVal){
                    if($attrName != $nodeName){
                        $attribute = $doc->createAttribute($attrName);
                        $elem->appendChild($attribute);
                        $attributevalue = $doc->createTextNode($attrVal);
                        $attribute->appendChild($attributevalue); 
                    }
                }
            }
        }
    }


    /**
     * As XML.
     * 
     * @return full proper xml document
     *
     * Fills in the correct header and footer for a proper XML document.
     */
    public function asXML()
    {
       return str_replace(PHP_EOL,'',str_replace('<?xml version="1.0"?>','', parent::saveXML()));
    }
}
?>