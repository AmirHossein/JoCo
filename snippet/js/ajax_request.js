/*********************************
Module : JoCo (Jot Comments module) : ajaxRequest()
For version : all versions that nedd ajax
Author : www.ajax-learn-tutorial.com
*********************************/

function ajaxRequest(){
	try
	{
		var xmlhttp = new XMLHttpRequest();
	}
	catch(err1)
	{
		var ieXmlHttpVersions = new Array();
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.7.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.6.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.5.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.4.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp.3.0";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "MSXML2.XMLHttp";
		ieXmlHttpVersions[ieXmlHttpVersions.length] = "Microsoft.XMLHttp";

		var i;
		for (i=0; i < ieXmlHttpVersions.length; i++)
		{
			try
			{
				var xmlhttp = new ActiveXObject(ieXmlHttpVersions[i]);
				break;
			}
			catch (err2)
			{
				return false;
			}
		}
	}
	
	return xmlhttp;
}