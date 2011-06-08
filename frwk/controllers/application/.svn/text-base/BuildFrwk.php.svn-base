<?php
chdir($cwd = dirname(__FILE__));
require_once('../Main.php');

Render::Controller("application/abstractBuild.php");

class BuildFrwk extends abstractBuild{

    public $conf = "template_frwk.conf"; //apache vhost conf file
    public $ini  = "template_frwk.ini";

    function __construct($argv){
        parent::__construct($argv);
    }
    
    function run(){
        $this->finalize();
        `apachectl graceful`;
    }

}    

$BuildFrwk = new BuildFrwk($argv);
$BuildFrwk->run();
?>