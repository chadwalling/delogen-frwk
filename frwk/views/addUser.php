<?php
/// Copyright (C) 2007 Delogen
//http://delogen.com/?v=/admin/addUpdate.php

require('../controllers/Main.php');
$class_id = ($request->hasValue("class_id")) ? $request->getValue("class_id") : "";

$classType	= 1;
$fname	= '';
$lname	= '';
$street	= '';
$city		= '';
$state	= '';
$zip		= '';
$phoneN 	= '';
$email 	= '';

?>
<html>
<head>
<script type="text/javascript" src="js/yahoo/yahoo.js"></script>

<script type="text/javascript" src="http://www.delogen.com/js/yui/event.js"></script>
<script type="text/javascript" src="http://www.delogen.com/js/yui/connection/connection.js"></script>
<script type="text/javascript" src="http://www.delogen.com/js/utils.js"></script>

<?php
Render::View("/js/utils.js");
Render::View("/css/admin.php");
?>
</head>
<body>
<div id="addUpdateStatic">
<div id="addUpdateDynamic">
<form method="POST" name="class_add_upate" id="class_add_upate" action="?c=/actions/createReplaceClass.php">
<input type="hidden" name="class_id" value='<?php echo $class_id?>'>
<table border="1" class="ADMIN_HEADER">
<tr><th>First Name </th><td><input type="text" name="fname" value='<?php echo $fname?>'></td></tr>
<tr><th>Last Name </th><td><input type="text" name="lname" value='<?php echo $lname?>'></td></tr>
<tr><th>Steet </th><td><input type="text" name="street" value='<?php echo $street?>'></td></tr>
<tr><th>City </th><td><input type="text" name="city" value='<?php echo $city?>'></td></tr>
<tr><th>State </th><td><input type="text" name="state" value='<?php echo $state?>'></td></tr>
<tr><th>Zip </th><td><input type="text" name="zip" value='<?php echo $zip?>'></td></tr>
<tr><th>Phone </th><td><input type="text" name="phone" value='<?php echo $phoneN?>'></td></tr>
<tr><th>Email </th><td><input type="text" name="email" value='<?php echo $email?>'></td></tr>
<tr><th>Domain </th><td><input type="text" name="domain" value='<?php echo $domain?>'></td></tr>
<tr><th>User Name </th><td><input type="text" name="username" value='<?php echo $username?>'></td></tr>

<tr><th>Password </th><td><input type="password" name="pass" value='<?php echo $pass?>'></td></tr>

<tr><th>Action </th><td align="right"><input type="button" name="submit" onclick='javascript: getElementByAJAX("POST", "addUpdateStatic", "addUpdateDynamic", "?c=/actions/createReplaceClass.php&class_id=<?php echo $class_id?>", "", "", "class_add_upate");' value="Add/Update"></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>
