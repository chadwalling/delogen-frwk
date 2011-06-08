<?php
/**
* Copyright (C) 2009 Delogen
* author: Chad Walling
* email: chad@delogen.com
* @desc- Program Main initializes enviroment variables (database connect settings, file paths, session, request object) 
* front controller and dynamic database modeling system.
* To model your databse tables change defines in Connect.php for server, database name, username and password. Only MySQL is supported.
* To get an instance of one of your tables for example- 
*                                                   $table = new Object('tablenamehere'); 
* @Instructions- create your own main by copying ContreteMain.php and naming something to do with the domain.
* ex: www.linux.org. the next line could be require_once('LinuxMain.php'); then take contents (ContreteMain.php) and paste into new LinuxMain.php
* and customize URL/REST and other settings for your application
*/
require_once(dirname(__FILE__).'/ConcreteMain.php');
$main = new Main();
$main->run();
?>

