/******************************************
JoCo search
Author : AHHP ~ Boplo.ir
*******************************************/

// gives IDs from form and gets names
function getNames(action)
{
	if(action == "doc") // from documents
	{
		var parents = "&searchParents=" + document.JoCoSearchForm.searchParents.value;
		var docGroups = "&searchDocGroups=" + document.JoCoSearchForm.searchDocGroups.value;
		var docs = "&searchDocs=" + document.JoCoSearchForm.searchDocs.value;
		var notDocs = "&searchNotDocs=" + document.JoCoSearchForm.searchNotDocs.value;
		var query = "&comment_search_submit=1&docTitles=1" + parents + docGroups + docs + notDocs;
		var stage = "getDocs";
		
		if( document.JoCoSearchForm.searchParents.value == "" && document.JoCoSearchForm.searchDocGroups.value == "" && document.JoCoSearchForm.searchDocs.value == "" && document.JoCoSearchForm.searchNotDocs.value == "" )
		{
			document.getElementById(stage + "Container").style.display = "block";
			document.getElementById(stage).innerHTML  = lang_fields_are_empty;
			return;
		}
	}
	
	if(action == "user") // from users
	{
		var webGroups = "&searchWebGroups=" + document.JoCoSearchForm.searchWebGroups.value;
		var users = "&searchUsers=" + document.JoCoSearchForm.searchUsers.value;
		var notUsers = "&searchNotUsers=" + document.JoCoSearchForm.searchNotUsers.value;
		var query = "&comment_search_submit=1&userNames=1" + webGroups + users + notUsers;
		var stage = "getUsers";
		
		if( document.JoCoSearchForm.searchWebGroups.value == "" && document.JoCoSearchForm.searchUsers.value == "" && document.JoCoSearchForm.searchNotUsers.value == "" )
		{
			document.getElementById(stage + "Container").style.display = "block";
			document.getElementById(stage).innerHTML  = lang_fields_are_empty;
			return;
		}
	}

	var xmlHttp = ajaxRequest();
	xmlHttp.open("POST" , "index.php?id=" +mKey+ "&search=1", true);
	

	xmlHttp.onreadystatechange=function() 
	{
		document.getElementById(stage + "Container").style.display = "block";
		
		if(xmlHttp.readyState==1)
			document.getElementById(stage).innerHTML  = '<p><img src="' +sniPath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
			document.getElementById(stage).innerHTML  = xmlHttp.responseText;
	}
	
	xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	xmlHttp.send(query);
}