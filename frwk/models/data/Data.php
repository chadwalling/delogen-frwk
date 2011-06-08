<?
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com

/**
* @desc Basic- a glorified super array object.
* This Class is a powerful field to value objects good at organizing large amounts of information.
* More robust and helpful than typical arrays. Builds and implements database table like info. This models basic column row field value type data structures.
*/
class Data
{
	public $fieldTypes = array();
	public $fields = array();
	public $row = 0;
	public $debug = FALSE;
	public $name = '';

	/**
		Data Constructor.
		@param $fieldTypes The table/model field types..
		@param $fields The table/model fields.
		@param $row The inncrementor that keeps count of the rows in the table.
		@param $debug Show the debugging.
		@param $name The name of the table/model.
	*/
	public function __construct($fields = array(), $fieldTypes = array(), $row = 0)
	{
		$this->addFields($fields);
		//$this->addFieldTypes($fieldTypes);
		$this->debug = FALSE;
	}


	/**
		Turns on degbugging
		@return
	*/
	public function debugOn()
	{
		$this->debug = TRUE;
	}


	/**
		Sets the data/table/object name
		@param $name [string] Name of the lock.
		@return
	*/
	public function __setName($name)
	{
		$this->name = $name;
	}


	/**
		Gets the name of data object.
		@return name
	*/
	public function __getName()
	{
		return $this->name;
	}


	/**
		Gets the data fields.
		@returns an array of all the fields of the data object
	*/
	public function getFields($field = '')
	{
		if ($field !== '')
		{
			$fields = array_keys($this->fields);
			return $fields[$field];
		}
		return array_keys($this->fields);
	}


	/**
		Gets the data fields.
		@returns the fields of the data object
	*/
	public function getFieldsArray()
	{
		return array_keys($this->fields);
	}


	/**
		Adds a column/field to data object
		@param $fields [string] Name of field to add to data object.
		@return
	*/
	public function addColumns($fields)
	{
		$this->addFields($fields);
	}


	/**
		Adds a column/field to data object
		@param $fields [string] [array] Name of field to add to data object.
		@return
	*/
	public function addFields($fields, $type = NULL)
	{
		if (! is_array($fields))
		{
			$fields = array($fields);
		}

		foreach($fields as $val)
		{
			if (is_array($val))
			{
				$this->addColumns($fields[$val]);
			}
			else
			{
				$this->fields[$val] = '';
			}
		}
	}


	/**
		Adds Types of data to the fields.
		@param $fieldsTypes [string] [array] Name of fieldtypes to add to data object.
		@return
	*/
	public function addFieldTypes($fieldsTypes)
	{
		if (! is_array($fieldsTypes))
		{
			$fieldsTypes = array($fieldsTypes);
		}
		$i = 0;
		foreach(array_keys($this->fields) as $field)
		{
			$this->fieldTypes[$field] = $fieldsTypes[$i];
			$i++;
		}
	}


	/**
		Adds a column/field types to data object
		@param $row [int] [string] the row value to retrieve from data object.
		@return array of values
	*/
	public function getRows($row = '') //this will only return the values fields are not preserved
	{
		$i = 0;
		$rows = array();
		foreach(array_keys($this->fields) as $field)
		{
			if ($row === '')
			{
				$rows[] = $this->fields[$field];
			}
			else
			{
				if (isset($this->fields[$field][$row]))
				{
					$rows[$field] = $this->fields[$field][$row];
				}
				else
				{
					$rows[$field] = '';
				}
			}
			$i++;
		}
		return $rows;
	}

	//TODO: bad assumptions
	//need to count by row which means need to fix row incrementor.
	public function getCount()
	{
		foreach(array_keys($this->fields) as $field)
		{
			return count($this->fields[$field]);
		}
	}

    public function next($row)
    {
        $obj = new Objects($this->name);
        return $obj->addRow($this->getRow($row));
        //$this->row--;
    }

