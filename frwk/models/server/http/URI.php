<?php
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
define('URI_NO_PATH', 'URI_NO_PATH');
define('URI_SCHEME_DELIMITER', '://');
define('URI_HTTP_SCHEME', 'http');
define('URI_HTTP_SSL_SCHEME', 'https');
define('URI_FTP_SCHEME', 'ftp');
define('URI_SERVER_DELIMITER', '@');
define('URI_PATH_DELIMITER', '/');
define('URI_QUERY_DELIMITER', '?');
define('URI_PARAMETER_DELIMITER', '&');
define('URI_PARAMETER_ASSOCIATOR', '=');
Render::Model('data/Data.php');


  ///TODO: cleanse URI from anamolies
class URI extends Data
{
	var $path = URI_NO_PATH;
	var $query = '';
	var $queryArray = array();
	var $scheme;
	var $uri = '';
    var $base = '';
    var $extension = '';

	function URI ($uri = '')
	{
		if ($uri == '')
		{
			$uri = $_SERVER["REQUEST_URI"];
		}
		$uri = preg_replace("/(.*?)\?/", "?", $uri);
		//error_log("f(x)URI  uri: ". $uri);
		$this->setURI($uri);
		$this->initParseURI($uri);
        $path_parts = pathinfo($uri);
        $base       = $path_parts['basename'];
	$extension  = $path_parts['extension'];
		$this->base = str_replace(".".$extension,"",$base);
		$this->extension = $extension;
		//$this->initRequestDataObj();
	}

    function getExtension()
    {
        return $this->extension;
    }

    function getBase()
    {
        return $this->base;
    }

	function setURI ($uri)
	{
		$this->uri = $uri;
	}


	function getURI ()
	{
		return $this->uri;
	}


	function setScheme ($scheme)
	{
		$this->scheme = $scheme;
	}


	function setQuery ($query)
	{
		$this->query = $query;
	}


	function setPath ($path)
	{
		$this->path = $path;
	}


	function getPath ()
	{
		return $this->path;
	}


	function getQuery ()
	{
		return $this->query;
	}

	function setQueryArray ($array)
	{
		$this->queryArray = $array;
	}


	function getQueryArray ()
	{
		return $this->queryArray;
	}


	function initParseURI ($uri)
	{
		//echo "f(x)initParseURI uri: ". $uri;
		$uri = (isset($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : URI_NO_PATH;
		if ($uri !== URI_NO_PATH)
		{
			$this->setPath($this->getPathByURI($uri));
			$this->setQuery($this->getQueryByURI($uri));
			$this->setScheme($this->getSchemeByURI($uri));
			$this->setQueryArray($this->getQueryArrayByURI($uri));
		}
	}


	function getPathByURI($uri)
	{
		$return = (isset($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : URI_NO_PATH;
		if ($uri)
		{
			$return = array();
			preg_match('/^(\/[^\?#]*)/', $uri, $return);
		}
		//error_log("file: ". $return);
		return $return;
	}


	function getQueryByURI($uri)
	{
		$return = false;
		if ($uri !== '')
		{
			if (strpos($uri, '?') !== false)
			{
				if (preg_match('/^(\/[^\?#]*)?\?([^#]*)/', $uri, $return))
				{
					$return = $return[2];
				}
			}
// 			else
// 			{
// 				preg_match('/^()([^#]+)/', $uri, $return);
// 			}

		}

		return $return;
	}


	function URIKeyExist($value)
	{
		$return = false;
		//echo "this->getQueryArray(): "; print_r($this->getQueryArray());
		if (array_key_exists($value, $this->getQueryArray()))
		{
			//error_log("val:  $value was found in this uri: ".$this->uri."");
			$return = true;
		}
		return $return;
	}


	function getURIQueryValueByKey($key)
	{
		$return = "";
		if ($key !== '')
		{
			$return = $this->getQueryArray();
			$return = $return[$key];
		}
		return $return;
	}


	function getSchemeByURI($uri)
	{
		$scheme = array();
		if ($uri !== '')
		{
			preg_match('/^([^:]+):\/\//', $uri, $scheme);
			$scheme = $scheme[1];
		}
		return $scheme;
	}


	function getQueryArrayByURI($uriString)
	{
		$parameterArray = array();

		if (strpos($uriString, URI_QUERY_DELIMITER) !== false)
		{
			$uriString = $this->getQuery();
		}

		if ($uriString !== '')
		{
			$queryParts = explode(URI_PARAMETER_DELIMITER, $uriString);
			foreach ($queryParts as $parameterSet)
			{
				$parameterParts = explode(URI_PARAMETER_ASSOCIATOR, $parameterSet);
				if (count($parameterParts) == 2)
				{
					list($key, $value) = $parameterParts;
				}
				else
				{
					$key = $parameterParts[0];
					$value = '';
				}
				//error_log("URI key: $key ----- val: $val");
				$parameterArray[$key] = $value;
			}
		}

		return $parameterArray;
	}


	function URIEncodeVariable ($var)
	{
		return urlencode(base64_encode(gzcompress(serialize($var))));
	}


	function URIDecodeVariable ($string)
	{
		return unserialize(gzuncompress(base64_decode($string)));
	}
}

?>
