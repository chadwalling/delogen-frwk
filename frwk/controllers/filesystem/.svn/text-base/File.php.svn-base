<?
/// Copyright (C) 2007 Delogen
/// author: Chad Walling
/// email: chad@delogen.com

class File
{
    var $base = '';
    var $ext = '';
    var $file;

	public function __construct($file){
		if (is_file($file)){
			$this->file = $file;
		}else{
            error_log("parameter file: < $file > is not a valid file. \n");
        }
    }

    function ext($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    function base($file)
    {
        return pathinfo($file, PATHINFO_BASENAME);
    }

	function getFilesByDir($dir, &$files)
	{
		$files = array();
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if ( $file != "." && $file != ".." )
					{
						if (is_dir($file))
						{
							$this->getFilesByDir($file, $files);
						}
						else
						{
							$files[] .= $file;
						}
					}
				}
				closedir($dh);
			}
		}
		else
		{
			echo "No files in this dir";
		}
		return $files;
	}

    function getAllChildDirs($dir, &$dirChildren)
    {
            if (is_dir($dir))
            {
                    if ($dh = opendir($dir))
                    {
                            while (($file = readdir($dh)) !== false)
                            {
                                    if ($file != "." && $file != "..")
                                    {
                                            if (is_dir($dir.$file))
                                            {
                                                    $dirChildren[]= $dir.$file;
                                                    $this->getAllChildDirs($dir.$file, $dirChildren);
                                            }
                                    }
                            }
                            closedir($dh);
                    }
                    else
                    {
                            echo "\ncant open dir: $dir for reading";
                    }
            }
            else
            {
                    echo "\nNo child dir in this $dir";
            }
            return $dirChildren;
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

		return $path1.$separatorChar.$path2;
	}

	//takes a file (needs to be full path and then assoc array and replaces values in file with matching keys to the new values
	function searchAndReplace($keyvalpairArray, $file = ''){
		if (!$file ) $file = $this->file;
		foreach ($keyvalpairArray as $key=>$val){
			`perl -p -i -e s/$key/$val/g $file`;
		}
	}

	//expects file to be a full path or to be in the relative dir of the php script that is running
	function getINIValues($file = ''){
		if (!$file) $file = $this->file;
		if($file){
			return parse_ini_file($file);
		}
	}
}

?>
