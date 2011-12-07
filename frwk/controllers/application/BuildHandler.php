#!/usr/bin/php -q
<?php
/*
* This is the Domain Queue handler it pulls domains that need to run the build 
* scripts and create web sites based on web site type. Ex: wordpress, drupal, joomla, custom
*/
require_once('../Main.php');
require_once 'System/Daemon.php';
$options = array(
    'appName' => 'BuildHandler',
    'appDir' => dirname(__FILE__),
    'sysMaxExecutionTime' => '0',
    'sysMaxInputTime' => '0',
    'sysMemoryLimit' => '1024M',
);

System_Daemon::setOptions($options);
System_Daemon::start();
while (!System_Daemon::isDying()){
    $domainq = new Objects("domainsqueue");
    $domainq->fetch();
    sleep(4);
    for($i = 0; $i < $domainq->getCount(); $i++){
        $d   = $domainq->getRow($i);
        $domainID = $d['domain_id'];
        if(!empty($domainID)){
            $domains = new Objects("domains");
            $c_d     = new Objects("classes_domains");
            $class   = new Objects("classes");
            $c_d->setdomain_id($domainID);
            $c_d     = $c_d->fetch();
            $c_id    = $c_d->getClass_id();
            $class->setclass_id($c_id);
            $class   = $class->fetch();
            $owner   = $class->getname1();

            $domains->setDomain_id($domainID);
            $buildD = $domains->fetch();
            $domaintype   = $buildD->getdomaintype_id();
            $domain_name = $buildD->getDomain();

            if(!is_null($domaintype) && !is_null($domain_name)){
                $script = "BuildFrwk.php";
                $type   = "frwk";
                if($domaintype == 1){ //wordpress
                    $script = "BuildWordPress.php";
                    $type = "wordpress";
                }elseif($domaintype == 3){
                    $script = "BuildWordPress.php";
                    $owner = "softrockit";
                    $type = "wordpress";
                }

                echo "php $script $domain_name type=$type owner=$owner > buildlog \n";
                `php $script $domain_name type=$type owner=$owner > buildlog`;
                $buildD->sql("DELETE FROM domainsqueue where domain_id=$domainID");
                sleep(2);
            }
        }
    }

}

?>
