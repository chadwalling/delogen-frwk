<script type="text/javascript">
var aItems =
[
	{
		text: "Communication",
		url: "http://communication.yahoo.com",
		submenu:
		{
			id: "communication"
		}
	}

];

var oMenu = new YAHOO.widget.Menu("productsandservices", { fixedcenter: true });

oMenu.addItems(aItems);

oMenu.showEvent.subscribe(function () 
{
    this.focus();

});

oMenu.render("rendertarget");

</script>

