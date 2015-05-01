<?php
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
Render::Model('data/Data.php');

class Session extends Data
{
	var $id = '';
	var $file = '';
	function __construct()
	{
		$this->init();
	}
	
	function init()
	{
		session_start();
	}
	
	function isStarted()
	{
	    	return session_id();
	}

    	//creates a session cookie ... assumes authentication was successful
	function _makeid($class_id)
	{
		$ip = $_SERVER["REMOTE_ADDR"];
		if(session_id()){
			session_regenerate_id();
		}else{
			session_cache_expire(60);
			session_start();
		}
		$this->id = session_id();
		$salt = $class_id.$this->id.microtime().$ip;
		$_SESSION['id'] = $salt;
		$_SESSION['class_id'] = $class_id;
		$hash = hash( 'sha256', $salt.$class_id);
	
		$sess = new Objects("sessions");
		$date = date('Y-m-d H:i:s');
		$expiresDate = date('Y-m-d H:i:s', strtotime($date ."+ 1 hour"));
	
		$sess->addRow(array($this->id, $class_id, $hash, $date, $expiresDate, $date));
		$sess->insert(true);
	
		return $this->id;
	}

	function getLoginType(){
		if(session_id() !== "" && !empty($_SESSION['class_id']) ){
		    $logins = new Objects("logins");
		    $logins->setclass_id($_SESSION['class_id']);
		    $l = $logins->fetch();
		    return $l->getlogintype_id();
		}
		error_log("getLoginType() failed.");
		return NULL;
	}
    
	function getid(){
		if(Session::isAuth()){
			return session_id();
		}
		return false;
	}

	function isAuth()
	{
	        if(session_id() == "") return false;
		$sessObject = new Objects("sessions");
		$sessObject->setSession_id(session_id());
		$newsess = $sessObject->fetch();
		$hash = $newsess->getHash();
		$class_id = $newsess->getclass_id();
	        if($_SESSION['id'] && $class_id){
			if($hash == hash( 'sha256', $_SESSION['id'].$class_id)){
			    Session::_makeid($class_id);
			    return true;
			}
	        }
		return false;
	}

	function start()
	{
		$id = $this->getid();
		if (!$id)
		{
			$this->_makeid();
		}
	}

	function _getfile()
	{
		$file = $this->file;
		preg_match('/\/home\/(.*)?/', $_SERVER['DOCUMENT_ROOT'], $match );
		$site = $match[1];
        	//error_log(/tmp/cache/".$site."/".$file);
		return CACHE_DIR.$site."/".$file;
	}
}
?>
