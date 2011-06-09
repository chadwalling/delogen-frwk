<?php
class FileSystem
{
	var $file = '';
	var $files = array();
	
	function FileSystem($file)
	{
		if (is_array($file))
		{
			$this->files = $file;
		}
		elseif($file != '')
		{
			$this->file = $file;
		}
		else
		{
			
		}
	}
	
	function IncludeModel($file)
	{
		include_once($file);
	}
	
	function IncludeView($file)
	{
		include_once($file);
	}

	function IncludeController($file)
	{
		include_once($file);
	}

	
}

?>