<?php

/**
* @desc
* TODO: this is an example file of how to customize your front end controller the abstract class needs to be updated to accomodate programmer customization better
* typically instead of calling, you could copy and paste the contents from abstactMain::run(); into the Main class and begin customization of the front controller
* instruction you can rename this file to match the site it is running for which must match the include inside your Main.php
* example. domain name of site is www.linux.org naming convention could be class LinuxMain
*/

require_once(dirname(__FILE__).'/AbstractMain.php');

class Main extends AbstactMain
{
    public function __construct()
    {
        AbstactMain::__construct();
    }

    /**
    * @desc This Run function will run your front controller customizations
    * Also should run all http request files.
    * From within this scope all pages will have access to request and session objects this allows simplicity and control over creating new
    * web sites and web pages
    */
    public function run()
    {
        /** TODO: add caching capabilities
        $daysToCache  = 1.5;
        $cacheMaxAge  = ${daysToCache}*24*(60*60);
        $layoutFile   = '/home/justimagine/views/layout_main.php';
        $config = new AppConfig();


        $page         = $pageMappings->getPage( $requestedURI );
         if( is_null( $page ) )
         {
             header( 'HTTP/1.0 404 Not Found' );
             $page = $pageMappings->getNotFoundPage();
         }

         if( ! headers_sent() )
         {
             header(
                 'Cache-Control: max-age=' . ${cacheMaxAge} . ',
                 must-revalidate'
             );
             header(
                 'Last-Modified: ' . gmdate('D, d M Y H:i:s' ,
                 $page->getLastModified() ) . ' GMT'
             );
         }
         //print_r($page);
         include( $layoutFile );
        **/
        global $request;
        global $session;

        $request = new Request();
        $uri = new URI();
        $session = new Session();
        $requestedURI = $uri->getPath();
        if ($requestedURI == URI_NO_PATH){
            $requestedURI = null;
        }else{
            $requestedURI = $requestedURI[0];
        }

    /**
        * @desc  All code below this line is custom to application. Here is where you would CUSTOMIZE your front controller for application specific requirements.
        * EX: custom global URI keys v = view and c = controller and m = model change these globals here if desired.
        * inside this main->run() function will setup system wide propertiers easily for specifc REST services or for URL rewrite, manguling, and manipulation
        */
        $view         = ($uri->URIKeyExist("v")) ? $uri->getURIQueryValueByKey("v") : "";
        $controller   = ($uri->URIKeyExist("c")) ? $uri->getURIQueryValueByKey("c") : "";
        $model        = ($uri->URIKeyExist("m")) ? $uri->getURIQueryValueByKey("m") : "";

       // $view = 'admin/index.php'; //example used to debug with out having to run through a browser request...emulates a browser http request.

       //TODO: create permission controller and authentication controller
       //function authenticator($requestedURI, $session)
       //{

       $isAdminPageRequested = strpos(strtolower($requestedURI), SESSION_PROTECTED_DIR);
       $isUserPageRequested = strpos(strtolower($requestedURI), "user");

       $isAdminQueryRequested = strpos(strtolower($uri->uri), SESSION_PROTECTED_DIR);
       $isUserQueryRequested = strpos(strtolower($uri->uri), "user");

       if ($isAdminPageRequested !== false || $isUserPageRequested !== false || $isAdminQueryRequested !== false || $isUserQueryRequested !== false){
            if (Session::getid() == ""){
                $requestedURI = "login"; //send to login for authentication challenge
                $view = "";
                $controller = "";
                //error_log("no session id found \n");
            }else{
                //check security level from session and restrict access
                if (Session::getLoginType() != 1 && ($isAdminPageRequested !== false || $isAdminQueryRequested !== false) ){
                    //error_log("sess login type: ".Session::getLoginType()."\n");
                    //error user login type does not have access to any admin URI requests. send to login page for re-auth challenge
                    //TODO: maybe show error for access denied
					$view = "";
					$controller = "";
                	$requestedURI = "login";
                }
            }
       }

        $render = new Render();
        //TODO: make default controller to render for each page
        // maybe Class PageController_filename
        if(!empty($controller)){
            $render->Controller($controller);
        }elseif ($requestedURI == "/" && $view === ""){
            $render->View("index.php");
        }elseif ($view !== ""){
            $render->View($view);
        }elseif(!empty($requestedURI)){
            //TODO: if php_sapi_name() === 'cli'... then adjust
            //TODO: wrap in try catch is there is a an issue send to proper location for error handling.
            $render->View($requestedURI);
        }
    }
}
?>
