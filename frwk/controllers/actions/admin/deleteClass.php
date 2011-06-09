<?php
chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)/actions/?.*@i', '$1/', $cwd).'Main.php');

$date = date('Y-m-d H:i:s');


$classID = ($request->hasValue("class_id")) ? $request->getValue("class_id") : "";

if ($classID)
{
	$c_c = new Objects('classes_classstatuses');
	$c_c->addRow(array('', $classID, 1, $date, $date));//1 = deleted
	$c_c->insert();
}

?>