	/**
		Adds a column/field types to data object
		@param $row [int] [string] the row value to retrieve from data object.
		@return array of values
	*/
	public function getRow($row = '') //this will maintain fields to values key value pairs
	{
		$tempData = new Data();
		if ($row === '')
		{
			$tempData = $this->row;
		}
		foreach(array_keys($this->fields) as $field)
		{
			if (isset($this->fields[$field][$row]))
			{
				$tempdata[$field] = $this->fields[$field][$row];
			}
			else
			{
				$tempdata[$field] = '';
			}
		}
		return $tempdata;
	}


	/**
		Updates values based on row
		@param $field [string] the row value to retrieve from data object.
		@param $value [string] the row value to retrieve from data object.
		@param $row   [string] the row value to retrieve from data object.
		@return array of values
	*/
	public function updateValue($field, $value, $row)
	{
		if (array_key_exists($field, $this->fields))
		{
			$this->fields[$field][$row] = $value;
		}
	}


	/**
		ASSUMES the fields are set and
		Adds a row of values to data object.
		@param $rowArray [array] values to be added.
		@return array of values
	*/
	public function addRow($rowArray)
	{
		$i = 0;
		if (is_array($rowArray))
		{
			foreach(array_keys($this->fields) as $field)
			{
				if (! is_array($this->fields[$field]))
				{
					if (isset($rowArray[$i]))
					{
						$this->fields[$field] = array($rowArray[$i]);
					}
				}
				else
				{//if set then set otherwise add null
					$this->fields[$field][] = $rowArray[$i];
				}
				$i++;
			}
			$this->row++;
		}
		else
		{
			error_log("<ERROR>function addrow must be passed an array.\n");
		}
	}


	public function addValues($field, $value)
	{
		$value = (is_array($value)) ? $value : array($value);

		if ($field !== '')
		{
			if (array_key_exists($field, $this->fields))
			{
				$this->fields[$field] = $value;
			}
			else //key does not exist so add it and the values
			{
				$this->addFields($field);
				$this->fields[$field] = $value;
			}
			$this->row++;
		}
	}


	/**
		Gets values based on field and/or row.
		@param $field [array] values to be added.
		@param $row   [array] values to be added.
		@return array of values
	*/
	public function getValues($field, $row = '')
	{
		if (array_key_exists($field, $this->fields))
		{
			if ($row !== '' && isset($this->fields[$field][$row]))
			{
				$return = $this->fields[$field][$row];
			}
			elseif(!isset($this->fields[$field][$row]) && $row != '')
			{
				$return = NULL;
			}
			else
			{
				$return = $this->fields[$field];
			}
			//if (is_array($return) && count($return) == 1)
			//{
			//	$return = $return[0];
			//}
			return $return;
		}
		else
		{
			error_log("field: $field not found for object name ".$this->name.".\n");
		}
	}


	/**
		Gets last value added to object.
		@param $field [array] values to be added.
		@return last entered value
	*/
	public function getValue($field)
	{
			$return = "";
		if (array_key_exists($field, $this->fields))
		{
			if ($field != '')
			{
				$return = $this->fields[$field];
				$return = array_pop($return);
			}
			return $return;
		}
	}


	/**
		Checks to see if a field has a value
		@param $field [string] to check for values being set.
		@return [bool] if value is set or not.
	*/
	public function hasValue($field)
	{
		$return = false;
		if ($field)
		{
			if (array_key_exists($field, $this->fields))
			{
				if (is_array($this->fields[$field]))
				{
					if (count($this->fields[$field]))
					{
						if (count(array_values($this->fields[$field])))
						{
							$return = true;
						}
					}
				}
			}
		}
		return $return;
	}


	/**
		Set the data objects fields.
		@param $fields [string] or [array] of fields to set.
		@return
	*/
	public function setFields($fields)
	{
		$this->fields = NULL;
		if (! is_array($fields))
		{
			$fields = array($fields);
		}

		foreach($fields as $val)
		{
			if (is_array($val))
			{
				$this->addColumns($fields[$val]);
			}
			else
			{
				$this->fields[$val] = '';
			}
		}
	}


	/**
	 * Handling getters and setters for fields if already set in the data object
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
