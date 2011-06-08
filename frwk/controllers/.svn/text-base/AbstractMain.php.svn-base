<?php
 abstract class abstactMain
{
    //This is the default abstract main constructor to setup environments, classes/libraries to be ready for implementation for you application.
    public function __construct()
    {
        $this->init();
    }

    /**
    * @desc Init() initializes all required code and environment variables for this framework to run.
    */
    public function init()
    {
        $dir = dirname(__FILE__);
        require_once($dir.'/filesystem/Environment.ini');
        require_once($dir.'/filesystem/File.php'); //TODO this should be a model
        require_once($dir.'/filesystem/Render.php');
    
        $render = new Render();
        $render->Model('server/http/URI.php' );
        $render->Model('Objects.php');
        $render->Model("/server/http/Request.php");
        $render->Model("/server/Session.php");           
    }    
    
    /**
    * @desc Here is a default front controller it will render page requests and still have the global request and session object available for use on all pages
    */
    public function run()
    {
        global $request;
        global $session; 
        $request = new Request();         
        $uri = new URI();
        $requestedURI = $uri->getURI();  
        $render = new Render($requestedURI);
        $render->it();
    }                        
}
?>
