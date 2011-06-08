<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Justimagine Photography Gallery</title>
<?
Render::View("/css/gallery.php"); //css file
Render::View("/js/utils.js");
?>
<script type="text/javascript" src="js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="js/yui/event/event.js"></script>
<script type="text/javascript" src="js/yui/dom/dom.js"></script>
<script type="text/javascript" src="js/yui/animation/animation.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>
<body bgcolor="black">
<script type="text/javascript">

scrollIt = function(val, id)
{
	var anim = new YAHOO.util.Scroll('thumbnails', { scroll: { by: [0, val] } });
	YAHOO.util.Event.on(id, 'click', anim.animate, anim, true);
};

YAHOO.util.Event.onAvailable('scrollUp', scrollIt);
YAHOO.util.Event.onAvailable('scrollDown', scrollIt);

</script>
<?
Render::View('/admin/gallery_form.php');

?>
</body>
</html>
