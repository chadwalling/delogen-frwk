<?
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
Render::Controller('SQL/ServiceSQL.php');

/**
* @desc The Objects class models any Database table and uses a magic method to create all getter and setter functions for quick and easy integration with any
* database tables. To get access to a specific table you would pass in the table name to the object.
* ex: the table name is users and has X amount of fields
* to get a user model table instance: $user = new Objects('user');
*/
class Objects extends ServiceSQL
{
	var $name = '';
	var $type = FALSE;
    var $fkey = '';
    var $refTable = '';
    var $refkey = ''; 
	public $db = '';

	/**
		Objects Constructor
		@param $db [string] Name of the db.
		@param $name [string] Name of the database table/object;
		@param $fields [string] can override the table fields by setting your own.
		@return
	*/
	public function __construct(
		$name = '',
		$db = DEFAULT_DATABASE,
		$fields = '')
	{
		if ($db == '')
		{
			$this->db = DEFAULT_DATABASE;
		}
		else
		{
			$this->db = $db;
		}	
		
		$this->name = $name;
		parent::__construct();
		$this->setObjectType($name);
		if ($fields == '' && $name !== '')
		{
			$this->buildFields($name, $fields);
		}
	}
	
	/**
		Sets the Objects object type
		@param $name [string] sets the objecttype;
		@return
	*/
	function setObjectType($name)
	{
		if ($name == "texts" && $name !== FALSE)
		{
			$this->type = $name;
		}
	}
	
	/**
		Gets the Objects object type
		@param $name [string] sets the objecttype;
		@return [string]
	*/
	function getObjectType()
	{
		return $this->type;
	}
	
	/**
		Sets the table/object name then build the objects fields based on if the table in the db exists or not
		@param $name [string] sets the objecttype;
		@return [string]
	*/
	function setObject($name)
	{
		$this->name = $name;
		$this->connect();
		$this->buildFields($name, $fields);
	}


	/**
		
		@param $fields [array]
		@return
	*/
// 	function setFields($fields)
// 	{
// 		$this->fields = array();
// 		$this->buildFields($this->name, $fields);
// 	}



	/**
		Gets the Objects object type
		@param $table [string] the table/object name to model
		@param $fields [string] override/manually set the table fields
		@return [objects object]
	*/
	function buildFields($table, $fields)
	{
		$fieldnames = array();  
		if ($table != '')
		{
			if ($fields == '')
			{
				$fields = "*";
			}
			else
			{
				if (!is_array($fields))
				{
					$fields = array($fields);
				}
				$fields = implode(",", $fields);
			}
            $queryStringKey = "show create table ".$this->db.".".$table."";     
            
            $queryString = "select $fields from ".$this->db.".".$table." limit 1"; //TODO: should be a DESC sql call here
			//$queryString = "DESC ".$this->db.".".$table."";
			$resource = mysql_query($queryString, $this->link);            
            $resourceKey = mysql_query($queryStringKey, $this->link);
			if (!$resource)
			{
				error_log('Invalid query: ' . mysql_error().' QUERY: '.$queryString.'');
				die("Contact support");
			}
	
			$numfields = mysql_num_fields($resource);
            $createTable = $row[1];
            preg_match('/FOREIGN KEY \(`(.*)`\) REFERENCES `(.*)` \(`(.*)`\)/', $createTable, $matches);
            
            if (!empty($matches[1])){ $this->fkey = $matches[1]; }
			if (!empty($matches[2])){ $this->refTable = $matches[2]; }
            if (!empty($matches[3])){ $this->refkey = $matches[3]; }

            
            for ($i = 0; $i < $numfields; $i++)
			{
				$fieldnames[$i] = mysql_field_name($resource, $i);
				$fieldTypes[$i] = mysql_field_type($resource, $i);
			}

			$this->addFields($fieldnames);
			$this->addFieldTypes($fieldTypes);           
		}
		return $this;
	}
	
	
	/**   
	 * Magic Methods for handling getters and setters for fields if already set in the data object
	 *
	 * @param string $func
	 * @return mixed
	 */
	public function __call($func, $arg)
	{
		$ret = '';
		$fieldsArray = $this->getFields();
		$prefix = strtolower(substr($func, 0, 3));
		$field = strtolower(substr($func, 3));
		$arg = (!empty($arg[0])) ? $arg[0] : 0;

		if ($prefix == "get" && isset($field) && in_array($field, $fieldsArray))
		{
			switch (strtolower($field))
			{
				case $field:
					$this->sqlStr = '';
					$this->sqlJoin = '';
					$ret = $this->getValues($field, $arg);
					///TODO: fix the indexing issue returns [][]val
					return $ret;
					break;
				default: 
					return $ret;
					break;
			}
		}
		elseif($prefix == "set" && isset($field) && in_array($field, $fieldsArray))
		{
			$this->addValues($field, $arg);
		}
		return $ret;
	}
}
?>
