<?php
class Services_Gnip_Activity
{
    public $at;
    public $action;
    public $activityID;
    public $URL;
    public $source;
    public $keyword;
    public $place; 
    public $actor;
    public $destinationURL;
    public $tag; 
    public $to;
    public $regardingURL;
    public $payload;


    /**
     * Constructor.
     * Please review gnip.xsd for element and attributes properties and requirements
     * Creates a Services_Gnip_Activity object.
     * Required fields are:
     *   at
     *   action
     * 
     * @param string or DateTime $at Time sent by publisher, required 
     * @param string $action Action of post as sent by the publisher, required
     * @param string $activityID ID sent by the pulbisher, this can be GUID, etc. optional
     * @param string $URL Machine readable direct lookup parsible xml document url. optional
     * @param array $source client used to deliver action (SMS, web, etc.). optional
     *          can contain one or more arrays
     *          example: array(array('source'=>'web'))
     *          example: array(array('source'=>'web'), array('source'=>'sms'))
     * @param array $keyword optional.
     *          can contain one or more arrays
     *          example: array(array('keyword'=>'gnip'))
     *          example: array(array('keyword'=>'gnip'), array('keyword'=>'gnop'))
     * @param array of Services_Gnip_Place objects $place Contains place data. optional
     * @param array $actor Profile page of entity performing action. optional
     *          can contain one or more arrays
     *          example: array(array('actor'=>'joe', 'metaURL'=>'http://place.com', 'uid'=>'1234'))
     * @param array $destinationURL The one URL that best describes this action. 
     *        For instance, a digg submission action would be the digg landing page for that item. 
     *        For a digg action, it would be URL for the actual link being dugg. optional
     *          can contain one or more arrays
     *          example: array(array('destinationURL'=>'http://place.com', 'metaURL'=>'http://place.com/metainfo'))
     * @param array $tag User generated tag for action. optional
     *          can contain one or more arrays
     *          example: array(array('tag'=>'stories', 'metaURL'=>'http://place.com/tags/stories'), array('tag'=>'code', 'metaURL'=>'http://place.com/tags/code'))
     * @param array $to Response to entity. optional
     *          can contain one ore more arrays
     *          example: array(array('to'=>'Sue', 'metaURL'=>'http://place.com/users/sue'))
     * @param array $regardingURL Distinct URL outside the service itself that was used in regards to activity. optional 
     *          can contain one or more arrays
     *          example: array(array('regardingURL'=>'http://theotherplace.com', 'metaURL'=>'http://theotherplace.com/metainfo'))
     * @param array of Services_Gnip_Payload objects $payload Payload objects. optional
     * 
     */
    function __construct($at, $action, $activityID = null, $URL = null, $source = null, $keyword = null, $place = null, $actor = null, $destinationURL = null, $tag = null, $to = null, $regardingURL = null, $payload = null) {
        $this->at = is_string($at) ? new DateTime($at) : $at;
        $this->action = trim($action);
        $this->activityID = ($activityID != null) ? trim($activityID) : null;
        $this->URL = ($URL != null) ? trim($URL) : null;
        $this->source = (is_array($source)) ? $source : null;
        $this->keyword = (is_array($keyword)) ? $keyword : null;
        $this->place = (is_object($place) || is_array($place)) ? $place : null;
        $this->actor = (is_array($actor)) ? $actor : null;
        $this->destinationURL = (is_array($destinationURL)) ? $destinationURL : null;
        $this->tag = (is_array($tag)) ? $tag : null;
        $this->to = (is_array($to)) ? $to : null;
        $this->regardingURL = (is_array($regardingURL)) ? $regardingURL : null;
        $this->payload = (is_object($payload) || is_array($payload)) ? $payload : null;
    }

    
    /**
     * Converts the activity to properly formatted XML.     
     * 
     * @return XML formatted activity data
     */
    public function toXML() {
        $doc = new GnipDOMDocument();
        $root = $doc->createElement('activity');
        $doc->appendChild($root);
        $root->appendChild($doc->createElement('at', $this->at->format(DATE_ATOM)));
        $root->appendChild($doc->createElement('action', $this->action));
        if($this->activityID != null){
            $root->appendChild($doc->createElement('activityID', $this->activityID));
        }
        if($this->URL != null){
            $root->appendChild($doc->createElement('URL', $this->URL));
        }
        if ($this->source != null){
            $doc->appendChildren($doc, $root, "source", $this->source);
        }
        if ($this->keyword != null){
            $doc->appendChildren($doc, $root, "keyword", $this->keyword);
        }
        if($this->place != null) {
            foreach ($this->place as $key => $val){
                $val->toXML($doc, $root);
            }
        }
        if ($this->actor != null){
            $doc->appendChildren($doc, $root, "actor", $this->actor);
        }
        if ($this->destinationURL != null){
            $doc->appendChildren($doc, $root, "destinationURL", $this->destinationURL);
        }
        if ($this->tag != null){
            $doc->appendChildren($doc, $root, "tag", $this->tag);
        }
        if ($this->to != null){
            $doc->appendChildren($doc, $root, "to", $this->to);
        }
        if ($this->regardingURL != null){
            $doc->appendChildren($doc, $root, "regardingURL", $this->regardingURL);
        }
        if($this->payload != null) {
            if(@property_exists($this->payload, "raw")){ 
                $this->payload->toXML($doc, $root);
            }
        }
        return $doc->asXML();
    }
    

