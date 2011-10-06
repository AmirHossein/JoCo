<?php
/*********************************
Module : JoCo (Jot Comments module) : search
Author : AHHP ~ Boplo.ir


File map:
	# Process submit values : 33
		# Display : 42
		# Search in comments : 45
		# Set highlight word : 48
		# Search in documents : 85
			# Search Parents : 86
			# Search Docgroups : 98
			# Search Documents : 108
			# Search NOT documents : 118
			# Get choosen doc titles (ajax) : 145
		# Search by users : 180
			# Search Webgroups : 181
			# Search guests : 192
			# Search users : 202
			# Search NOT users : 214
			# Get choosen user names (ajax) : 248
		# Date limitation : 280
		# Limit : 314
		# Sort : 318
		# Set WHERE statement : 324
	# Normal search form : 338
*********************************/

if(!$modx) die("<h1>Forbidden!</h1>");

// Part after pressing search submit
if( array_key_exists("comment_search_submit", $_POST) )
{
	$POST = $JoCo->escape($_POST);
	$inComment = $form = array();
	$id_temp = $jotId_doc = array();
	$jotId_user = $users = array();
	$jotId = array();
	
	/****************** Display ******************/
	$form[] = ($POST["type"] != 2) ? ("published=" . $POST["type"]) : "id>0";
	
	/****************** Search in comments ******************/
	if( !empty($POST["searchComments"]) )
	{
		$searchString = $_POST['highlight'] = $POST["searchComments"];	// Set highlight word
		
		if(isset($POST['in_content'])) $inComment[] = "`content` LIKE '%$searchString%'";
		if(isset($POST['in_title'])) 	$inComment[] = "`title` LIKE '%$searchString%'";
		if(isset($POST['in_tagid'])) 	$inComment[] = "`tagid` LIKE '%$searchString%'";
		if(isset($POST['in_flags'])) 	$inComment[] = "`flags` LIKE '%$searchString%'";
	}
	if( !empty($POST["searchNotComments"]) )
	{
		$searchString = $POST["searchNotComments"];
		
		if(isset($POST['not_in_content'])) $inComment[] = "`content` NOT LIKE '%$searchString%'";
		if(isset($POST['not_in_title'])) 	$inComment[] = "`title` NOT LIKE '%$searchString%'";
		if(isset($POST['not_in_tagid'])) 	$inComment[] = "`tagid` NOT LIKE '%$searchString%'";
		if(isset($POST['not_in_flags'])) 	$inComment[] = "`flags` NOT LIKE '%$searchString%'";
	}
	
	if( !empty($POST["searchRegex"]) )
	{
		$regString = $POST["searchRegex"];
		if(isset($POST['regx_in_content'])) $inComment[] = "`content` REGEXP '$regString'";
		if(isset($POST['regx_in_title'])) 	$inComment[] = "`title` REGEXP '$regString'";
		if(isset($POST['regx_in_tagid']))	$inComment[] = "`tagid` REGEXP '$regString'";
		if(isset($POST['regx_in_flags'])) 	$inComment[] = "`flags` REGEXP '$regString'";
	}
	if( !empty($POST["searchRegex"]) )
	{
		$regString = $POST["searchNotRegex"];
		if(isset($POST['regx_not_in_content'])) $inComment[] = "`content` NOT REGEXP '$regString'";
		if(isset($POST['regx_not_in_title'])) 	$inComment[] = "`title` NOT REGEXP '$regString'";
		if(isset($POST['regx_not_in_tagid']))	$inComment[] = "`tagid` NOT REGEXP '$regString'";
		if(isset($POST['regx_not_in_flags'])) 	$inComment[] = "`flags` NOT REGEXP '$regString'";
	}

	if( !empty($inComment) )
		$inComment_where = implode(" OR ", $inComment);
	
	/****************** Search in documents ******************/
	if( !empty($POST["searchParents"]) )
	{
		$parent_where = "parent IN(" .implode(",", $JoCo->explodeByComma($POST["searchParents"])). ")";
		$select = $modx->db->select("id", $modx->getFullTableName("site_content"), $parent_where);
		while( $row = $modx->db->getRow($select) )
			$id_temp[] = $row['id'];
		
		// add parent docs themeselves to IDs
		$id_temp = array_merge($id_temp, $JoCo->explodeByComma($POST["searchParents"]));
		$_POST["indicateParents"] = $id_temp;	// for indicate in output
	}
	
	if( !empty($POST["searchDocGroups"]) )
	{
		if( $docs = $JoCo->getDocGroupDocs($POST["searchDocGroups"]) )
		{
			$id_temp = array_merge($id_temp, $docs);
			// for indicate in output
			$_POST["indicateDocGroups"] = $docs;
		}
	}
	
	if( !empty($POST["searchDocs"]) )
	{
		$docs_tmp = $JoCo->explodeByComma($POST["searchDocs"]);
		foreach($docs_tmp as $key => $val)	settype($docs_tmp[$key], "integer");
		$id_temp = array_merge($id_temp, $docs_tmp);
		// for indicate in output
		$_POST["indicateDocs"] = $docs_tmp;
		unset($docs_tmp);
	}
	
	if( !empty($POST["searchNotDocs"]) )
	{
		$notdocs = $JoCo->explodeByComma($POST["searchNotDocs"]);	
		foreach($notdocs as $notdoc)
		{
			$i = 0;
			foreach($id_temp as $temp)
			{
				if($notdoc == $temp)
					unset($id_temp[$i]);
				$i++;
			}
		}
	}

	
	if( !empty($id_temp) )
	{
		$uparent_where = " `uparent` IN(" .join(",", $id_temp).")";
		$select = $modx->db->select("id,uparent", $JoCo->gv['jot_content_tbl'], $uparent_where);
		$uparents = array();
		while( $row = $modx->db->getRow($select) )
		{
			$jotId_doc[] = $row['id'];
			$uparents[] = $row['uparent'];
		}
		
		// Set AJAX output
		if( !empty($_POST["docTitles"]) )
		{
			if( empty($id_temp) )
			{
				echo $JoCo->lang['nothing_to_dispaly'];
				exit;
			}
			
			$valid_titles = $invalid_titles = $not_exists_titles = array();
			foreach($id_temp as $temp)
			{
				if( $title = $JoCo->id_to_title($temp) )
				{
					if( in_array($temp ,$uparents) )
						$valid_titles[] = "<b>".$title."</b> <small>($temp)</small>";
					else
					 $invalid_titles[] = "<s>$title</s><small>($temp)</small>";
				}
				else
					$not_exists_titles[] = "<small><s><i>$temp</i></s></small>";
			}
			echo join(" , ", $valid_titles) . "<p class=\"eliminated\">" . join(" , ", $invalid_titles) . "</p>" . "<p class=\"eliminated\">" . join(" , ", $not_exists_titles) . "</p>";	// an inline class!!!
			
			unset($valid_titles,$invalid_titles,$not_exists,$id_temp);
			exit;
		}
	}
	if( empty($id_temp) && array_key_exists("docTitles", $_POST) )
	{
			echo $JoCo->lang['nothing_to_dispaly'];
			exit;
	}		
	
	
	/****************** Search by users ******************/
	if( !empty($POST["searchWebGroups"]) )
	{
		if( $userTmp = $JoCo->getWebGroupUsers($POST["searchWebGroups"]) )
		{
			$users = $userTmp;
			unset($userTmp);
			// for indicate in output
			$_POST["indicateWebGroups"] = $users;
		}
	}
	
	if( array_key_exists("searchGuest", $POST) )
	{
		if( $POST["searchGuest"] == "on" )
		{
			$users[] = "0";
			// for indicate in output
			$_POST["guestUsers"] = "0";
		}
	}
	
	if( !empty($POST["searchUsers"]) )
	{
		if( $users_tmp = $JoCo->explodeByComma($POST["searchUsers"]) )
		{
			foreach($users_tmp as $key => $val)	settype($users_tmp[$key], "integer");
			$users = array_merge($users, $users_tmp);
			// for indicate in output
			$_POST["indicateUsers"] = $users_tmp;
			unset($users_tmp);
		}
	}
	
	if( !empty($POST["searchNotUsers"]) )
	{
		$notusers = $JoCo->explodeByComma($POST["searchNotUsers"]);
		
		if( empty($users) )
		{
			$select = $modx->db->select("createdby", $JoCo->gv['jot_content_tbl']);
			while( $row = $modx->db->getRow($select) )
				$users[] = $row['createdby'];
		}
		
		foreach($notusers as $notuser)
		{
			$i = 0;
			foreach($users as $user)
			{
				if($notuser == $user)
					unset($users[$i]);
				$i++;
			}
		}
	}
	
	if( !empty($users) )
	{
		$createdby = array();
		$createdby_where = " `createdby` IN(". join(",", $users) .")";
		$select = $modx->db->select("id,createdby", $JoCo->gv['jot_content_tbl'], $createdby_where);
		while( $row = $modx->db->getRow($select) )
		{
			$jotId_user[] = $row['id'];
			$createdby[] = $row['createdby'];
		}
		
		// Set AJAX output
		if( array_key_exists("userNames", $_POST) )
		{
			$valid_names = $invalid_names = $not_exists_names = array();
			foreach($users as $temp)
			{
				if($temp == 0)
				{
					$valid_names[] = "<b>{guest}</b> <small>($temp)</small>";
					continue;
				}
				if( $name = $JoCo->id_to_fullname($temp, "fullname", "CHECKIT") )
				{
					if( in_array($temp ,$createdby) )
						$valid_names[] = "<b>".$name."</b> <small>($temp)</small>";
					else
					 $invalid_names[] = "<s>$name</s><small>($temp)</small>";
				}
				else
					$not_exists_names[] = "<small><s><i>$temp</i></s></small>";
			}
			echo join(" , ", $valid_names) . "<p class=\"eliminated\">" . join(" , ", $invalid_names) . "</p>" . "<p class=\"eliminated\">" . join(" , ", $not_exists_names) . "</p>";	// an inline class!!!
			unset($valid_names,$invalid_names,$not_exists_names,$users);
			exit;
		}
	}
	if( empty($users) && array_key_exists("userNames", $_POST) )
	{
			echo $JoCo->lang['nothing_to_dispaly'];
			exit;
	}	
	
	/****************** Date limitation ******************/
	if( !empty($POST["fromDate"]) )
	{
		if($JoCo->gv['lang'] == "persian")
			$form[] = "createdon > " .jalali_to_stamp($POST["fromDate"]);
		else
		{
			list($year, $month, $day) = explode("-",$POST["fromDate"]);
			$form[] = "createdon > " .mktime(0, 0, 0, intval($month), intval($day), intval($year));
		}
	}
	
	if( !empty($POST["toDate"]) )
	{
		if($lang == "persian")
			$form[] = "createdon < " .jalali_to_stamp($POST["toDate"]);
		else
		{
			list($year, $month, $day) = explode("-",$POST["toDate"]);
			$form[] = "createdon < " .mktime(0, 0, 0, intval($month), intval($day), intval($year));
		}
	}

	if( array_key_exists("recentComments", $POST) )
	{
		if( $POST["recentComments"] == "on" )
			$form[] = "createdon > " .$JoCo->permission['lastActive'];
	}


	if( isset($POST["type"]) || isset($POST["fromDate"]) || isset($POST["toDate"]) )
		$form_where = implode(" AND ", $form);

	
	/****************** Limit ******************/
	$LIMIT = ( isset($POST["maxResult"]) ) ? $POST["maxResult"] : "";
	
	
	/****************** Sort ******************/
	$sortBy = ( isset($POST["sortBy"]) ) ? $POST["sortBy"] : "uparent";
	$sortType = ( isset($POST["sortType"]) ) ? $POST["sortType"] : "DESC";
	$SORT = $sortBy." ".$sortType;
	
	
	// Set WHERE statement
	if( isset($form_where) && isset($inComment_where) )
		$WHERE = "($form_where) AND ($inComment_where)";
	if( isset($form_where) && !isset($inComment_where) )
		$WHERE = "($form_where)";
	if( !isset($form_where) && isset($inComment_where) )
		$WHERE = "($inComment_where)";

	$jotId = array_merge($jotId_user, $jotId_doc);
	if( !empty($jotId) )
		$WHERE .= " AND `id` IN(" .join(",", $jotId ).")";

	unset($row,$select);
}
else	// Form submit has not pressed
{
	$SEARCH_FORM = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'tpls/search.html');
}
?>