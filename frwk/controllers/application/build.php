<?php
chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)/application/?.*@i', '$1/', $cwd).'Main.php');

define("IP_ADDRESS", "IP_ADDRESS");
define("SITE_SRC", "SITE_SRC");
define("SITE_TARGET", "SITE_TARGET");
define("SITE_FRWK", "SITE_FRWK");
define("SITE_TARGET", "SITE_TARGET");
define("SITE_OWNER", "SITE_OWNER");
define("SITE_GROUP", "SITE_GROUP");
define("SITE_DATABASE", "SITE_DATABASE");
define("SITE_TLD", "SITE_TLD");
define("WILD_CARD", "*");
define("FRWK","frwk");
define("PATH_SEPARATOR", "/");
define("FREE", "free");

$siteSrc = array();
$destination = '';
$owner = '';
$group = '';
$addr = '';
$siteSrcCount = 0;
$confFile = isset($argv[1]) ? $argv[1] : ''; //this will also be the FQD
$type = isset($argv[2]) ? $argv[2] : '';
$cwd = dirname(__FILE__);
$cwd = $cwd.PATH_SEPARATOR;
$FQP = $cwd.$confFile;
$ext_conf = "conf";

/*
usage example:
php build.php mydomainname.com type=wordpress
*/

$type = getTypeCommand($type);
if($type == FRWK){
	define("TEMPLATE_CONF", $cwd."template_frwk.conf");
	define("TEMPLATE_COM", $cwd."template_frwk.com");
}else{
	define("TEMPLATE_CONF", $cwd."template.conf");
	define("TEMPLATE_COM", $cwd."template.com");
}


preg_match_all('/\b^(-|[a-z0-9])*\b/ix', $confFile, $result, PREG_PATTERN_ORDER);
$result = $result[0];

$parts = pathinfo($confFile);
if(isset($parts['extension'])){
	$ext = $parts['extension'];
}else{
	if ($type != FREE) die( "Missing TLD (extension) or if you do not want a personal domain then you must specify type=free as a CLI parameter." );
}


//$params = buildConfFileKeyValPairs($FQP, $siteSrc, $confFile);

$params = parse_ini_file($confFile);
//set keys
print_r($params); exit;
if (count($params))
{
	foreach($params as $key=>$val)
     	{
		switch ($key)
		{
			case IP_ADDRESS;
				$addr = $val;
				break;
			case SITE_TARGET;
				$destination = $val;
				break;
			case SITE_OWNER;
				$owner = $val;
				break;
			case SITE_GROUP;
				$group = $val;
				break;
			case SITE_DATABASE;
				$site_db = $val;
				break;
		}
	}
}
else
{
	echo "problem building";
	exit;
}

if($type != "delete"){
//might not want framework copies over to wordpress or other builds
 //might be first build
                //parameter should be a domain name
                //TODO: need to check if domain already exists on server...if it does need to verify the user creating domain
                // is the owner...maybe need password
                $ext = isValidExt($conFile);
                if (!$ext) die('invalid domain extension');
                $base = str_replace(".".$ext,"",$domain);
                $conf = $base.".conf";
                $com = $base.".".$ext;
                $_template_com = TEMPLATE_COM;
                $_template_conf= TEMPLATE_CONF;
                `cp $_template_conf $conf`;
                `cp $_template_com $com`;
                $cwd = dirname(__FILE__);
                $cwd = $cwd."/";
                $domain_name_conf = $cwd.$conf;
                //echo "perl -p -e s/DOMAIN/$domain/g $domain_name_conf"; exit;
                `perl -p -i -e s/DOMAIN/$base/g $domain_name_conf`;
                regEx($cwd.$com, array('DOMAIN'=>$base));
                regEx($cwd.$com, array('EXT'=>$ext));
                `mkdir "/home/src/$base"`;
                `mkdir "/home/src/$base/logs/"`;
                `touch "/home/src/$base/logs/"$base"_accesses.log"`;
                `touch "/home/src/$base/logs/"$base"_errors.log"`;
                `mkdir "/home/src/$base/configurations"`;
                `mkdir "/home/src/$base/configurations/httpd"`;
                `mkdir "/home/src/$base/configurations/httpd/domains"`;
                `cp $cwd$com "/home/src/$base/configurations/httpd/domains/"`;
                `ln -s /home/$base/configurations/httpd/domains/$base.$ext /etc/httpd/conf/webapps.d/$base.$ext.conf`;
}


if ($type != ''){
	$db_pass = build($type, $site_db, $owner);
}else{
	die("need to specify the build type.\n");
}

