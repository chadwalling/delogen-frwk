<?php
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
Render::Controller('SQL/Connector.php');
Render::Controller('SQL/Filters.php');

/**
* @desc This Class runs (services) the actual SQL. It builds queries out of the raw object data or objects that have the Filters Class with SQL filters ready to be
* executed for data insertion or extraction from a database.
*/
class ServiceSQL extends Connector
{
	var $link;
	var $name;
//	var $sqlStr = '';
//	var $joinStr= '';
	//var $limit = '';
	var $filterStr = '';

	/**
		ServiceSQL Constructor
		@param $name   [string] Name of the db.
		@param $link   [string] link id to be db
		@param $sqlStr [string] SQL string to be built
		@return
	*/
	public function __construct()
	{
		parent::__construct();
		$name = $this->name;
		$link = $this->link;
		//$sqlStr  = $this->sqlStr;
	}

	/**
		Creates the sql to insert into the database then pushes/inserts.
		@param $table [string] sets the table to insert into.
		@param $replace [string] will replace as it inserts.
		@return
	*/
	function insert(
		$replace = FALSE)
	{
		$table = $this->name;
		$sql = "INSERT INTO ".$table." (";
		$returnSQL = "SELECT * from ".$table." ";

		if ($replace === TRUE)
		{
			$sql = "REPLACE INTO ".$table." (";
		}
		$fullSQL='';
		$fields = $this->getFieldsArray();
		if (count($fields))
		{
			$sql .= implode($fields, ",");
			$sql .= ")";
		}
		else
		{
			$sql .= ")";
		}
		$sql .= " VALUES ('";

		///ASSUMPTION!!!:will only retrieve last result via the first field in the object
		///TODO: fix this assumption
		for ($i = 0; $i < $this->getCount(); $i++)
		{
			$rows = $this->getRow($i);
			$vals = implode("','", $rows);
			$this->push($sql.$vals."');\n");
		}

		if ($replace)
		{
			//error_log("we are replacing so must pull result from object");
			$id = $rows[$fields[0]];
            if(is_string($id)) $id = "'".$id."'";
			$returnSQL = "select * from ".$table." where ".$fields[0]."=".$id." limit 1";
		}
		else
		{
			//error_log("adding new entry so we must retrieve it for return");
			$returnSQL = "select * from ".$table." order by ".$fields[0]." desc limit 1";
		}
		//error_log("getting last inserted object query: $returnSQL");
		return $this->getLastInsertedObject($returnSQL);
	}


	/**
		Used to insert or push data into table
		@param $sql [string] sets the table to insert into.
		@return
	*/
	function getLastInsertedObject($sql)
	{
		$newObject = new Objects($this->name, $this->db); //builds the object fields for returning last objected added to db

		$resource = mysql_query($sql, $this->link);
		if (!$resource)
		{
			error_log('Invalid query: ' . mysql_error().' QUERY: '.$sql.'');
			die();
		}

		while ($row = mysql_fetch_row($resource))
		{
			$newObject->addRow($row);
		}
		mysql_free_result($resource);
		return $newObject;
	}

	/**
		Used to insert or push data into table
		@param $sql [string] sets the table to insert into.
		@return
	*/
	function push($sql)
	{
		$resource = mysql_query($sql, $this->link);
		if (!$resource)
		{
			error_log('Invalid query: ' . mysql_error().' QUERY: '.$sql.'');
			die('Contact support to have tech check logs.');
			die('Invalid query: ' . mysql_error().' QUERY: '.$sql.'');
		}
	}

