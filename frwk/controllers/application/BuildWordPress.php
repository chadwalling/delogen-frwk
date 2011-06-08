<?php
chdir($cwd = dirname(__FILE__));
require_once(preg_replace('@(.*)/application/?.*@i', '$1/', $cwd).'Main.php');
Render::Controller("application/abstractBuild.php");

class BuildWordPress extends abstractBuild{

	public $conf = "wordpress.conf"; //apache vhost conf file
	public $ini  = "wordpress.ini";

	function __construct($argv){
		parent::__construct($argv);
	}


	function run(){
		$pass = $this->generatePassword(9,4);
		$domain = $this->domain;
		$owner  = $this->owner;
		$database = $owner."_".$domain;

		`echo $pass > /home/$owner/$domain/text`;
		`chmod 000 /home/$owner/$domain/text`;

		$sql = new ServiceSQL();
		$dbuser = $this->formatWordPressDBUser($domain."_".$owner);
		$sql->createDBUser($dbuser, $pass, 'localhost');
		$sql->createDatabase($database);
		$sql->createGrantALL($database, $dbuser, $pass, 'localhost');

		//`perl -p -i -e s/classes_db/$/g $destination/controllers/filesystem/Environment.ini`;
		`perl -p -i -e s/__OWNER/$owner/g /home/$owner/$domain/wp-config.php`;
		`perl -p -i -e s/__DOMAIN/$domain/g /home/$owner/$domain/wp-config.php`;
		`perl -p -i -e s/__DATABASE/$database/g /home/$owner/$domain/wp-config.php`;
		`perl -p -i -e s/__PASS/$pass/g /home/$owner/$domain/wp-config.php`;

		`perl -p -i -e s/__USER/$dbuser/g /home/$owner/$domain/wp-config.php`;
		$this->finalize();
        `apachectl graceful`;
		die("\n\nbuild done.\n");
	}


	function formatWordPressDBUser($owner){
		$dbUser = $owner;
		$dbUser = preg_replace('/![a-z]/', '', $dbUser);
		if (strlen($owner) > 15) { $dbUser = substr($owner, 0, 10); }
		return $dbUser;
	}

	function delWordPressDBUser($owner){
		`userdel $owner`;
		if(is_dir("/home/src/".$owner)){
			`rm -frv /home/src/$owner/`;
			`rm -f /etc/httpd/conf/webapps.d/$owner.com.conf`;
		}
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

}

$buildWP = new BuildWordPress($argv);
$buildWP->run();

?>
