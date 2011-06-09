<html>
<head>
<script type="text/javascript" src="/views/js/utilities.js"></script>
</head>
<body>

<?php
$gallery = '';
$dir = '';
$xml = '';
$gallery = strtolower($_REQUEST['gallery']);
$dir = "images/".$gallery;
$thumbsDir = "images/thumbs";

if (is_dir($thumbsDir))
{
    if ($dh = opendir($thumbsDir))
    {
        //$xml = "<gallery>";
        while (($file = readdir($dh)) !== false)
        {
                    if ( $file != "." && $file != ".." )
                    {
                        $xml .= '<image title="'.$file.'" main="'.$dir.'/'.$file.'" />';
                }
        }
        closedir($dh);
       // $xml .= "</gallery>";
    }
}
else
{
        echo "No images in this gallery";
}


// $myFile = "gallery.xml";
// $fh = fopen($myFile, 'w') or die("can't open file");
// $stringData = $xml;
// fwrite($fh, $stringData);
// fclose($fh);

//echo $xml;
?>
</body>
</html>