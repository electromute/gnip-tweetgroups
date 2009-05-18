<?php

class Home extends Controller {


    function Home() {
        parent::Controller();
        $this->load->model('Gnipdata_model');
        $this->load->helper('timestamp');
    }

    function index() {
        if (stripos($_SERVER['REQUEST_METHOD'], 'HEAD') !== FALSE) {
            log_message('debug', 'Data Missing');
            exit();
        } else {
            log_message('debug', 'Data was posted from Gnip, attempting to process.');
            $xml = file_get_contents("php://input");
            if($xml == FALSE){
                log_message('debug', 'There was no data to process, exiting.');
                exit();
            } else {
                log_message('debug', 'Data was properly received, commence processing.');
                $this->addTweets($xml);
                $status = $this->Gnipdata_model->insertBlock($xml);
            }
        }
    }


    /*
    * Non view functions
    */

    function addTweets($xml){
        $xml_element = new SimpleXMLElement($xml);
        //prep data for db
        //note that for normal applications, full data for twitter is not available
        //therefore, the URL must be polled to get the full data.
        foreach ($xml_element as $key){
        $data = array(
            'username' => strval($key->actor),
            'tweet' => strval($key->payload->body),
            'URL' => strval($key->destinationURL),
            'time' => strval($key->at),
            'client' => strval($key->source),
            'replyto' => strval($key->regardingURL),
            'timeadded' => timestamp()
            );
        $status = $this->Gnipdata_model->addTweet($data);
        log_message('debug', 'Added tweet by '.strval($key->actor) . ' at ' . strval($key->at));
        }
    }

    function testXML(){
        //a little debugging function if you need it.
        $this->load->helper('debug');
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><activities publisher="twitter"><activity><at>2009-02-03T23:01:40.000Z</at><action>notice</action><activityID>tag:twitter.com,2007:statuses/1174518353</activityID><URL>http://twitter.com/statuses/show/1174518353.xml</URL><source>TwitterBerry</source><payload><body>Hello, this is a test tweet</body></payload><place><featurename>Richmond, VA</featurename></place><actor uid="19973270" metaURL="http://twitter.com/gregsamuels">electromute</actor><destinationURL>http://twitter.com/gregsamuels/status/1174518353</destinationURL><regardingURL>http://twitter.com/gregsamuels/status/1174518353</regardingURL></activity><activity><at>2009-18-03T23:01:40.000Z</at><action>notice</action><activityID>tag:twitter.com,2007:statuses/1174518353</activityID><URL>http://twitter.com/statuses/show/1174518353.xml</URL><source>web</source><payload><body>Second tweet</body></payload><place><featurename>Boulder, CO</featurename></place><actor uid="19973270" metaURL="http://twitter.com/gregsamuels">gregsamuels</actor><destinationURL>http://twitter.com/gregsamuels/status/1174518353</destinationURL><regardingURL>http://twitter.com/gregsamuels/status/1174518353</regardingURL></activity></activities>';
        $xml_element = new SimpleXMLElement($xml);
        //debug($xml_element);
        foreach ($xml_element as $key){
            $tweettime = strval($key->at);
            $location = strval($key->place->featurename);
            echo $location;
        }
        //print_r(strval($xml_element->activity->payload->body));
        exit;
    }

}

?>