function build($type, $site_db, $owner){
	if ($type == "wordpress"){
		$dbUser = $owner;
		if (strlen($owner) > 15) { $dbUser = substr($owner, 0, 10); }
		return BuildWordPress($site_db, $owner, $dbUser."_1");
	}elseif($type == "delete" && $owner != ''){
		`userdel $owner`;
		if(is_dir("/home/src/".$owner)){
			`rm -frv /home/src/$owner/`;
			`rm -f /etc/httpd/conf/webapps.d/$owner.com.conf`;
		}
		//todo delete user from db
		die();
	}elseif($type == "frwk"){

	}else{
		echo "build type of site ".$type." not found, command syntax error.";
		die();
	}
}



function BuildWordPress($db,$owner, $dbUser){

	$pass = BuildWordPressDB($db, $dbUser);
	`mkdir /home/src/$owner`;
	`mkdir /home/src/$owner/views`;
	`cp /home/src/wordpress/* /home/src/$owner/views -fr`;
	`echo $pass > /home/src/$owner/text`;
	//domain, user, pass switch out in the file
	`perl -p -i -e s/__DOMAIN/$owner/g /home/src/$owner/views/wp-config.php`;
	`perl -p -i -e s/__PASS/'$pass'/g /home/src/$owner/views/wp-config.php`;
	if (!empty($dbUser)){ $db=$dbUser; }
	`perl -p -i -e s/__USER/$db/g /home/src/$owner/views/wp-config.php`;

}

function BuildWordPressDB($db, $dbUser){
	$pass = generatePassword(9,4);
	//echo "mysql -uroot -p'My$Q_r0ot=69' -hlocalhost"; exit;
	//`mysql -uroot -p'My\$Q_r0ot=69' --execute="DELETE USER $db;"`;
	$dbpass = "My\$Q_r0ot=69";
	$user = $db;
	if (!empty($dbUser)){ $user=$dbUser; }
	//echo "mysql -uroot -p'$dbpass' --execute=\"CREATE USER $user IDENTIFIED BY '$pass'\"";
	//`mysql -uroot -p'$dbpass' --execute="CREATE USER $user@localhost IDENTIFIED BY '$pass'"`;
	`mysql -uroot -p'$dbpass' --execute="CREATE DATABASE $db"`;
	`mysql -uroot -p'$dbpass' --execute="GRANT ALL PRIVILEGES ON $db.* TO '$user'@'localhost' IDENTIFIED BY '$pass'"`;

	return $pass;
}

function getTypeCommand($type){
	$return = false;
	if ($type != '')
	{
		$specialConfig = explode('=', $type);
		if (count($specialConfig) > 1){
			if (!TemplateTypes($specialConfig[1]) || $specialConfig[0] != "type"){
				echo "template type not found or second parameter key was not type";
				die();
			}
		}else{
			echo "parameter config error";
			die();
		}
	}
	return $specialConfig[1];
}


/*
if word press build create database and user
CREATE USER USER IDENTIFIED BY 'PASS';
GRANT ALL PRIVILEGES ON DATABASE.* TO "USER"@"localhost"
IDENTIFIED BY "PASS";

CREATE USER microhogs IDENTIFIED BY 'microhogs+_poLK';
GRANT ALL PRIVILEGES ON microhogs.* TO "microhogs"@"localhost"
IDENTIFIED BY "microhogs+_poLK";

*/

$framework = $siteSrc[SITE_FRWK];
$siteSrc = $siteSrc[SITE_SRC];

$filesArray = array();
echo "===========================================================\n";
echo "buidling site...\n";

if ($framework != '' && $destination != '' && $siteSrc != '')
{
	//if(!is_dir($destination))
	//{
		`adduser $owner`;
	//}
	echo "cp $framework $destination -Rf\n";
	`cp $framework $destination -Rf`;
	echo "cp $siteSrc $destination -Rf\n";
	`cp $siteSrc $destination -Rf`;
}
else
{
	echo "parameter missing";
}

//`ln -s /home/$owner/configurations/httpd/domains/$owner.$ext /etc/httpd/conf/webapps.d/$owner.$ext`;

//`perl -p -i -e s/classes_db/$site_db/g $destination/models/Objects.php`;
`perl -p -i -e s/classes_db/$site_db/g $destination/controllers/filesystem/Environment.ini`;

TraverseDirSetPermissions($destination, $filesArray);

if ($owner != '' && $group != '')
{
	echo "\nchown $owner.$group $destination -R\n";
	`chown $owner.$group $destination -R`;
	`service httpd restart`;
	echo "\n...[done]... enjoy your new ".$owner." web site.\n";
}
else
{
	echo "owner or group not set in conf file";
}



function TemplateTypes ($type)
{
	$ret = false;
	$types = array('wordpress','delete', FRWK, FREE);
	if (in_array($type, $types)){
		$ret = true;
	}
	return $ret;
}

