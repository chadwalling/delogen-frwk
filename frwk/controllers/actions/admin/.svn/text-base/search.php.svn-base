<?
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com
chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)/actions/?.*@i', '$1/', $cwd).'Main.php');

$date = date('Y-m-d H:i:s');

$fname = ($request->hasValue("fname")) ? $request->getValue("fname") : "";
$lname = ($request->hasValue("lname")) ? $request->getValue("lname") : "";
$street= ($request->hasValue("street")) ? $request->getValue("street") : "";
$city  = ($request->hasValue("city")) ? $request->getValue("city") : "";
$state = ($request->hasValue("state")) ? $request->getValue("state") : "";
$zip   = ($request->hasValue("zip")) ? $request->getValue("zip") : "";
$phone = ($request->hasValue("phone")) ? $request->getValue("phone") : "";
$email = ($request->hasValue("email")) ? $request->getValue("email") : "";
$replace = (!empty($classID)) ? TRUE : FALSE;

$error = FALSE;
$fname = "chad";
$class = new Objects("classes");
$filters = new Filters();
if ($fname != '')
{
	$filters->setTarget($class);
	$filters->addFilter("name1", "'%".$fname."%'", "classes", "like", "||");
}

if ($lname != '')
{
	$filters->addFilter("name3", "'%".$lname."%'", "classes", "like");
}

	$filters->setLimit("20");
	$class->fetch($filters);
	print_r($class);
echo "count: ". $class->getCount();
	
exit;
function isValidClass($classID)
{
	$return = FALSE;
	if ($classID != '')
	{
		$class = new Objects("classes");
		$filters = new Filters();
		$filters->setTarget($class);
		$filters->addFilter("class_id", $classID, "classes", "=");
		$filters->setLimit("1");
		$class = $class->fetch($filters);
		$classID = $class->getClass_id();
		if ($classID != "")
		{
			$return = true;
		}
	}
	return $return;
}


function getPhoneNumberIDByClassID($classID)
{
	$return = '';
	if ($classID != '')
	{
		$class = new Objects("classes");
		$filters = new Filters();
		$filters->setTarget($class);
		$filters->addFilter("class_id", $classID, "classes", "=");
		$filters->setLimit("1");
		$classnew = $class->fetch($filters);

		if ($classnew->getClass_id() != "")
		{
			$phones = new Objects("phonenumbers");
			$c_e = new Objects("classes_phonenumbers");
			$f = new Filters();
			$f->setTarget($phones);
			$f->addJoin($c_e);
			$f->addJoin($class);
			$f->setLimit(1);
			$phones = $phones->fetch($f);
			$return = $phones->getPhonenumber_id();
		}
		else
		{
			//error_log("classid:". $classID ."not found");
		}
			
	}
	return $return;
}


function getEmailAddressIDByClassID($classID)
{
	$return = '';
	if ($classID != '')
	{
		$class = new Objects("classes");
		$filters = new Filters();
		$filters->setTarget($class);
		$filters->addFilter("class_id", $classID, "classes", "=");
		$filters->setLimit("1");
		$classnew = $class->fetch($filters);
		$classID = $classnew->getClass_id();
		if ($classID != "")
		{
			$emailAddress = new Objects("emailaddresses");
			$c_e = new Objects("classes_emailaddresses");
			$f = new Filters();
			$f->setTarget($emailAddress);
			$f->addJoin($c_e);
			$f->addJoin($class);
			$f->setLimit(1);
			$emailAddress = $emailAddress->fetch($f);
			$return = $emailAddress->getEmailaddress_id();
			//error_log("f(X) getEmailAddressIDByClassID classid:". $classID ." and emailaddress_id: $return");
		}
		else
		{
			//error_log("f(X) getEmailAddressIDByClassID  classid:". $classID ."not found");
		}
			
	}
	return $return;
}

function getAddressIDByClassID($classID)
{
	$return = '';
	if ($classID != '')
	{
		$class = new Objects("classes");
		$filters = new Filters();
		$filters->setTarget($class);
		$filters->addFilter("class_id", $classID, "classes", "=");
		$filters->setLimit("1");
		$classnew = $class->fetch($filters);
		
		if ($classnew->getClass_id() !=  "")
		{
			$address = new Objects("addresses");
			$c_a = new Objects("classes_addresses");
			$f = new Filters();
			$f->setTarget($address);
			$f->addJoin($c_a);
			$f->addJoin($class);
			$f->setLimit(1);
			$address = $address->fetch($f);
			$return = $address->getAddress_id();
			//error_log("function getaddressIDbyclassid addressID: $return");
		}
		else
		{
			//error_log("function getAddressIDByClassID was not passed a valid classID");
		}
			
	}
	else
	{
		//error_log("function getAddressIDByClassID was not passed a classID");
	}
	return $return;
}

