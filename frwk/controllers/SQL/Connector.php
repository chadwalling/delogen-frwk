<?
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com

Render::Model('data/Data.php');
$dir = dirname(__FILE__);
require_once($dir.'/Connector.ini');

/**
	Connector Constructor.
	@param $link The table/model field types..
	@param $db The table/model fields.
	return
*/
class Connector extends Data
{
	protected $link = '';
	protected $db = '';
	//private $server = 'localhost';
	//private $user   = 'root';
	//private $pass   = '';


	public function __construct()
	{
		 // let the salt be automatically generated
		//$this->Data(/*$fields, $fieldTypes, $row*/);
		$this->connect();
	}

	protected function connect()
	{
		$link = mysql_pconnect(SERVER, USER, PASS);
		if (!$link)
		{
			error_log('Could not connect: ' . mysql_error());
		}
		$this->db = DB;
		if($this->db){
			if (!mysql_select_db($this->db, $link))
			{
				error_log('Could not select database');
			}
			$this->setLink($link);
		}
	}

	function setLink($link)
	{
		$this->link = $link;
	}

	function getLink()
	{
		return $this->link;
	}


	function setDB($db)
	{
		$this->db = $db;
	}

	function getDB()
	{
		return $this->db;
	}


	function close()
	{
		mysql_close($this->link);
	}

	//      function insert($table, $obj)
	//      {
	//           $SQLobj = new ServiceSQLClass();
	//           print_r($SQLobj); exit;
	//           //print_r($SQLobj->buildInsert($table, $obj)); exit;
	//      }

	function scrubQuery($queryString)
	{
		return preg_replace("/\'/", "\\'", $queryString);
	}
}
?>
