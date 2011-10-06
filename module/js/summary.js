/******************************************
JoCo subscriptions
Author : AHHP ~ Boplo.ir
******************************************/

// get subscriptions
function getSubscriptions(uparent, id)
{
	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?a=112&id=" + mKey + "&summary=1&getSubs=" + uparent, true);
	xmlHttp.onreadystatechange=function() 
	{
		if(xmlHttp.readyState==1)
			document.getElementById("subscriptions_" + id).innerHTML  = '<p><img src="' +baseUrl+modulePath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
		{
			document.getElementById("subscriptions_" + id).style.border = "2px solid pink";
			document.getElementById("subscriptions_" + id).innerHTML  = xmlHttp.responseText;
		}
	}
	xmlHttp.send(null);
}