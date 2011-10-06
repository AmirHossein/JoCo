/******************************************
JoCo permissions
Author : AHHP ~ Boplo.ir
*******************************************/

// get user permissions data who selected in "existUsers" select tag and display as table.
function getPermissions(id)
{	
	if( typeof id == "undefined" || id == "" )	return false;
	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?id=" +mKey+ "&permission=1&getPermissions=" + id, true);
	xmlHttp.onreadystatechange=function() 
	{document.getElementById("dataTable").innerHTML  = xmlHttp.responseText;
		if(xmlHttp.readyState==1)
			document.getElementById("dataTable").innerHTML  = '<p><img src="' +sniPath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
			document.getElementById("dataTable").innerHTML  = xmlHttp.responseText;
	}
	xmlHttp.send(null);
}

// save permissions that changed by user. the permissions has come from getPermissions() output.
function setPermissions(id)
{
	var query = "&publishPermi=" + ((document.setPermissions.publishPermi[0].checked) ? "1" : "0");
	query += "&unpublishPermi=" + ((document.setPermissions.unpublishPermi[0].checked) ? "1" : "0");
	query +=  "&editPermi=" + ((document.setPermissions.editPermi[0].checked) ? "1" : "0");
	query += "&removePermi=" + ((document.setPermissions.removePermi[0].checked) ? "1" : "0");
	query += "&loggingPermi=" + ((document.setPermissions.loggingPermi[0].checked) ? "1" : "0");
	query += "&viewAllPermi=" + ((document.setPermissions.viewAllPermi[0].checked) ? "1" : "0");
	query += "&viewPublishedPermi=" + ((document.setPermissions.viewPublishedPermi[0].checked) ? "1" : "0");
	query += "&viewUnpublishedPermi=" + ((document.setPermissions.viewUnpublishedPermi[0].checked) ? "1" : "0");
	query += "&ipPermi=" + ((document.setPermissions.ipPermi[0].checked) ? "1" : "0");
	query += "&webUsersPermi=" + ((document.setPermissions.webUsersPermi[0].checked) ? "1" : "0");
	query += "&searchPermi=" + ((document.setPermissions.searchPermi[0].checked) ? "1" : "0");
	query += "&permissionPermi=" + ((document.setPermissions.permissionPermi[0].checked) ? "1" : "0");
	query += "&summaryPermi=" + ((document.setPermissions.summaryPermi[0].checked) ? "1" : "0");
	query += "&defaultThemePermi=" + ((document.setPermissions.defaultThemePermi[0].checked) ? "1" : "2");
	query += "&changeThemePermi=" + ((document.setPermissions.changeThemePermi[0].checked) ? "1" : "0");
	
	query += "&defaultViewPermi=" + document.setPermissions.defaultViewPermi.value;
	query += "&summaryResPerPagePermi=" + document.setPermissions.summaryResPerPagePermi.value;
	query += "&resPerPagePermi=" + document.setPermissions.resPerPagePermi.value;
	
	query += "&createdDocsPermi=" + 
		((document.setPermissions.own0createdDocsPermi.checked) ? "0" : 
			((document.setPermissions.own1createdDocsPermi.checked) ? "1" : "") + 
			((document.setPermissions.own2createdDocsPermi.checked) ? "2" : "") + 
			((document.setPermissions.own3createdDocsPermi.checked) ? "3" : "") + 
			((document.setPermissions.own4createdDocsPermi.checked) ? "4" : "")
		);
	query += "&publishedDocsPermi=" + 
		((document.setPermissions.own0publishedDocsPermi.checked) ? "0" : 
			((document.setPermissions.own1publishedDocsPermi.checked) ? "1" : "") + 
			((document.setPermissions.own2publishedDocsPermi.checked) ? "2" : "") + 
			((document.setPermissions.own3publishedDocsPermi.checked) ? "3" : "") + 
			((document.setPermissions.own4publishedDocsPermi.checked) ? "4" : "")
		);		
	query += "&editedDocsPermi=" + 
		((document.setPermissions.own0editedDocsPermi.checked) ? "0" : 
			((document.setPermissions.own1editedDocsPermi.checked) ? "1" : "") + 
			((document.setPermissions.own2editedDocsPermi.checked) ? "2" : "") + 
			((document.setPermissions.own3editedDocsPermi.checked) ? "3" : "") + 
			((document.setPermissions.own4editedDocsPermi.checked) ? "4" : "")
		);
			

	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?id=" +mKey+ "&permission=1&setPermissions=" + id + query, true);
	
	xmlHttp.onreadystatechange=function() 
	{
		if(xmlHttp.readyState==1)
			document.getElementById("dataTable").innerHTML  = '<p><img src="' +sniPath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
			document.getElementById("dataTable").innerHTML  = xmlHttp.responseText;
	}
	xmlHttp.send(null);
}


