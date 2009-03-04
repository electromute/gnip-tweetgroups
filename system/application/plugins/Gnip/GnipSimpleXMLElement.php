<?php
/**
 *  Extended to provide commmon XML serialization/customizations for Gnip
 * 
 */
class GnipSimpleXMLElement extends SimpleXMLElement
{

    /**
     * Add attributes.
     * 
     * @param string $name name of attribute
     * @param string $value value of the attribute
     * @param string $namespace optional namespace
     *
     * Adds an attribute to the parent.
     */
    public function addOptionalAttribute($name, $value, $namespace = null)
    {
        if(! empty($value)) {
          parent::addAttribute($name, $value, $namespace);
        }
    }
    
    /**
     * Add child.
     * 
     * @param string $name name of attribute
     * @param string $value value of the attribute
     * @param string $namespace optional namespace
     *
     * Adds an child to the XML document.
     */
    public function addOptionalChild($name, $value, $namespace = null)
    {
        if(!empty($value)) {
          parent::addChild($name, $value, $namespace);
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
       return str_replace(PHP_EOL,'',str_replace('<?xml version="1.0"?>','', parent::asXML()));
    }
}
?>