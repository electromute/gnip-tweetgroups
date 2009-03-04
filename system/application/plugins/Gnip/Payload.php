<?php
class Services_Gnip_Payload {
    public $title;
    public $body;
    public $mediaURL;
    public $mediaMetaURL;
    public $raw;


    /**
     * Creates a Services_Gnip_Payload object.
     * 
     * @param string $raw required string representation of the dataset
     * @param string $title optional
     * @param string $body optional
     * @param array  $mediaURL with optional atrributes. 2d array can be sent. optional
     * optional attributes for mediaURL are:
     * height
     * width
     * duration
     * mimeType
     * type
     */
    public function __construct($raw, $title = null, $body = null, $mediaURL = null) {
        $this->title = ($title != null) ? trim($title) : null;
        $this->body = ($body != null) ? trim($body) : null;
        $this->mediaURL = (is_array($mediaURL)) ? $mediaURL : null;
        $this->raw = base64_encode(gzencode($raw));
    }


    /**
     * Decodes a base64 and gzipped representation of the raw data 
     * from a publisher.
     * 
     * @return object Services_Gnip_Payload
     */
    public function decodedRaw() {
        if($this->raw != null)
            return Services_Gnip_Payload::gzdecode(base64_decode($this->raw));
        return $this->raw;
    }

    /**
     * Converts the place to properly formatted XML..
     * 
     * @param object $doc DOMDocument object
     * @param object $root DOMDocument root
     */
    public function toXML($doc, $root) {
        $payload = $doc->createElement('payload');
        if ($this->title != null){
            $payload->appendChild($doc->createElement('title', $this->title));
        }
        if ($this->body != null) {
            $payload->appendChild($doc->createElement('body', $this->body));
        }
        if ($this->mediaURL != null){
            $doc->appendChildren($doc, $payload, "mediaURL", $this->mediaURL);
        }
        $payload->appendChild($doc->createElement('raw', $this->raw));
        if($payload->hasChildNodes()){
            $root->appendChild($payload);
        }
    }


    /**
     * Converts XML formatted payload to Services_Gnip_Payload object.
     * 
     * @param string $xml XML data
     * @return object Services_Gnip_Payload
     */
    public static function fromXML($xml) {
        $xml_element = new SimpleXMLElement($xml);
        $found_title = strlen(strval($xml_element->title)) ? strval($xml_element->title) : null;
        $found_body = strlen(strval($xml_element->body)) ? strval($xml_element->body) : null;
        $result = $xml_element->xpath('mediaURL');
        $nodes_num = count($result);
        $found_mediaURL = array();
        
        if (!empty($result)){
        if($nodes_num >= 1){
            foreach ($result as $key => $val){
                if(is_object($val)){
                    $media_stuff['mediaURL'] = strval($val[0]);
                    foreach($val[0]->attributes() as $attr_name => $attr_val) {
                        if (strlen (strval($attr_val))){
                            $media_stuff[$attr_name] = strval($attr_val);
                        }
                    }
                    $found_mediaURL[] = $media_stuff;
                }
            }
        } else {
            $found_mediaURL = null;
        }
        } else {
            $found_mediaURL = null;
        }
        $found_raw = Services_Gnip_Payload::gzdecode(base64_decode($xml_element->raw));
        
        return new Services_Gnip_Payload($found_raw, $found_title, $found_body, $found_mediaURL);
    }


    /**
     * Uncompresses Gzipped data and returns the resulting String.
     * 
     * @return string uncompressed data
     */
    private static function gzdecode ($data)
    {
       $flags = ord(substr($data, 3, 1));
       $headerlen = 10;
       $extralen = 0;
       $filenamelen = 0;
       if ($flags & 4) {
           $extralen = unpack('v' ,substr($data, 10, 2));
           $extralen = $extralen[1];
           $headerlen += 2 + $extralen;
       }
       if ($flags & 8) // Filename
           $headerlen = strpos($data, chr(0), $headerlen) + 1;
       if ($flags & 16) // Comment
           $headerlen = strpos($data, chr(0), $headerlen) + 1;
       if ($flags & 2) // CRC at end of file
           $headerlen += 2;
       $unpacked = gzinflate(substr($data, $headerlen));
       if ($unpacked === FALSE)
             $unpacked = $data;
       return $unpacked;
    }
}
?>