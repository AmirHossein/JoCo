/******************************************
JoCo Jot Calls
Author : AHHP ~ Boplo.ir
******************************************/

// get calls
function getJotCalls(docId, chunk)
{
	var xmlHttp = ajaxRequest();
	
	if(chunk == 1)
		xmlHttp.open("GET" , "index.php?a=112&id=" + mKey + "&jotCall=1&chunks=1&chunkId=" + docId, true , "aa", "bb");
	else
		xmlHttp.open("GET" , "index.php?a=112&id=" + mKey + "&jotCall=1&docId=" + docId, true);
	
	xmlHttp.onreadystatechange=function() 
	{
		document.getElementById("calls_" + docId).style.display = "";
		
		if(xmlHttp.readyState==1)
			document.getElementById("calls_" + docId).innerHTML  = '<p><img src="' +baseUrl+modulePath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
		{
			document.getElementById("calls_" + docId).innerHTML  = xmlHttp.responseText;
			document.getElementById("control_" + docId).innerHTML = lang_jotcall_hide;
			document.getElementById("control_" + docId).setAttribute("href" , "javascript:closeCalls(" + docId + (chunk == 1 ? ",1":"") + ");");
		}
	}
	xmlHttp.send(null);
}



// show edit form
function goToEdit(pos, docId , item)
{
	document.getElementById(pos + "_inner_" + docId + "_" + item).style.display = "none";
	document.getElementById("edit_" + pos + "_" + docId + "_" + item).disabled = "disabled";
	
	var form = document.createElement("form");
	form.setAttribute("id" , pos + "_form_" + docId + "_" + item);

	var textarea = document.createElement("textarea");
	textarea.setAttribute("id" , pos + "_textarea_" + docId + "_" + item);
	textarea.className = "editArea";
	textarea.value = document.getElementById(pos + "_inner_" + docId + "_" + item).innerHTML;
	
	var save = document.createElement("a");
	save.setAttribute("id" , pos + "_save_" + docId + "," + item);
	save.setAttribute("href" , "javascript:saveIt('" + pos + "'," + docId + "," + item + ");");
	save.className = "editButton";
	save.innerHTML = lang_save;
	
	var cancel = document.createElement("a");
	cancel.setAttribute("id" , pos + "_cencel_" + docId + "," + item);
	cancel.setAttribute("href" , "javascript:cancel('" + pos + "'," + docId + "," + item + ");");
	cancel.className = "editButton";
	cancel.innerHTML = lang_cancel;
	
	var p = document.createElement("p");
	
	form.appendChild(textarea);
	form.appendChild(p);
	p.appendChild(save);
	p.appendChild(cancel);
	document.getElementById(pos + "_" + docId + "_" + item).appendChild(form);
}



// hide edit form (cancel)
function cancel(pos, docId , item)
{
	var form = document.getElementById(pos + "_form_" + docId + "_" + item);
	form.removeAttribute("id");
	form.style.display= "none";
	
	document.getElementById(pos + "_inner_" + docId + "_" + item).style.display = "";
	document.getElementById("edit_" + pos + "_" + docId + "_" + item).disabled = "";
}



// do edit
function saveIt(pos, sourceId, item)
{
	if( ! confirm(lang_are_you_sure_edit) )
		return;
	
	var oldCall = document.getElementById(pos + "_inner_" + sourceId + "_" + item).innerHTML;
	var newCall = document.getElementById(pos + "_textarea_" + sourceId + "_" + item).value;
	var position = pos;
	var sourceId = sourceId;
	var query = "&oldCall=" + encodeURIComponent(oldCall) + "&newCall=" + encodeURIComponent(newCall) + "&position=" + position + "&sourceId=" + sourceId;

	var xmlHttp = ajaxRequest();
	xmlHttp.open("POST" , "index.php?a=112&id=" + mKey + "&jotCall=1" + (pos=="chunk" ? "&chunks=1" : "") +"&saveIt=1", true);
	xmlHttp.onreadystatechange=function() 
	{
		if(xmlHttp.readyState==1)
			document.getElementById(pos + "_inner_" + sourceId + "_" + item).innerHTML  = '<p><img src="' +baseUrl+modulePath+ 'images/loading.gif" />' +lang_loading+ '</p>';
		
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
		{
			document.getElementById("edit_" + pos + "_" + sourceId + "_" + item).disabled = "";
			cancel(pos, sourceId, item);
			document.getElementById(pos + "_inner_" + sourceId + "_" + item).innerHTML = newCall;
		}
	}
	xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded;");
	xmlHttp.send(query);
}


// hide calls
function closeCalls(docId, chunk)
{
	document.getElementById("calls_" + docId).innerHTML = "";
	document.getElementById("calls_" + docId).style.display = "none";
	document.getElementById("control_" + docId).innerHTML = lang_jotcall_show;
	document.getElementById("control_" + docId).setAttribute("href" , "javascript:getJotCalls(" + docId + (chunk == 1 ? ",1":"") + ");");
}