//TODO: use request forward
//or use ajax to call this page
function createReplaceAddressByClassID(
	$classID = '',
	$addresse,
	$physicalAddress,
	$city,
	$state,
	$zip,
	$created,
	$lastMod,
	$replace = FALSE)
{
	if ($classID != "")
	{
		$addID = getAddressIDByClassID($classID);
	}
	else
	{
		//error_log("classid is NULL this function will insert new classID");
	}

	
	$address = new Objects('addresses');
	$address->setAddress_ID($addID);
	$address->setName($addresse);
	$address->setStreet($physicalAddress);
	$address->setCity($city);
	$address->setState($state);
	$address->setCode($zip);
	$address->setCreated($created);
	$address->setLastModified($lastMod);
	$address = $address->insert($replace);
	$addID = $address->getAddress_id();
	if ($classID != "" && $addID != "")
	{
		$c_a = new Objects('classes_addresses');
		$c_a->addRow(array($classID, $addID));
		$c_a->insert($replace);
		//error_log("updating classes_addresses classid: $classID and address_id: $addID");
	}
	else
	{
		error_log("classid || addressid is NULL this function will fail");
	}
	return $addID;
}



function createReplaceClassAndEmailAndPhone(
	$classID = '',
	$fname,
	$lname,
	$classtypeID = 1,
	$emailaddressValue = '',
	$phonenumberValue = '',
	$created = '',
	$lastmod = '',
	$replace)
{
	$created = ($created) ? $created : date('Y-m-d h:i:s');
	$lastmod = ($lastmod) ? $lastmod : date('Y-m-d h:i:s');
	$class = new Objects('classes');
	$class->addRow(array($classID,$fname,'',$lname,'',$created, $lastmod));
	$class = $class->insert($replace);
	//print_r($class);

	if ($classID == "")
	{
		$classID = $class->getClass_id();
		//error_log("replace is $replace.  f(x) createReplaceClassAndEmailAndPhone was not passed a so we created a new classid: $classID");
	}
	//error_log("replace is $replace.  f(x) createReplaceClassAndEmailAndPhone");
	if ($classtypeID != "" && $classID != "")
	{
		$classtype = new Objects('classes_classtypes');
		$classtype->addRow(array($classID, $classtypeID ));
		$classtype->insert($replace);

		//print_r($classtype);
		if ($emailaddressValue != "")
		{
			$emailAddressID = getEmailAddressIDByClassID($classID);
			//error_log("1st==============emailAddressID: $emailAddressID");
			$emailaddress = new Objects('emailaddresses');
			$emailaddress->addRow(array($emailAddressID, $emailaddressValue, $created, $lastmod));
			$emailaddress = $emailaddress->insert($replace);
			//print_r($emailaddress);
			$emailAddressID = $emailaddress->getEmailaddress_id();
			//error_log("2nd==============emailaddress_id: ".$emailAddressID);
			if ($emailAddressID != "" )
			{
				$c_e = new Objects('classes_emailaddresses');
				$c_e->addRow(array($classID, $emailAddressID));
				$c_e->insert($replace);
				//error_log("f(x) createReplaceClassAndEmailAndPhone update emailaddressID: $emailAddressID");
			}
			else
			{
				//error_log("f(x) createReplaceClassAndEmailAndPhone did not find a emailaddressID to update");
			}
		}

		if ($phonenumberValue != '')
		{
			$phonenumberID = getPhoneNumberIDByClassID($classID);
			$phonenumber = new Objects('phonenumbers');
			$phonenumber->addRow(array($phonenumberID, $phonenumberValue, $created, $lastmod));
			$phonenumber = $phonenumber->insert($replace);

			$phonenumberID = $phonenumber->getPhoneNumber_id();
			if ($phonenumberID != '')
			{
				$c_p = new Objects('classes_phonenumbers');
				$c_p->addRow(array($classID, $phonenumberID));
				$c_p->insert($replace);
			}
		}
	}
	return $classID;
}
?>