// move selected user. add from all users to permissions table and remove from permissions table.
function moveUser(dir, pos)
{
	if(dir == 'in')
	{
		if( !confirm(lang_move_in_confirm) )	return;
		
		if(pos == "mgr")
			var id = document.permission.mgrallUsers.value;
		if(pos == "web")
			var id = document.permission.weballUsers.value;
	}
	
	if(dir == 'out')
	{
		if( !confirm(lang_move_out_confirm) )	return;
		
		if(pos == "mgr")
			var id = document.permission.mgrexistUsers.value;
		if(pos == "web")
			var id = document.permission.webexistUsers.value;
	}
	
	if(!id)
	{
		confirm(lang_choose_a_user);
		exit();
	}
	
	if( typeof id == "undefined" || id == "" )	return false;
	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?id=" +mKey+ "&permission=1&dir=" + dir + "&moveUser=" + id, true);
	xmlHttp.onreadystatechange=function() 
	{document.getElementById(pos + "existUsers").innerHTML = xmlHttp.responseText;
		if(xmlHttp.readyState==1)
			document.getElementById(pos + "existUsers").innerHTML = '<p><img src="' +sniPath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
			document.getElementById(pos + "existUsers").innerHTML = xmlHttp.responseText;
	}
	xmlHttp.send(null);
	setTimeout("refreshList('"+pos+"')",3000);
	
}


// refresh exist <select> list
function refreshList(pos)
{
	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?id=" +mKey+ "&permission=1&refreshList=" +pos, true);
	xmlHttp.onreadystatechange=function() 
	{
		if(xmlHttp.readyState==1)
			document.getElementById(pos + "existUsers").innerHTML  = '<p><img src="' +sniPath+ 'images/loading.gif" />' +lang_loading+ '</p>';

		if(xmlHttp.readyState==4 && xmlHttp.status==200)
			document.getElementById(pos + "existUsers").innerHTML  = xmlHttp.responseText;
	}
	xmlHttp.send(null);
}


// enable and disable action: "moveIn", "moveOut", and "getPermissions".
function changeEl(src, pos)
{
	if(src == "exists") // the exists user has been selected. it enables "moveOut" & "getPermissions" and disables "moveIn".
	{
		document.getElementById("moveIn").innerHTML = '<img src="'+sniPath+'images/right_arrow_dis.png" alt="'+lang_add_user+'" />';
		document.getElementById("moveOut").innerHTML = '<a href="javascript:moveUser(\'out\',\''+pos+'\');"  title="'+lang_remove_user+'"><img src="'+sniPath+'images/left_arrow.png" alt="'+lang_remove_user+'" /><a/>';

		if(pos == "mgr")
			var res = document.permission.mgrexistUsers.value;
		if(pos == "web")
			var res = document.permission.webexistUsers.value;
		
		document.getElementById("getPermi").innerHTML = '<a href="javascript:getPermissions(\''+res+'\');" title="'+lang_get_permissions+'"><img src="'+sniPath+'images/process.png" alt="'+lang_get_permissions+'" /></a>';
	}
	
	if(src == "all") // the exists user has been selected. it disables "moveOut" & "getPermissions" and enables "moveIn".
	{
		document.getElementById("moveIn").innerHTML = '<a href="javascript:moveUser(\'in\',\''+pos+'\');" title="'+lang_add_user+'"><img src="'+sniPath+'images/right_arrow.png" alt="'+lang_add_user+'" /><a/>';
		document.getElementById("moveOut").innerHTML = '<img src="'+sniPath+'images/left_arrow_dis.png" alt="'+lang_remove_user+'" />';
		document.getElementById("getPermi").innerHTML = '<img src="'+sniPath+'images/process_dis.png" alt="'+lang_get_permissions+'" />';
	}
}