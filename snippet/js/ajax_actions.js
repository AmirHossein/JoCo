/******************************************
JoCo permissions
Author : AHHP ~ Boplo.ir
/******************************************/

// main actions: publish, unpublish, edit and delete
function ajaxAction(action, id)
{
	if(action == "publish")	var conf = lang_are_you_sure_publish;
	if(action == "unpublish")	var conf = lang_are_you_sure_unpublish;
	if(action == "edit")	var conf = lang_are_you_sure_edit;
	if(action == "delete")	var conf = lang_are_you_sure_delete;
	
	if( ! confirm(conf) )
		return;
	
	var query = "";
	if(action == "edit")
	{
		var message = document.getElementById('textarea_' + id).value;
		for(var i=0; i < message.length; i++)
		{
			message = message.replace("\n","<BR>");
			message = message.replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;");
		}
		query = "&message=" + message;
	}
	var xmlHttp = ajaxRequest();
	xmlHttp.open("GET" , "index.php?id=" +mKey+ "&comment_action=" + action + "&comment_id=" + id + query, true);
	
	xmlHttp.onreadystatechange=function() 
	{
		if(xmlHttp.readyState==1)
		{
			document.getElementById("loading_" + id).innerHTML  = "<p><img src=\"" +sniPath+ "images/loading.gif\" />" +lang_loading+ "</p>";
			document.getElementById("indvTpl_" + id).innerHTML = document.getElementById("indvTpl_" + id).innerHTML;
		}
		if(xmlHttp.readyState==4 && xmlHttp.status==200)
		{
			document.getElementById("loading_" + id).innerHTML  = "";
			
			if(action == "publish")
			{
			// action message
				document.getElementById("done_" + id).innerHTML = '<div class="done">' +lang_published+ '</div>';
			// disable publish checkbox TD	
				document.getElementById("pub_col_"+id).style.backgroundImage="url('"+sniPath+"images/publish-col_dis.png')";
			//disable checkbox	
				document.getElementById("pub_chkbx_" + id).setAttribute("disabled","disabled");
			//enable upublish checkbox TD	
				document.getElementById("unpub_col_"+id).style.backgroundImage="url('"+sniPath+"images/unpublish-col.gif')";
			// remove unpublish "disabled" attribute
				document.getElementById("unpub_chkbx_" + id).removeAttribute("disabled");
			// uncheck publish checkbox
				if(document.getElementById("pub_chkbx_" + id).checked == true)
					document.getElementById("pub_chkbx_" + id).checked = false;
				
			// disable publish action link
				document.getElementById("act_publish_" + id).setAttribute("href","javascript:void(0)");
			// disable publish action image	
				document.getElementById("img_act_publish_" + id).setAttribute("src", sniPath + "images/publish_dis.png");
			// enable unpublish action link	
				document.getElementById("act_unpublish_" + id).setAttribute("href","javascript:ajaxAction('unpublish','"+id+"');");
			// enable unpublish action image
				document.getElementById("img_act_unpublish_" + id).setAttribute("src", sniPath + "images/unpublish.png");
			// set new status bg
				document.getElementById("message_"+id).style.backgroundImage="url('"+baseUrl+modulePath+"images/statusBg-published.png')";
				if(document.getElementById("status_"+id) != null)
					document.getElementById("status_"+id).innerHTML = lang_published;
			}
			
			if(action == "unpublish")
			{
				document.getElementById("done_" + id).innerHTML = "<div class=\"done\">" +lang_unpublished+ "</div>";
				document.getElementById("unpub_col_"+id).style.backgroundImage="url('"+sniPath+"images/unpublish-col_dis.png')";
				document.getElementById("unpub_chkbx_" + id).setAttribute("disabled","disabled");
				document.getElementById("pub_col_"+id).style.backgroundImage="url('"+sniPath+"images/publish-col.gif')";
				document.getElementById("pub_chkbx_" + id).removeAttribute("disabled");
				if(document.getElementById("unpub_chkbx_" + id).checked == true)
					document.getElementById("unpub_chkbx_" + id).checked = false;
				
				// disable Unpublish action
				document.getElementById("act_unpublish_" + id).setAttribute("href","javascript:void(0)");
				document.getElementById("img_act_unpublish_" + id).setAttribute("src", sniPath + "images/unpublish_dis.png");			
				// enable Publish action
				document.getElementById("act_publish_" + id).setAttribute("href","javascript:ajaxAction('publish','"+id+"');");
				document.getElementById("img_act_publish_" + id).setAttribute("src", sniPath + "images/publish.png");
				document.getElementById("message_"+id).style.backgroundImage="url('"+baseUrl+modulePath+"images/statusBg-unpublished.png')";
				if(document.getElementById("status_"+id) != null)
					document.getElementById("status_"+id).innerHTML = lang_unpublished;
			}
			
			if(action == "delete")
			{
				document.getElementById("quickButts_" + id).style.display = "none";
				document.getElementById("pub_col_"+id).style.backgroundImage="url('"+sniPath+"images/publish-col_dis.png')";
				document.getElementById("pub_chkbx_" + id).setAttribute("disabled","disabled");

				document.getElementById("unpub_col_"+id).style.backgroundImage="url('"+sniPath+"images/unpublish-col_dis.png')";
				document.getElementById("unpub_chkbx_" + id).setAttribute("disabled","disabled");
				
				document.getElementById("delete_col_"+id).style.backgroundImage="none";
				document.getElementById("delete_col_"+id).style.backgroundColor="#EEE";
				document.getElementById("delete_chkbx_" + id).setAttribute("disabled","disabled");
				document.getElementById("done_" + id).innerHTML  = "<div class=\"done\">" +lang_deleted+ "</div><br />";
				document.getElementById("pub_chkbx_" + id).checked = false;
				document.getElementById("unpub_chkbx_" + id).checked = false;
				document.getElementById("delete_chkbx_" + id).checked = false;
			}
			
			if(action == "edit")
			{
				document.getElementById("form_" + id).style.display = "none";
				document.getElementById("row_" + id).innerHTML =  xmlHttp.responseText;
				document.getElementById("done_" + id).innerHTML = "<div class=\"done\">" +lang_edited+ "</div>";
			}
		}
	}
	xmlHttp.send(null);
}


