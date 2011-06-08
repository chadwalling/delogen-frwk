<?
// Copyright (C) 2007 Delogen
chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)views?.*@i', '$1', $cwd).'/controllers/Main.php');


        function encode($arr) {            
            $str = '{';
            foreach($arr as $key => $value) {
                $str .= '"'.$key.'":"'.$value.'",';
            }
            
            $out = substr_replace($str, "", -1);
            $out .= '}';
            
            return $out;
        }

///CRUD on a model?   TODO: handle custom fields in the return object after complex query is built.gai

$classType = 1; //user type

$classes_classtypes = new Objects('classes_classtypes');
$classtypes         = new Objects('classtypes');
$classes            = new Objects('classes');
$classes_statuses   = new Objects('classes_classstatuses');

$filters = new Filters();
$filters->setTarget($classes);
$filters->addJoin($classes_classtypes);
$filters->addJoin($classtypes);
$filters->addJoin($classes_statuses, "LEFT JOIN");
/**manual example below
$filters->addJoin('classes_classtypes', 'class_id', 'LEFT JOIN', 'classes', 'class_id');
$filters->addJoin('classtypes', 'classtype_id', 'LEFT JOIN', 'classes_classtypes', 'classtype_id');*/
//$filters->addFilter("classstatus_id", "1", "classes_classstatuses", "!=");

$filters->addFilter("classtype_id", $classType, "classtypes", "=", "&&");
//$filters->addJoin('classes_classstatuses', 'class_id', 'LEFT JOIN', 'classes', 'class_id');
$filters->addFilter("classstatus_id", "", 'classes_classstatuses', 'is null');
$filters->setLimit(50);
$classes = $classes->fetch($filters);

$cid = $classes->getValues('class_id');

for($i = 0; $i < count($cid); $i++)
{
    /*	$c_id = $classes->getValues("class_id", $i);

        $ccs = new Objects('classes_classstatuses');
	    $filters = new Filters();
	    $filters->setTarget($ccs);
	    $filters->addFilter("class_id", $c_id, 'classes_classstatuses', '=');
	    $ccs->fetch($filters);
	    if($ccs->getClassstatus_id() == 1)
	    {
		    continue;
	    }
     */
    $c     = $classes->getRow($i); 
    $val   = $c['class_id'];
    $fname = $c['name1'];
    $lname = $c['name3'];

    $tempClass = new Objects('classes');
    $tempClass->setClass_id($val);

    $phonenumbers 	      = new Objects('phonenumbers');
    $classes_phonenumbers = new Objects('classes_phonenumbers');
    $filter1 = new Filters();
    $filter1->setTarget($phonenumbers);
    $filter1->addJoin($classes_phonenumbers);
    $filter1->addJoin($tempClass);
    $filter1->setLimit(50);
    $phonenumbers->fetch($filter1);

    $classes_addresses = new Objects('classes_addresses');
    $addresses         = new Objects('addresses');
    $f = new Filters();
    $f->setTarget($addresses);
    $f->addJoin($classes_addresses);
    $f->addJoin($tempClass);
    $f->setLimit(50);
    $addresses->fetch($f);

    $emailaddresses		    = new Objects('emailaddresses');
    $classes_emailaddresses = new Objects('classes_emailaddresses');
    $f = new Filters();
    $f->setTarget($emailaddresses);
    $f->addJoin($classes_emailaddresses);
    $f->addJoin($tempClass);
    $f->setLimit(50);
    $emailaddresses->fetch($f);

    if($request->getValue('format') == 'json'){
	$users .= encode(( array(
			'fname' => ucfirst($fname),
			'lname' => ucfirst($lname),
			'address' => $addresses->getStreet(),
			'city'  => $addresses->getCity(),
			'state' => $addresses->getState(),
			'zip'   => $addresses->getCode(),
			'phone' => $phonenumbers->getNumber(),
			'email' => $emailaddresses->getEmail()
		)));
	$users .= ",";

    }else {
	$color = ($i%2) ? "#E7E4D3" : "F1EFE2" ;
	$id = $val;
	$html  .= "<tr bgcolor='$color'>";
	$html  .= "<td>".ucfirst($fname)."</td>";
	$html  .= "<td>".ucfirst($lname)."</td>";
	$html  .= "<td>".$addresses->getStreet()."</td>";
	$html  .= "<td>".ucfirst($addresses->getCity())."</td>";
	$html  .= "<td>".$addresses->getState()."</td>";
	$html  .= "<td>".$addresses->getCode()."</td>";
	$html  .= "<td>".$phonenumbers->getNumber()."</td>";
	$html  .= "<td>".$emailaddresses->getEmail()."</td>";
	$html  .= "<td><a href='?v=/admin/addUpdate.php&class_id=$id'>Edit</a> | <a href='javascript: getElementByAJAX(\"POST\", \"p\", \"c\", \"?c=/actions/deleteClass.php&class_id=$id\", \"\", \"\", \"class_delete\");'>Delete</a></td>";
	$html  .= "</tr>";
	}
}
$users = rtrim ($users,",");
//if (count($users)) echo "[".$users."]"; die();
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
<a href="?v=/admin/index.php">Main</a> | <a href="?v=/admin/addUpdate.php">Add New</a> | <a href="?v=/admin/search.php">Search</a> | <a href="?v=/admin/manage_gallery.php">Gallery</a>
<table border="1">
<tr class="ADMIN_HEADER" bgcolor="grey">
	<th>First Name</th>
	<th>Last Name</th>
	<th>Street</th>
	<th>City</th>
	<th>State</th>
	<th>Zip</th>
	<th>Phone</th>
	<th>Email</th>
	<th>Action</th>
</tr>
<div id="p">
 <div id="c">
<form method="POST" name="class_delete" id="class_delete" action="#">
<?
echo $html;
?>
</form>
</div>
</div>
</table>
</body>

</html>
