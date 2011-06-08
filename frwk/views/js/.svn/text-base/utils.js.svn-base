<script type="text/javascript">

function getElementByAJAX(method, structure, content, url, width, height, formID)
{
	//alert(url);
	var formObject = document.getElementById(formID);
	YAHOO.util.Connect.setForm(formObject);
//	YAHOO.util.Connect.setForm(formObject, true);
//	width  = (width === null) ? "" : width;
//	height = (height === null) ? "" : height;
	// handles replacing the ticker on page.
	//todo handle time out variable
	var callback    = { success:updateElementByAJAXCallBack, width:width, height:height, structure:structure, content:content, failure:responseFailure, timeout: 15000 };
	var cObj = YAHOO.util.Connect.asyncRequest(method, url, callback);
}

var responseFailure = function(o)
{
	//Don't need to do anything at this point in time!
};


/**
	@Description- if a successful response is returned from an AJAX request made by getElementByAJAX this function will swap the returned data 
	with the DOM data contained by the "content" variable passed in to getElementByAJAX.
*/
function updateElementByAJAXCallBack(o)
{
	alert(o.responseText);
	var responseText      = o.responseText; //assumes responseText is html
	AJAXStructure         = document.getElementById(this.structure);
	var deleteReplaceNode = document.getElementById(this.content);
	delElement(deleteReplaceNode);
	newAJAXContentDiv    = document.createElement("div"); //assumes the content node to be replaced was a DIV element
	newAJAXContentDiv.id = this.content;

	if (this.width !== "" && (this.width.indexOf("px") !== -1 || this.width.indexOf("%") !== -1))
	{
		replacementDiv.style.width  = this.width;
	}
	
	if (this.height !== "" && (this.height.indexOf("px") !== -1 || this.height.indexOf("%") !== -1))
	{
		replacementDiv.style.height = this.height;
	}

	newAJAXContentDiv.innerHTML = responseText;
	AJAXStructure.appendChild(newAJAXContentDiv);
}

function scrollPositionDown(id, px)
{
// 	el = document.getElementById(id);
// 	var top = el.scrollTop;
// 	//document.getElementById(id).scrollTop=(top + px);
// 	
//    var attributes = {
//       scroll: { to: [YAHOO.util.Dom.get('thumbnails').scrollLeft, 200] }
//    };
//    
//    var anim = new YAHOO.util.Scroll('thumbnails', attributes);
//    //YAHOO.util.Event.on('thumbnails', 'click', anim.animate, anim, true);
//    //var anim2 = new YAHOO.util.Scroll('thumbnails', attributes2);
//    anim.animate;
}


function scrollPositionUp(id, px)
{
// 	el = document.getElementById(id);
// 	var top = el.scrollTop;
// 	//document.getElementById(id).scrollTop=(top - px);
//     
// 	var attributes =
// 	{
// 		scroll: { to: [YAHOO.util.Dom.get('thumbnails').scrollLeft, -400] }
// 	};
//    var anim = new YAHOO.util.Scroll('thumbnails', attributes);
//    //var anim2 = new YAHOO.util.Scroll('thumbnails', attributes2);
//    anim.animate;
   //YAHOO.util.Event.on("scrollUp", 'click', anim.animate, anim, true);
   //YAHOO.util.Event.on("scrollDown", 'click', anim.animate, anim2, true);

}

function moveFromTo(idsource, idtarget)
{
	el = document.getElementById(idsource);
	el.style.position = "absolute";
	el.style.zIndex= "1";
	var anim = new YAHOO.util.Motion(idsource, { points: { to: YAHOO.util.Dom.getXY(idtarget) } });
	//var greenRocket = new YAHOO.util.Motion(symbol+'high', { points: { to: [''+x+'',600] } }, 2);
	anim.animate();
}

function changeImage(id, imgSrc, sourceid)
{
	div = document.getElementById(id);
	div.innerHTML = '';
	div.style.MozOpacity = .0;
	div.style.visibility = "hidden";
	
	img=document.createElement('img');
	img.id = "fullImg";
	img.src=imgSrc;

	div.appendChild(img);
	div.style.visibility = "visible";
	
	var anim = new YAHOO.util.Anim(id, { opacity: { from: 0, to: 1 } }, 1, YAHOO.util.Easing.easeIn);
	anim.animate();

}

</script>
