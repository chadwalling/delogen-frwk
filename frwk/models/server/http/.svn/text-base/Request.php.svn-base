<?php
Render::Model('data/Data.php');
class Request extends URI
{
	public function __construct()
	{
		$this->init();
	}
	
	function init()
	{
		//$this = new Data();
		$queryArray = $this->getQueryArray();
		
		///TODO: need to handle forms
		foreach($queryArray as $key=>$val)
		{
			$this->addFields(array($key));
			$this->addValues($key, $val);
		}
		
		foreach($_REQUEST as $rkey=>$rval)
		{
			$this->addFields(array($rkey));
			$this->addValues($rkey, $rval);
			
		}
		
		foreach($_SERVER as $skey=>$sval)
		{
			$this->addFields(array($skey));
			$this->addValues($skey, $sval);
		}
	}
}
?>