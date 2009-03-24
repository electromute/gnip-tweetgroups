<?php
require_once 'Gnip/Activity.php';
require_once 'Gnip/Filter.php';
require_once 'Gnip/GnipSimpleXMLElement.php';
require_once 'Gnip/GnipDOMDocument.php';
require_once 'Gnip/Helper.php';
require_once 'Gnip/Publisher.php';
require_once 'Gnip/Rule.php';
require_once 'Gnip/RuleType.php';
require_once 'Gnip/Payload.php';
require_once 'Gnip/Place.php';


class Services_Gnip {

    static public $uri = "https://api-v21.gnip.com";
    public $helper;
    public $debug;


     /**
     * Creates a Services_Gnip object.
     * 
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password) {
        $this->helper = new Services_Gnip_Helper($username, $password, Services_Gnip::$uri);
    }


     /**
     * Creates a publisher on the Gnip server.
     * 
     * @param object $publisher Services_Gnip_Publisher
     * @param string $scope publisher scope (my or gnip) default is my
     * @return string response from the server
     */
    function createPublisher($publisher, $scope="my") {
        $scope = $this->_scopeprep($scope);
        try {
            return $this->helper->doHttpPost($scope . $publisher->getCreateUrl(), $publisher->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling createPublisher on $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Updates an existing publisher on the Gnip server. You must be the publisher
     * owner to update a publisher.
     * 
     * @param object $publisher Services_Gnip_Publisher 
     * @param string $scope publisher scope (my or gnip) default is my
     * @return string response from the server
     */
    function updatePublisher($publisher, $scope="my") {
        $scope = $this->_scopeprep($scope);
        try {
            return $this->helper->doHttpPut($scope . $publisher->getUrl(), $publisher->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling updatePublisher on $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Retrieves a single publisher by name. 
     * 
     * @param string $publisherName name of an existing publisher
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array containing publisher object
     */
    function getPublisher($publisherName, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        $publisher = new Services_Gnip_Publisher($publisherName);
        try {
            $xml = $this->helper->doHttpGet($scope . $publisher->getUrl());
            return $publisher->fromXML($xml);
        } catch (Exception $e) {
            $message = "There was a problem when calling getPublisher on $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Retrieves all publishers from the Gnip servers. 
     * 
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array containing Services_Gnip_Publisher objects
     */
    function getAllPublishers($scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            $xml = $this->helper->doHttpGet($scope . Services_Gnip_Publisher::getIndexUrl());
            $xml = new SimpleXMLElement($xml);
            $publishers = array();
            foreach($xml->children() as $child) {
                $publishers[] = Services_Gnip_Publisher::fromXML($child->asXML());
            }
            return $publishers;
        } catch (Exception $e){
            $message = "There was a problem when calling getAllPublishers(). Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }

    
     /**
     * Publishes data to the Gnip server. You must be the publisher
     * owner to publish data under a publisher.
     * 
     * @param object $publisher Services_Gnip_Publisher
     * @param array $activities array of Services_Gnip_Activity objects
     * @param string $scope publisher scope (my or gnip) default is my
     * @return string response from the server
     */
    function publish($publisher, $activitiesArray, $scope="my") {
        $scope = $this->_scopeprep($scope);        
        $url =  Services_Gnip::$uri . $scope . $publisher->getPublishToUrl();
        $xmlString = $this->_buildChildXml($activitiesArray, 'activities');
        $xmlString = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$xmlString);
        try {
            return $this->helper->doHttpPost($scope . $publisher->getPublishToUrl(), $xmlString);
        } catch (Exception $e){
            $message = "There was a problem when calling publish on publisher $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Retrieves activity data of a given publisher from the Gnip servers. 
     * An optional time parameter can be passed, defaults to current.
     * 
     * @param object $publisher Services_Gnip_Publisher
     * @param long $when optional bucket time, defaults to current
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array containing activity objects
     */    
    function getPublisherActivities($publisher, $when, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            $activities = $this->parseActivities($this->helper->doHttpGet($scope . $publisher->getActivityUrl($when)));
            return $activities;
        } catch (Exception $e){
            $message = "There was a problem when calling getPublisherActivities on publisher $publisher->name. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Retrieves notification data of a given publisher from the Gnip servers. 
     * An optional time parameter can be passed, defaults to current.
     * 
     * @param object $publisher Services_Gnip_Publisher
     * @param long $when optional bucket time, defaults to current
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array containing notification objects
     */	
    function getPublisherNotifications($publisher, $when, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($scope . $publisher->getNotificationUrl($when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getPublisherNotifications on publisher $publisher->name. Status message: ";
           $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Retrieves filtered activity data by publisher from the Gnip servers
     * given a valid filter.
     * An optional time parameter can be passed, defaults to current.
     * 
     * @param string $publisherName name of publisher
     * @param object $filter Services_Gnip_Filter
     * @param long $when optional bucket time, defaults to current
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array of filtered activity objects
     */
    function getFilterActivities($publisherName, $filter, $when, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($scope . $filter->getActivityUrl($publisherName, $when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getFilterActivities on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }



    /**
     * Retrieves filtered notification data by publisher from the Gnip servers
     * given a valid filter.
     * An optional time parameter can be passed, defaults to current.
     * 
     * @param string $publisherName name of publisher
     * @param object $filter Services_Gnip_Filter
     * @param long $when optional bucket time, defaults to current
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array of filtered notification objects
     */
    function getFilterNotifications($publisherName, $filter, $when, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        if ($when != "current") { $when = $this->helper->bucketName($when); }
        try {
            return $this->parseActivities($this->helper->doHttpGet($scope . $filter->getNotificationUrl($publisherName, $when)));
        } catch (Exception $e){
            $message = "There was a problem when calling getFilterNotifications on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        } 
    }



    /**
     * Creates a filter on the Gnip servers for any publisher in the system.
     * 
     * @param string $publisherName publisher name
     * @param object $filter Services_Gnip_Filter
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return string response from the server
     */    
    function createFilter($publisherName, $filter, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try{
            return $this->helper->doHttpPost($scope . $filter->getCreateUrl($publisherName), $filter->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling createFilter on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


    /**
     * Retrieves a given filter from a given publisher. You must be the filter
     * owner to retrieve the filter.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param string $filterName name of filter
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return array of filter objects
     */
    function getFilter($publisherName, $filterName, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        $filter = new Services_Gnip_Filter($filterName);
        try {
            $xml = $this->helper->doHttpGet($scope . $filter->getUrl($publisherName));
            return Services_Gnip_Filter::fromXML($xml);
        } catch (Exception $e){
            $message = "There was a problem when calling getFilter on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


    /**
     * Updates the filter properties on a given filter. You must be the 
     * filter owner to update a filter.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param object $filter Services_Gnip_Filter
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return string response from the server
     */
    function updateFilter($publisherName, $filter, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            return $this->helper->doHttpPut($scope . $filter->getUrl($publisherName), $filter->toXML());
        } catch (Exception $e){
            $message = "There was a problem when calling updateFilter on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


    /**
     * Deletes a given filter from a given publisher.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param object $filter Services_Gnip_Filter
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return string response from the server
     */
    function deleteFilter($publisherName, $filter, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            return $this->helper->doHttpDelete($scope . $filter->getUrl($publisherName));
        } catch (Exception $e){
            $message = "There was a problem when calling deleteFilter on publisher $publisherName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }

    /**
     * Checks to see if a rule exists for a given filter/publisher combination.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param string $filterName name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return boolean true or false
     */
    function ruleExists($publisherName, $filterName, $rule, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            $status = $this->helper->doHttpGet($scope . $rule->getUrl($publisherName, $filterName)."?type=$rule->type&value=$rule->value");
        } catch (Exception $e){
            $message = "There was a problem when calling getRule on publisher $publisherName and filter $filterName. Status message: ";
            error_log($message . $e->getMessage(), 0);
            return 0;
        }
        return 1;
    }

    /**
     * Deletes a rule given an existing valid publisher/filter combination.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param string $filterName name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return string response from the server
     */
    function deleteRule($publisherName, $filterName, $rule, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            return $this->helper->doHttpDelete($scope . $rule->getUrl($publisherName, $filterName)."?type=$rule->type&value=$rule->value");
        } catch (Exception $e){
            $message = "There was a problem when calling deleteRule on publisher $publisherName with filter $filterName on rule type $rule->type and value $rule->value. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


     /**
     * Gets a rule given an existing valid publisher/filter combination. Used
     * mostly for verification that the rule does exist.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param string $filterName name of filter that contains rules
     * @param object $rule Service_Gnip_Rule object
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return Service_Gnip_Rule object
     */
    function getRule($publisherName, $filterName, $rule, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        try {
            $xml = $this->helper->doHttpGet($scope . $rule->getUrl($publisherName, $filterName)."?type=$rule->type&value=$rule->value");
            return Services_Gnip_Rule::fromXML($xml);
        } catch (Exception $e){
            $message = "There was a problem when calling getRule on publisher $publisherName and filter $filterName with rule type $rule->type and value $rule->value. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }

    /**
     * Gets a rule given an existing valid publisher/filter combination.
     * 
     * @param string $publisherName name of the publisher that contains the filter
     * @param string $filterName name of filter that contains rules
     * @param array $rulesArray array of Service_Gnip_Rule objects
     * @param string $scope publisher scope (my or gnip) default is gnip
     * @return string response from the server
     */
    function addBatchRules($publisherName, $filterName, $rulesArray, $scope="gnip") {
        $scope = $this->_scopeprep($scope);
        if(is_array($rulesArray)){
            $rules = $this->_buildChildXml($rulesArray, "rules");
            $rules = str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$rules);
            $url = $rulesArray[0]->getUrl($publisherName, $filterName);
        } else {
            $rules = $rulesArray->toXML();
            $url = $rulesArray->getUrl($publisherName, $filterName);
        }
        try {
            return $this->helper->doHttpPost($scope . $url, $rules);
        } catch (Exception $e){
            $message = "There was a problem when calling addBatchRules on publisher $publisherName with filter $filterName. Status message: ";
            $this->_handleDebug($message, $this->debug, $e);
        }
    }


    /**
     * Gives you the server clock offset so you can better calculate
     * bucket times. The number you get back from this function should 
     * then be added to your time to get the correct time with offset
     * included.
     * 
     * @return long difference
     */
    function getGnipClockOffset(){
        return $this->helper->getGnipClockOffset();
    }


    /**
     * Parses XML data from the server into an array of objects.
     * 
     * @param XML $xml
     * @return array of objects from the request
     */
    function parseActivities($xml) {
        $xml = new SimpleXMLElement($xml);
        $activities = array();
        foreach($xml->children() as $child) {
            $activities[] = Services_Gnip_Activity::fromXML($child->asXML());
        }
        return $activities;
    }


    /**
     * Configuration setting to turn debugging on or off. If true, the debug 
     * messages will display in the browser. Bugs will still be written to the PHP 
     * Logs regardless of setting.
     * 
     * @param boolean $debug
     */
    function setDebugging($debug) {
        $this->debug = $debug;
    }


    /**
     * Configuration setting to turn debugging on or off. If true, the debug 
     * messages will display in the browser. Bugs will still be written to the PHP 
     * Logs regardless of setting.
     * 
     * @param string $message message sent by the function
     * @param boolean $debug debug setting for this object
     * @param string $e exception caught
     */
    private function _handleDebug($message, $debug, $e) {
        if ($debug){
            echo $message . $e->getMessage();
        }
        error_log($message . $e->getMessage(), 0);
    }


    /**
     * Private function to format data into XML.
     * 
     * @param object $batchArray array of objects
     * @param string $name name of object for xml formatting
     * @return string XML formatted batch data
     */
    private function _buildChildXml($batchArray, $name) {
        $batchXML = "";
        foreach($batchArray as $item)
        {
            $batchXML .= $item->toXML();
        }
        $xml = new SimpleXMLElement(utf8_encode('<'.$name.'>' . $batchXML . '</'.$name.'>'));
        $doc = new DOMDocument();
        $doc->loadXML($xml->asXML());
        $doc->schemaValidate(dirname(__FILE__) . '/Gnip/gnip.xsd');
        return $xml->asXML();
    }
    
    
    /**
     * Makes sure the scope is allowed and correctly formatted.
     * Current choices are 'gnip' or 'my'.
     * 
     * @param string $scope name of scope being checked
     * @return string $scope with proper formatting
     */
    private function _scopeprep($scope) {
        return "/" . trim($scope);
    }
}
?>
