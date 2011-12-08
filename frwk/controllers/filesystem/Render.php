<?php
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com

 /**
 * @desc Render includes all requested files with global request and session objects for programmer use.
 * Front controller page renderer
 */
class Render
{
    var $path = '';
    public $file = '';
    public function __construct($file = '')
    {
        if (!empty($file))
        {
            $this->file = $file;
            if ($this->isModel($file)) { $this->path = MODELS; }
            if ($this->isView($file)) { $this->path = VIEWS; }
            if ($this->isController($file)) { $this->path = CONTROLLERS; }
        }
    }

    function it($file = '')
    {
        if (empty($file)) { $file = $this->file; }
        if (!empty($file))
        {
            switch ($this->path)
            {
                case MODELS:
                    $this->Model($file);
                    return;
                case VIEWS:
                    $this->View($file);
                    return;
                case CONTROLLERS:
                    $this->Controller($file);
                    return;
                default:
                    error_log("file $file could not be found\n.");
            }
        }
    }

    function Model($file)
    {
        global $request;
        global $session;
	Render::setSuffix($file);
        if ($file){
            $file = BASE_PATH_MODELS.$file;
            if (is_file($file)){
                include_once($file);
            }else{
                error_log("function IncludeModel can not include requested file: ".$file);
                return false;
            }
        }
    }

    function View($file, $array = array())
    {
        global $request;
        global $session;
        if(count($array) > 0){
            if($request->hasValue("messages")){
                $request->addValues("messages", $array);
            }else{
                $request->addFields("messages");
                $request->addValues("messages", $array);
            }
        }

	Render::setSuffix($file);
        if ($file){
		$fileT = BASE_PATH_VIEWS.$file;
		$file_and_path = BASE_PATH_VIEWS.$file;

            	if (is_file($file_and_path)){
			$output = file_get_contents($file_and_path);
			preg_match_all('/<\/?(?P<ns>p):(?P<name>[A-Z]+[\w\.]*)(?P<attrs>(?:\s*[\w\.]+\s*=\s*\'.*?\'|\s*[\w\.]+\s*=\s*".*?"|\s*[\w\.]+\s*=\s*<%.*?%>)*)\s*\/?>/msS', $output, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
			//print_r($matches);
			foreach ($matches as $match) {
				$tag = $match[0][0];
				$name = $match['name'][0];
				$pos = $match[0][1];
				if (strpos($tag, '<p:')===0 && strpos(strrev($tag), '>/')!==0) {
					Render::setSuffix($name,"phtml");
					$phtml = dirname($file_and_path)."/".$name;
					//echo "initial file: ". $output. " <br />";
					if(is_file($phtml)){
						$replace = file_get_contents($phtml);
						$output = str_replace($tag, $replace, $output);
						$foundPhtml = true;
					}else{
						error_log("Waring p tag was found in $file_and_path but the matching phtml file: $phtml not found\n");
					}
				}
			}
			if($foundPhtml) {
				$compiled_tag_dir = BASE_PATH_VIEWS."compiledtags";
				// TODO:
				// 1.if cli script we need to process differently
				// 2.check if file compiled file exists if so compare to source file if the file we want to serve is older cache new file and serve the new file
				// 3.fix this to be relative path
				$file_and_path = File::FixPaths($compiled_tag_dir, $file);
				if(!is_dir(dirname($file_and_path))){
                        		if(mkdir(dirname($file_and_path), 0770, true)) error_log('Could not make dir ['.dirname($file_and_path).']. \n');
                    		}
				if (!file_exists($file_and_path) || true/*|| filemtime($fileT) > filemtime($file_and_path) */ ) {
					if(file_put_contents($file_and_path, $output) === false) error_log('File ['.$file_and_path.'] could not be written. \n');
		                }

			}
                	include_once($file_and_path);
            }else{
		error_log("function IncludeView can not include requested file: ".$file);
                //include_once(WEB_ROOT.$file);
                return false;
            }
        }
    }

    function Controller($file)
    {
        global $request;
        global $session;
	Render::setSuffix($file);
        if ($file)
        {
            $file = BASE_PATH_CONTROLLERS.$file;
            if (is_file($file))
            {
                include_once($file);
            }
            else
            {
                error_log("function IncludeController can not include requested file: ".$file);
                return false;
            }
        }
    }

    function isModel($file)
    {
        return is_file(BASE_PATH_MODELS.$file);
    }

    function isView($file)
    {
        return is_file(BASE_PATH_VIEWS.$file);
    }

    function isController($file)
    {
        return is_file(BASE_PATH_CONTROLLERS.$file);
    }

    function isControllerAction($file)
    {
        return is_file(BASE_PATH_CONTROLLERS_ACTIONS.$file);
    }

    function setSuffix(&$file, $suffix = DEFAULT_FILE_SUFFIX)
    {
        if (!File::ext($file))
        {
            $file = $file.".".$suffix;
        }
        return $file;
    }

}

?>
