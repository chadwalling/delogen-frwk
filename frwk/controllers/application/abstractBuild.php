<?php
Render::Model('data/Data.php');

define("IP_ADDRESS", "IP_ADDRESS");
define("SITE_SRC", "SITE_SRC");
define("SITE_TARGET", "SITE_TARGET");
define("SITE_FRWK", "SITE_FRWK");
define("SITE_OWNER", "SITE_OWNER");
define("SITE_GROUP", "SITE_GROUP");
define("SITE_DATABASE", "SITE_DATABASE");
define("SITE_TLD", "SITE_TLD");
define("EXT", "EXT");
define("WILD_CARD", "*");
define("FRWK","frwk");
define("FREE", "free");
define("WORDPRESS", "wordpress");



/**
* @desc:
* This class can be extended to auto build any web application, apache conf, and database to be made LIVE on a server or localhost.
* What this does is looks for a config file that tells source dir and dest dir to build from then to. It builds a user for the domain and permissions for the site based on
* that domain. EX: /home/chad. chad is user. domain is mydomain.com. Then /home/chad/mydomain.com would be the ROOT dir. This allows for work done in source then a build script can run
* to push to live server. Or you can make many changes then 'build' to what destination you would like or temporary staging or dev destinations.
* This makes it very easy to move your code and permissions and apache configs around to quickly build new web sites or update them.
*
*
* @parameter:
* 1. CLI parameter is domain name
* 2. CLI parameter is type=<build type> ex: type=wordpress
* 3. CLI parameter is owner ex: owner=<usernamehere> OR owner
*
*	EX- domain type could be wordpress the handler will call the appropriate build script based on the domain type and auto-create
*	the site. In this example the word press site would be automatically built, configured, and live.
* 	TODO: finish making this to be able to build a generic delogen-frwk site (non-open source based build)
* 	may want to have the configuration parameters stored in a database and derived from a database versus a conf file
*
*/

abstract class abstractBuild extends Data{

	var $schema = null;
	var $port = null;
//	var $conf;
	var $type;
	var $ext_conf = "conf";
	var $cwd;
	var $FQP;
	var $siteSrcCount = 0;
	var $siteSrc = array();
	var $target = '';
	var $owner = '';
	var $group = '';
	var $addr = '';
	var $domain;
	var $argv;
	var $iniParameters;
	var $ext_domain;
	var $base;
	var $site_db;
    const USER = "USER";
    const DOMAIN = "DOMAIN";

	function __construct($argv){


		$this->argv = $argv;
        chdir($cwd = dirname(__FILE__));
		$this->cwd = $cwd;
        $this->cwd = str_replace(':','', $this->cwd);
        $this->cwd = $this->cwd."/";
    	if (count($argv)){
			$this->setCLIParameters($argv);
		}

		if($this->isTypeCommandValid($this->type)){
		  	echo "valid type\n";
		}else{
			die("invalid type\n");
		}

		//setup the vhost apache conf file
		$this->prepareConf();

		if ($this->owner){
            $owner = $this->owner;
			echo "/usr/sbin/useradd $owner\n";
			`/usr/sbin/useradd $owner`;
		}

		//now the vhost conf file is ready
		//move on to deployment of files for build config ini file
		$this->prepareINIFile();
		$this->prepareINIValues();

		//all the second prepare INI values does is get the newly set ini values by reparsing it
        $this->loadINIValues();
		$this->createStandardDirAndFiles();
		$this->processINIParametersAsFunctions();

	}

	//TODO: make this use programmer/user defined paths
	function createStandardDirAndFiles(){
        $domain = $this->domain;
        $owner = $this->owner;
        $ext = $this->ext_domain;
	    //TODO: make the FULL site source path var come from db or from ini file
        if (!is_dir("/home/$owner/$domain/logs/")){
            if(!mkdir("/home/$owner/$domain/logs/",0750, true)) die("could not create directories: /home/$owner/$domain/logs/. \n");
	    }
        //`mkdir "/home/$owner"`;
	    //`mkdir "/home/$owner/$domain"`;
        //`mkdir "/home/$owner/$domain/logs/"`;
        //`touch "/home/$owner/$domain/logs/"$domain"_accesses.log"`;
        //`touch "/home/$owner/$domain/logs/"$domain"_errors.log"`;
        if(!touch("/home/$owner/$domain/logs/".$domain."._accesses.log") ) die("could not create log files: ".$domain."_accesses.log. \n");
        if(!touch("/home/$owner/$domain/logs/".$domain."_errors.log") ) die("could not create log files: ".$domain."_errors.log. \n");

        if (!is_dir("/home/$owner/$domain/configurations/httpd/domains")){
            if(!mkdir("/home/$owner/$domain/configurations/httpd/domains",0750, true)) die("could not create directories: /home/$owner/$domain/configurations/httpd/domains .\n");
        }
        //`mkdir "/home/$owner/$domain/configurations"`;
        //`mkdir "/home/$owner/$domain/configurations/httpd"`;
        //`mkdir "/home/$owner/$domain/configurations/httpd/domains"`;
        if(!copy($this->cwd.$this->conf, "/home/$owner/$domain/configurations/httpd/domains/".$this->conf)) die("could not copy ".$this->conf." to /home/$owner/$domain/configurations/httpd/domains/ .\n");
        //`cp $this->conf "/home/$owner/$domain/configurations/httpd/domains/"`;
        if(!readlink("/etc/httpd/conf/webapps.d/".$this->conf)){
            if(!symlink("/home/$owner/$domain/configurations/httpd/domains/$this->conf", "/etc/httpd/conf/webapps.d/".$this->conf)) die("could not create symbolic link /etc/httpd/conf/webapps.d/".$this->conf. "\n");
        }
        //`ln -s /home/$owner/$domain/configurations/httpd/domains/$this->conf /etc/httpd/conf/webapps.d/$this->conf`;
	}

