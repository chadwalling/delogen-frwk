<?
/// Copyright (C) 2007 Delogen
//http://delogen.com/?v=/admin/addUpdate.php
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

if ($class_id)
{
	$classes_classtypes = new Objects('classes_classtypes');
	$classtypes 	  = new Objects('classtypes');
	$classes 	    	  = new Objects('classes');
	$filters = new Filters();
	$filters->setTarget($classes);
	$filters->addFilter("class_id", $class_id, "classes", "=");
	$filters->setLimit("1");
	$classes->fetch($filters);

	if ($classes->getClass_id() != "")
	{
		$phonenumbers 	    = new Objects('phonenumbers');
		$classes_phonenumbers = new Objects('classes_phonenumbers');
		$filter1 = new Filters();
		$filter1->setTarget($phonenumbers);
		$filter1->addJoin($classes_phonenumbers);
		$filter1->addJoin($classes);
		$filter1->setLimit("1");
		$phonenumbers->fetch($filter1);
		
		$classes_addresses = new Objects('classes_addresses');
		$address           = new Objects('addresses');
		$filter1 = new Filters();
		$filter1->setTarget($address);
		$filter1->addJoin($classes_addresses);
		$filter1->addJoin($classes);
		$address->setLimit(1);
		$address->fetch($filter1);
		
		$emailaddress		= new Objects('emailaddresses');
		$classes_emailaddresses = new Objects('classes_emailaddresses');
		$f2 = new Filters();
		$f2->setTarget($emailaddress);
		$f2->addJoin($classes_emailaddresses);
		$f2->addJoin($classes);
		$emailaddress->setLimit(1);
		$emailaddress->fetch($f2);
		
		$class_id = $classes->getClass_id();
		$fname = $classes->getName1();
		$lname = $classes->getName3();
		$street = $address->getStreet();
		$city = $address->getCity();
		$state = $address->getState();
		$zip = $address->getCode();
		$phoneN = $phonenumbers->getNumber();
		$email = $emailaddress->getEmail();
	}
}
?>
<html>
<head>
<script type="text/javascript" src="js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="js/yui/event/event.js"></script>
<script type="text/javascript" src="js/yui/connection/connection.js"></script>
<?
Render::View("/js/utils.js");
Render::View("/css/admin.php");
?>
</head>
<body>
<a href="?v=/admin/index.php">Main</a> | <a href="?v=/admin/addUpdate.php">Add New</a>
<div id="addUpdateStatic">
<div id="addUpdateDynamic">
<form method="POST" name="class_add_upate" id="class_add_upate" action="?c=/actions/admin/createReplaceClass.php">
<input type="hidden" name="class_id" value='<?=$class_id?>'>
<table border="1" class="ADMIN_HEADER">
<tr><th>First Name </th><td><input type="text" name="fname" value='<?=$fname?>'></td></tr>
<tr><th>Last Name </th><td><input type="text" name="lname" value='<?=$lname?>'></td></tr>
<tr><th>Steet </th><td><input type="text" name="street" value='<?=$street?>'></td></tr>
<tr><th>City </th><td><input type="text" name="city" value='<?=$city?>'></td></tr>
<tr><th>State </th><td><input type="text" name="state" value='<?=$state?>'></td></tr>
<tr><th>Zip </th><td><input type="text" name="zip" value='<?=$zip?>'></td></tr>
<tr><th>Phone </th><td><input type="text" name="phone" value='<?=$phoneN?>'></td></tr>
<tr><th>Email </th><td><input type="text" name="email" value='<?=$email?>'></td></tr>
<tr><th>Action </th><td align="right"><input type="button" name="submit" onclick='javascript: getElementByAJAX("POST", "addUpdateStatic", "addUpdateDynamic", "?c=/actions/admin/createReplaceClass.php&class_id=<?=$class_id?>", "", "", "class_add_upate");' value="Add/Update"></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>
