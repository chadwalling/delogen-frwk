<?
//$config->getValue( 'LinkColor' );
//$config->getValue( 'HeaderFooterFGColor' );
// $config->getValue( 'HeaderFooterBGColor' );
//$config->getValue( 'BodyFGColor' );
//$config->getValue( 'BodyBGColor' )
?>


<HTML>
	<HEAD>
	<TITLE>Test Site: <?= $page->getTitle() ?></TITLE>

	<STYLE type="text/css">
	<!--

	BODY,TD,P,A {font-family:arial,sans-serif }

	A {
/* 		color:  ; */
		font-weight: bold ;
	}

	TD.header_footer {
/* 		color:; */
/* 		background-color: ; */
	}

	BODY {
		text-align: justify ;
/* 		color:   ; */
/* 		background-color: ; */
	}

	-->
	</STYLE>


	</HEAD>


<BODY>

<H2>Test Site: <?= $page->getTitle() ?></H2>

<TABLE WIDTH="100%">

<TR VALIGN="TOP">
<TD CLASS="header_footer">
<B>Menu:</B>
<?= $util->aliasLink( 'MainIndex' , 'Home' ) ?>
|
<?= $util->aliasLink( 'MainLeafOne' , 'First Leaf Page' ) ?>
|
<?= $util->aliasLink( 'MainLeafTwo' , 'Second Leaf Page' ) ?>
</P>
</TR>
</TABLE>


<!-- BEGIN: content -->

<?php include( $page->getFile() ) ?>

<!-- END: content -->


<TABLE WIDTH="100%">

<TR VALIGN="TOP">
<TD CLASS="header_footer">

<P>
<I>This site's content Copyright &copy; 2004 Some Company</I>
</P>

</TR>
</TABLE>

</BODY>

</HTML>