	/*
	* switch ONLY the right side (values side of the ini file
	*/
	function prepareINIValues(){
        //TODO: move array to constructor
		$keyvalpairArray = array(
            "USER"   => $this->owner,
			DOMAIN	=> $this->domain,
			EXT		=> $this->ext_domain
		);
		$file = new File($this->getINI());
		$file->searchAndReplace($keyvalpairArray);
	}

    function loadINIValues(){
        $ini = $this->getINI();
        if($ini){
            $this->iniParameters = File::getINIValues($ini);
        }else{
            echo "the ini file: <$ini> could not be loaded.\n";
        }
    }

	function prepareConf(){
		//if the domain value passed in is a file use this vhost file for configuration
		if(is_file($this->domain.".conf")){
			$this->setConf($this->domain.".conf");
			echo "conf file exists no need to copy then search and replace values\n";
			//TODO: existing build only update
			//parse the found file for build parameters and search and replace
		}else{
			//domain value passed in does NOT yet have a config vhost file entry try to copy from the conf template default value
			//may want to connect to database for this as well
            if ($this->owner != "softrockit"){
                if (!$this->isValidExt($this->domain)){
                    die("domain extension is not support/invalid. domain: $this->domain \n");
                }
            }

            if (copy($this->conf, $this->domain.".conf") ){
                echo "created new conf file from template\n";
                $this->setConf($this->domain.".conf");
            }else{
                die("could not copy $this->conf to {$this->domain}.conf \n");
            }
        }


		//apache conf vhost file is created now search and replace with new values
		//setup assoc array to replace values inside of vhost entry
		$uri = new URI($this->domain);
		$this->base = $uri->getBase();

		$this->ext_domain = $uri->getExtension();
		$keyvalpairArray = array(
            "USER"    => $this->owner,
			DOMAIN  => $this->base,
			EXT 	=> $this->ext_domain
		);
		$file = new File($this->cwd.$this->getConf());
		$file->searchAndReplace($keyvalpairArray);
	}

	function processINIParametersAsFunctions(){
		foreach($this->iniParameters as $key=>$val){
			$this->$key();
		}
	}

	abstract public function run();


	function IP_ADDRESS(){
		//echo "do ip address work";
	}

	function SITE_FRWK(){
		if ( isset($this->iniParameters[SITE_FRWK]) && isset($this->iniParameters[SITE_TARGET]) ){
			$framework = $this->iniParameters[SITE_FRWK];
			$destination = $this->iniParameters[SITE_TARGET];
			echo "rsync -avur --exclude=.svn  $framework $destination\n";
			`rsync -avur --exclude=.svn  $framework $destination`;
		}
	}

	function finalize(){
		$owner = $this->iniParameters[SITE_OWNER];
		$this->TraverseDirSetPermissions($this->iniParameters[SITE_TARGET]);
		$group = $this->iniParameters[SITE_GROUP];
		$destination = $this->iniParameters[SITE_TARGET];
		echo "\nchown $owner.$group $destination -R\n";
		`chown $owner.$group /home/$owner -R`;
		echo "\n...[done]... enjoy your new ".$this->domain." web site.\n";
		//restart httpd
	}

	function SITE_SRC(){
		if( isset($this->iniParameters[SITE_TARGET]) ){
			$siteSrc = $this->iniParameters[SITE_SRC];
			$destination = $this->iniParameters[SITE_TARGET];
			echo "rsync -avur --exclude=.svn  $siteSrc $destination\n";
			`rsync -avur --exclude=.svn  $siteSrc $destination`;
		}else{
            die("could not find SITE_TARGET in the ini file.\n");
        }
	}

	function SITE_TARGET(){

	}

	function SITE_OWNER(){

	}

	function SITE_TLD(){

	}

	function SITE_GROUP(){

	}

	function SITE_DATABASE(){

	}