	//need to build queries ie: ( table.field =|!=|LIKE|NULL| value ) AND|OR
	function fetch($filter = '')
	{
		$sql = '';
		$keyValPair = array();
		$tableFieldPair = array();
		$i = 0;
		$field = ($field == '') ? $this->fields : $field;
		$field = (is_array($field)) ? $field : array($field);
		$value = (is_array($value)) ? $value : array($value);
		$table = (is_array($table)) ? $table : array($table);
		$filterStrTemp = "";

		$fields = $this->getFields();
		reset($fields);
		foreach($fields as $f)
		{
			$Fs .= $this->db.".".$this->name.".".$f.", ";
			$values = $this->getValues($f);

			if (count($values) > 0 && is_array($values))
			{
				foreach($values as $val)
				{
					$filterStrTemp .= " (".$f."='". mysql_real_escape_string($val)."') &&";
				}
			}
			else if($values != "")
			{
				$filterStrTemp .= " (".$f."='". mysql_real_escape_string($values)."') &&";
			}

		}
		$filterStrTemp = rtrim($filterStrTemp, "&&");
		$Fs = rtrim($Fs, ", ");

		$this->sqlStr .= "SELECT $Fs FROM ".$this->db.".".$this->name;

		$fcount = count($field);
		$countValue = count($value);

		if ($countValue == $fcount)
		{
			$keyValPair = array_combine($field, $value);
		}
		elseif ($fcount < $countValue)
		{
			foreach($field as $curfield)
			{
				foreach($value as $curval)
				{
					error_log( "<debug>key:  $curfield and value: $curval \n\n");
					$keyValPair[$curfield] =  mysql_real_escape_string($curval); //this works because if array key is duplicate it will only make sql key val pair query once
				}
			}
		}
		else
		{
			//error_log("problem with data fields and values.\n");
		}

		if ($filter !== '')
		{
			$filter->buildJoinStr();
			$this->sqlStr .= $filter->getJoinStr();
			if ($filter->getLimit() === '')
			{
				$filter->setLimit();
			}
		}

		if ($filter !== '')
		{
			///TODO: fix this so where clause is in Filters Class this
			///f(X) addFilter() was called so add where clause.
			if ($filter->filterStr != '' )
			{
				$this->sqlStr .= " WHERE ";
			}
			$this->sqlStr .= $filter->getfilterStr();
			$this->sqlStr .= $filter->getLimit();
		}
		else
		{
			if ($filterStrTemp !="")
			{
				$this->sqlStr .= " WHERE ";
				$this->sqlStr .= $filterStrTemp;
			}
			if (count($keyValPair) > 0)
			{
				$this->sqlStr .= " WHERE ";
				foreach($keyValPair as $key=>$val)
				{
					$i++;
					if(isset($val))
					{
						$this->sqlStr .= " (".$key."='". mysql_real_escape_string($val)."')";
						$this->sqlStr .= ($i == $fcount) ? '' : ' and'; //HELP!!! this needs to be fixed
					}
				}
			}
		}

		return $this->runQuery($this->sqlStr);
	}


	//depricated used for running sql manually
	function sql($sql)
	{
		$resource = mysql_query($sql, $this->link);
		if (!$resource)
		{
			error_log('Invalid query: ' . mysql_error().' QUERY: '.$queryString.'');
		}
		return $resource;
	}

	function runQuery($sql)
	{
		//TODO: add the next line and test
		//(mysql_real_escape_string($sql)
		//error_log("\nServiceSQL: ". $sql. ";\n");
		$resource = mysql_query($sql, $this->link);
		if (!$resource)
		{
			error_log('Invalid query: ' . mysql_error().' QUERY: '.$sql.'');
		}
		//print_r($this);
		while ($row = mysql_fetch_row($resource))
		{
			//print_r($row);
			$this->addRow($row);
		}
		mysql_free_result($resource);
		return $this;
	}

	function createDatabase($database){
		$this->runQuery("CREATE DATABASE $database");
	}

//this should only be called by a very controlled data cleansing and never directly via a web service
	function createGrantALL($database, $user, $pass, $host = 'localhost'){
		$this->sql("GRANT ALL PRIVILEGES ON $database.* TO '$user'@'$host' IDENTIFIED BY '$pass'");
	}

//this should only be called by a very controlled data cleansing and never directly via a web service
	function createDBUser($user, $pass, $host = 'localhost'){
	//`mysql -uroot -p'$dbpass' --execute="CREATE USER $user@localhost IDENTIFIED BY '$pass'"`;
		$this->sql("CREATE USER $user@$host IDENTIFIED BY '$pass'");
	}
}
?>
