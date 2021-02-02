<?php

namespace tinyframe\core\helpers;

class XML_Helper
{
    /*
        XML processing
    */
    
    /**
     * Loads XML from URL.
     *
     * @return xml
     */
    public static function loadXml( $server, $file, $filter = NULL, $username = NULL, $password = NULL )
    {
        // get file
        $context = stream_context_create([
                                             'http' => [
                                                 'method' => "GET",
                                                 'header' => "Authorization: Basic ".base64_encode("$username:$password")
                                             ],
                                             'ssl' => [
                                                 "verify_peer" => FALSE,
                                                 "allow_self_signed" => TRUE
                                             ]
                                         ]
        );
        if( empty($filter) ) {
            $uri = $server.'/'.urlencode($file);
        } else {
            $uri = $server.'/'.urlencode($file).'?$filter='.$filter;
        }
        
        $data = file_get_contents($uri, FALSE, $context);
        
        // get xml
        $xml = simplexml_load_string($data);
        if( $xml === FALSE ) {
            echo "<br>Failed loading XML: ";
            foreach( libxml_get_errors() as $error ) {
                echo "<br>", $error->message;
            }
            
            return NULL;
        } else {
            return $xml;
        }
        
        return NULL;
    }
    
    /**
     * Gets properties from XML.
     *
     * @return array
     */
    public static function getProperties( $xml )
    {
        $properties = [];
        foreach( $xml->entry as $props ) {
            $prop         = $props->content
                ->children('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata')
                ->properties
                ->children('http://schemas.microsoft.com/ado/2007/08/dataservices');
            $properties[] = $prop;
        }
        
        return $properties;
    }
}