	function prepareINIFile(){
		if ( is_file($this->domain.".ini") ){
			echo "ini file found no need to copy from a template.\n";
			$this->setINI($this->domain.".ini");

		}else{
			$this->setINI($this->ini);//set from child class
			echo "no domain based ini file found try to copy from child class template based ini file. \n";

			if (copy($this->ini, $this->domain.".ini") ){
				echo "created new ini file from template ini file name < ".$this->domain.".ini >\n";
				$this->setINI($this->domain.".ini");
			}else{
				die("could not copy $this->ini to ".$this->domain.".ini \n");
			}
		}
	}

	//gets the apache vhost config file from concrete child class
	function getConf(){
		if (!isset($this->conf)) die("child class must set a conf file and variable\n");
		return $this->conf;
	}

	function getINI(){
		if (!isset($this->ini)) die("child class must set an INI file and variable\n");
		return $this->ini;
	}

	function setINI($ini){
		$this->ini = $ini;
	}

	public function setConf($conf){
		$this->conf = $conf;
	}

	//expects the type to be passed in
	//BUT if the passed in value is in this format key=value .. it will set the value to be the type of build
	function setType($buildType){
		if($buildType){
			list($key, $value) = split('=', $buildType);
			if (strtolower($key) == "type" && $value){
				$this->type = $value;
			}elseif($buildType){
				$this->type = $buildType;
			}else{
				$this->type = null;
			}
		}
	}

    //expects the onwer to be passed in
    //BUT if the passed in value is in this format key=value .. it will set the value to be the owner
    function setOwner($owner){
        if($owner){
            list($key, $value) = split('=', $owner);
            if (strtolower($key) == "owner" && $value){
                $this->owner = $value;
            }elseif($owner){
                $this->owner = $owner;
            }else{
                $this->owner = null;
            }
        }
    }

	function setCLIParameters($argv){
        //TODO: update to loop on all args and parse correctly
		$this->domain = isset($argv[1]) ? $argv[1] : ''; //this will also be the DOMAIN NAME possibly user name ... may need to convert to softrockit.com/chadwalling
 		$type = isset($argv[2]) ? $argv[2] : '';
 		$owner = isset($argv[3]) ? $argv[3] : '';

        if (empty($this->domain)){
            die("first param must be domain you wish to build. \n");
        }

        if($type){
			$this->setType($type);
		}else{
			die("no build type specified.\n");
		}
        if ($owner){
            $this->setOwner($owner);
        }else{
            die("third param must be owner.\n");
        }
	}


	function isValidExt($file){
		$ext = substr(strrchr($file,'.'),1);
		$validExt = array('com','org','net','biz','info','name','aero','biz','info','jobs','museum','name');
		if(in_array($ext, $validExt)){ return $ext;}
		return false;
	}

	function TraverseDirSetPermissions($dir, &$filesArray = array())
	{
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (substr($file, -1) == "~")
					{
						$path = File::FixPaths($dir, $file);
						echo "rm -f $path\n";
						`rm -f $path`;
						continue;
					}
					if ( ($file != "." && $file != ".." ) )
					{
						if (is_dir(''.$dir.'/'.$file.''))
						{
							$path = File::FixPaths($dir, $file);

							echo "chmod 750 $path\n";
							$str = preg_replace('/(\s+)/', '\\\$1', $path);
							//$cmd = escapeshellcmd("chmod 750 $str");
							exec("chmod 750 $str");
							if($file == "compiledtags" || $file == "compiledtags/" || $file == "/compiledtags" || $file == "/compiledtags/") exec("chmod 770 $str");
							$files = $this->TraverseDirSetPermissions($path, $filesArray);
						}
						else
						{
							$path = File::FixPaths($dir, $file);
							echo "chmod 640 $path\n";
							$str = preg_replace('/(\s+)/', '\\\$1', $path);
							//$cmd = escapeshellcmd("chmod 640 $str");
                            if(preg_match('/compiledtags/i', $str)){
                                exec("chmod 660 $str");
                            }else{
							    exec("chmod 640 $str");
                            }
							$filesArray[] = $path;
						}
					}
				}
				closedir($dh);
			}else{
				die("directory <$dir> is not readable \n.");
			}
		}
		else
		{
			echo "chmod 640 $dir\n";
			`chmod 640 $dir`;
			$filesArray[] = $dir;
		}
	}


	function isTypeCommandValid($type){
		$ret = false;
		$types = array(WORDPRESS,'delete', FRWK, FREE);
		if (in_array($type, $types)){
			$ret = true;
		}
		return $ret;
	}

	function delete(){
		`userdel $this->owner`;
		if(is_dir("/home/src/".$owner)){
			`rm -frv /home/src/$owner/`;
			`rm -f /etc/httpd/conf/webapps.d/$owner.com.conf`;
		}
		//TODO: delete user from db
        //delete target
        //delete database
	}

}
?>