    /**
     * Converts XML formatted activity to Services_Gnip_Activity object.    
     * 
     * @param string $xml XML data
     * @return object Services_Gnip_Activity
     */
    public static function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        $found_at = strval($xml_element->at);
        $found_action = strval($xml_element->action);
        $found_activityID = strval($xml_element->activityID);
        $found_URL = strval($xml_element->URL);
        $found_source = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('source'), 'source');
        $found_keyword = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('keyword'), 'keyword');
        
        $place_result = $xml_element->xpath('place');
        if (array_key_exists(0, $place_result)){
            foreach ($place_result as $k=>$v){
                $found_place[] = Services_Gnip_Place::fromXML($v->asXML());
            }
        } else {
            $found_place = null;
        }

        $found_actor = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('actor'), 'actor', array('metaURL', 'uid'));
        $found_destinationURL = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('destinationURL'), 'destinationURL', array('metaURL'));
        $found_tag = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('tag'), 'tag', array('metaURL'));
        $found_to = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('to'), 'to', array('metaURL'));
        $found_regardingURL = Services_Gnip_Activity::getUnboundXML($xml_element->xpath('regardingURL'), 'regardingURL', array('metaURL'));
        
        $payload_result = $xml_element->xpath('payload');
        if (array_key_exists(0, $payload_result)){
            foreach ($payload_result as $k=>$v){
                $found_payload = Services_Gnip_Payload::fromXML($v->asXML());
            }
        } else {
            $found_payload = null;
        }

        return new Services_Gnip_Activity(new DateTime($found_at), $found_action, $found_activityID, $found_URL, $found_source, $found_keyword, $found_place, $found_actor, $found_destinationURL, $found_tag, $found_to, $found_regardingURL, $found_payload);
    }
    
    
    /**
     * Get array(s) from XML format. Converts XML formatted elements to arrays for Services_Gnip_Activity Object.
     * 
     * @param object $result SimpleXML XPath object
     * @param string $nodeName name of your node
     * @param array $arrAttrs optional array of attribute names
     * @return array
     *
     */
    public static function getUnboundXML($result, $node_name, $arr_attrs = null) {
        $nodes_num = count($result);
        $xml_stuff = array();
        if($nodes_num >= 1){
            foreach ($result as $key => $val){
                if(is_object($val)){ 
                    $stuff = array();  
                    $stuff[$node_name] = strval($val[0]);
                    if (is_array($arr_attrs)){
                        foreach ($arr_attrs as $k => $v){
                            if (strlen($val[$v])){
                                $stuff[$v] = strval($val[$v]);
                            }
                        } 
                    }
                    $xml_stuff[] = $stuff;
                }
            }
        } else {
            $xml_stuff = null;
        }
        return $xml_stuff;
    }

}
?>