// display edit form for editing comment
function editForm(id)
{
	var message = document.getElementById("message_" + id).innerHTML;
	for(var i=0; i < message.length; i++)
	{
		if( message.search("%<BR>%") )	message = message.replace("<BR>", "\r");	// Opera uses <BR>
		if( message.search("%<br>%") )	message = message.replace("<br>", "\r");	// Gecko uses <br>
		message = message.replace("&nbsp;&nbsp;&nbsp;&nbsp;", "\t");
		message = message.replace('<span class="highlight">', "").replace('</span>', "");
		message = message.replace("&lt;","<").replace("&gt;",">");	// htmlspecialchars() - 0.9.2
	}
	document.getElementById("textarea_" + id).value = message;
	document.getElementById("msg_" + id).innerHTML = "";
	document.getElementById("form_" + id).style.display = "block";
}


// check all checkboxes
function checkAll(actName)
{
	if(actName == "publish")
	{
		var name = "comment_publish_ids[]";
		var lang_check = lang_checkAll_publish;
		var lang_uncheck = lang_uncheckAll_publish;
	}
	if(actName == "unpublish")
	{
		var name = "comment_unpublish_ids[]";
		var lang_check = lang_checkAll_unpublish;
		var lang_uncheck = lang_uncheckAll_unpublish;
	}
	if(actName == "delete")
	{
		var name = "comment_delete_ids[]";
		var lang_check = lang_checkAll_delete;
		var lang_uncheck = lang_uncheckAll_delete;
	}	

	var rows = document.getElementsByTagName('input');
	
	for( var i = 0; i < rows.length; i++ )
	{
		if(rows[i].name == name)
		{
			if(rows[i].disabled == false)
				rows[i].checked = ( document.getElementById(actName + "_checkLink").checked == true ) ? true : false;
			
			if(document.getElementById(actName + "_checkLink").getAttribute("disabled") == "disabled")	// Disabled checkboxes must be unchecked! - 0.9.2
				rows[i].checked = false;
		}
	}
}