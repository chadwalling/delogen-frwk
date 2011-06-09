<?php
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
//chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)/actions/?.*@i', '$1/', $cwd).'Main.php');
$date = date('Y-m-d H:i:s');

$login = ($request->hasValue("login")) ? $request->getValue("login") : "";
$pass  = ($request->hasValue("password")) ? $request->getValue("password") : "";

$error = FALSE;
$forward = "login";

if ($login != '' && $pass != '')
{
	$logins = new Objects("logins");
	$logins->setName($login);
	$logins->setPassword($pass);
	$logins = $logins->fetch();
	$id = $logins->getlogin_id();
	$l_id = $logins->getLogintype_id();


	if ($id != "")
	{
		//echo "authenticated";
		//now store info into session
		$class = new Objects("classes");
		$classes_logins = new Objects("classes_logins");
		$classes_logins->setlogin_id($id);
		$cl = $classes_logins->fetch();
		$class->setClass_id($cl->getClass_id());
		$user = $class->fetch();

		$session = new Session();
		//$session->set("fname", $user->getName1());
		$id = $session->_makeid($cl->getClass_id());
		//$session->set("login_id",$id);
		//$session->set("type_id", $l_id);

        if ($l_id == 1){
        	$forward = "admin/index.php";
       	}elseif ($l_id == 2){
	        $forward = "user/index.php";
	    }
	}
	else
	{
		$request->addValues("error", "authentication failed");
		$forward = "login";
	}
}
else
{
	$request->addValues("error", "Parameter(s) missing.");
	$forward = "login";
}
Render::View($forward);
?>
