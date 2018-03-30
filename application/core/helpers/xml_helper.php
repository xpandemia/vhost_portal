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
	public static function loadXml($server, $file, $filter = null, $username = null, $password = null)
	{
		// get file
		$context = stream_context_create(array(
	            'http' => array(
	                'method' => "GET",
	                'header'  => "Authorization: Basic " . base64_encode("$username:$password")
	            )
	        )
	    );
	    if (empty($filter)) {
			$uri = 'http://'.$server.'/'.urlencode($file);
		} else {
			$uri = 'http://'.$server.'/'.urlencode($file).'?$filter='.$filter;
		}
	    $data = file_get_contents($uri, false, $context);
	    // get xml
	    $xml = simplexml_load_string($data);
	    if ($xml === false) {
	        echo "Failed loading XML: ";
	        foreach(libxml_get_errors() as $error) {
	            echo "<br>", $error->message;
	        }
	        return null;
	    } else {
	        return $xml;
	    }
	}

	/**
     * Gets properties from XML.
     *
     * @return array
     */
	public static function getProperties($xml)
    {
        $properties = [];
        foreach ($xml->entry as $props) {
            $prop = $props->content
                ->children('http://schemas.microsoft.com/ado/2007/08/dataservices/metadata')
                ->properties
                ->children('http://schemas.microsoft.com/ado/2007/08/dataservices');
            $properties[] = $prop;
        }
        return $properties;
    }
}