function TraverseDirSetPermissions($dir, &$filesArray)
{
	if (is_dir($dir))
	{
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (substr($file, -1) == "~")
				{
					$path = FixPaths($dir, $file);
					echo "rm -f $path\n";
					`rm -f $path`;
					continue;
				}
				if ( ($file != "." && $file != ".." ) )
				{
					if (is_dir(''.$dir.'/'.$file.''))
					{
						$path = FixPaths($dir, $file);

						echo "chmod 750 $path\n";
						$str = preg_replace('/(\s+)/', '\\\$1', $path);
						//$cmd = escapeshellcmd("chmod 750 $str");
						exec("chmod 750 $str");
						if($file == "compiledtags" || $file == "compiledtags/" || $file == "/compiledtags" || $file == "/compiledtags/") exec("chmod 770 $str");
						$files = TraverseDirSetPermissions($path, $filesArray);
					}
					else
					{
						$path = FixPaths($dir, $file);
						echo "chmod 640 $path\n";
						$str = preg_replace('/(\s+)/', '\\\$1', $path);
						//$cmd = escapeshellcmd("chmod 640 $str");
						exec("chmod 640 $str");
						$filesArray[] = $path;
					}
				}
			}
			closedir($dh);
		}
	}
	else
	{
		echo "chmod 640 $dir\n";
		`chmod 640 $dir`;
		$filesArray[] = $dir;
	}
}




function buildConfFileKeyValPairs($FQP, &$siteSrc, $domain)
{
	$params = array();
	$count = 0;
	if (file_exists($FQP) && !isValidExt($FQP) )
	{
		$fileContents = file_get_contents($FQP);
		$eachline = explode("\n", $fileContents);

		foreach($eachline as $val)
		{
			list($key, $value) = split('=', $val);
			if ($key == SITE_SRC)
			{
				$siteSrc[SITE_SRC] = $value;
			}
			if ($key == SITE_FRWK)
			{
				$siteSrc[SITE_FRWK] = $value;
			}
			$params[$key] = $value;
			$count++;
		}
	}
	elseif(!empty($domain))
	{
		//might be first build
		//parameter should be a domain name
		//TODO: need to check if domain already exists on server...if it does need to verify the user creating domain
		// is the owner...maybe need password

		$ext = isValidExt($domain);
		if (!$ext) die('invalid domain extension');
		$base = str_replace(".".$ext,"",$domain);
		$conf = $base.".conf";
		$com = $base.".".$ext;
		$_template_com = TEMPLATE_COM;
		$_template_conf= TEMPLATE_CONF;
		//`cp $_template_conf $conf`;
		//`cp $_template_com $com`;
		$cwd = dirname(__FILE__);
		$cwd = $cwd."/";
		$domain_name_conf = $cwd.$conf;
		//echo "perl -p -e s/DOMAIN/$domain/g $domain_name_conf"; exit;
		//`perl -p -i -e s/DOMAIN/$base/g $domain_name_conf`;
		//regEx($cwd.$com, array('DOMAIN'=>$base));
		//regEx($cwd.$com, array('EXT'=>$ext));
		//`mkdir "/home/src/$base"`;
		//`mkdir "/home/src/$base/logs/"`;
		//`touch "/home/src/$base/logs/"$base"_accesses.log"`;
		//`touch "/home/src/$base/logs/"$base"_errors.log"`;
		//`mkdir "/home/src/$base/configurations"`;
		//`mkdir "/home/src/$base/configurations/httpd"`;
		//`mkdir "/home/src/$base/configurations/httpd/domains"`;
		//`cp $cwd$com "/home/src/$base/configurations/httpd/domains/"`;
		//`ln -s /home/$base/configurations/httpd/domains/$base.$ext /etc/httpd/conf/webapps.d/$base.$ext.conf`;

		return buildConfFileKeyValPairs($domain_name_conf, $siteSrc, '');

	}else{
		echo "\nfile: $confFile not found or permission issue\n";
	}
	return $params;
}

function cp($source, $destination){

}

function regEx($file, $keyvalpairArray = array()){
	foreach ($keyvalpairArray as $key=>$val){
		`perl -p -i -e s/$key/$val/g $file`;
	}
}

	function FixPaths(
		$path1,
		$path2)
	{
		$separatorChar = "";
		if (strlen($path1) > 0 && strlen($path2) > 0
			&& strrpos($path1, '/') !== (strlen($path1) - 1)
			&& strpos($path2, '/') !== 0)	// there is no slash at the end of element 1 and no slash at the begining of element 2 and neither is empty
		{
			$separatorChar = '/';
		}
		$path = preg_replace("/\/\//","/", $path1.$separatorChar.$path2);

		return $path;
	}

	function generatePassword($length=9, $strength=0) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
	function isValidExt($file){
		$ext = substr(strrchr($file,'.'),1);
		$validExt = array('com','org','net','biz','info','name','aero','biz','info','jobs','museum','name');
		if(in_array($ext, $validExt)){ return $ext;}
		return false;
	}
?>
