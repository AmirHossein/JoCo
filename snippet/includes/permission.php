<?php
/*********************************
Snippet : JoCo (Jot Comments snippet) : permissions
Author : AHHP ~ Boplo.ir


File map:
	# Create data table (ajax) : 18
	# Set new permissions from table (ajax) : 170
	# Move user (ajax) : 222
	# Refresh user list (ajax) : 288
	# Normal part : 297
	# getUserList function : 320
*********************************/



// Create data table : ajax
if( array_key_exists("getPermissions", $_GET) )
{	// $_GET['getPermission'] contains internalKey
	
	$select = $modx->db->select("*", $JoCo->gv["joco_permissions_tbl"], "internalKey=".$modx->db->escape($_GET['getPermissions']));
	$row = $modx->db->getRow($select);	
	
	$table_rows_sp = ''; // for cols which sholud place at the end of HTML table
	$fullname_table = ($row['internalKey'] > 0) ? "user_attributes" : "web_user_attributes";
	$table_rows = '
		<h2>'.$JoCo->id_to_fullname( abs($row['internalKey']), "fullname", $fullname_table).'</h2>
		<table id="permiTable" cellpadding="1" cellspacing="1">
			<tr>
				<th class="gridHeader">'.$JoCo->lang['actions'].'</td>
				<th class="gridHeader" colspan="2">'.$JoCo->lang['permissions'].'</td>
			</tr>
	';

	$cols = array(
		"publish", "unpublish", "edit", "remove", "logging", "viewAll",
		"viewPublished", "viewUnpublished", "ip", "webUsers", "createdDocs",
		"publishedDocs", "editedDocs", "search", "permission",
		"summary", "defaultView", "defaultTheme", "changeTheme",
		"summaryResPerPage", "resPerPage"
	);
	
	$disabledForAll = $JoCo->params["disabledForAll"];
	$disabledForUser = $JoCo->params["disabledForUser"];
	$enabledForUser = $JoCo->params["enabledForUser"];

	foreach($cols as $col)
	{
		$disabled = ( in_array($col, $disabledForAll) ) ? 'disabled="disabled"' : "";
		$disabled = ( in_array($JoCo->userId, $disabledForUser[$col]) ) ? 'disabled="disabled"' : $disabled;
		$disabled = ( in_array($JoCo->userId, $enabledForUser[$col]) ) ? "" : $disabled;
		
		if($col == "logging")
		{
			$checked1 = ($row[$col] == 1) ? 'checked="checked"' : "";
			$checked0 = ($row[$col] == 0) ? 'checked="checked"' : "";
			$table_rows_sp .= '
				<tr>
					<td>'.$JoCo->lang[$col.'Permi'].'</td>
					<td align="center"><input '.$disabled.' type="radio" name="'.$col.'Permi" value="1" '.$checked1.' /> '.$JoCo->lang['allow'].'</td>
					<td align="center"><input '.$disabled.' type="radio" name="'.$col.'Permi" value="0" '.$checked0.' /> '.$JoCo->lang['disallow'].'</td>
				</tr>
			';
			continue;
		}
		
		if($col=="defaultView" || $col=="defaultTheme")	// later
			continue;
		
		if($col=="createdDocs" || $col=="publishedDocs" || $col=="editedDocs")
		{
			$val1 = ( strstr($row[$col],"1") ) ? 'checked="checked"' : "";
			$val2 = ( strstr($row[$col],"2") ) ? 'checked="checked"' : "";
			$val3 = ( strstr($row[$col],"3") ) ? 'checked="checked"' : "";
			$val4 = ( strstr($row[$col],"4") ) ? 'checked="checked"' : "";
			$val0 = ( $row[$col] == 0 ) ? 'checked="checked"' : "";
			
			$table_rows_sp .= '
				<tr>
					<td>'.$JoCo->lang[$col.'Permi'].'</td>
					<td colspan="2">
						<input '.$disabled.' type="checkbox" name="own1'.$col.'Permi" value="1" '.$val1.' />'.$JoCo->lang['publish'].'&nbsp;&nbsp;
						<input '.$disabled.' type="checkbox" name="own2'.$col.'Permi" value="2" '.$val2.' />'.$JoCo->lang['unpublish'].'&nbsp;&nbsp;
						<input '.$disabled.' type="checkbox" name="own3'.$col.'Permi" value="3" '.$val3.' />'.$JoCo->lang['delete'].'&nbsp;&nbsp;
						<input '.$disabled.' type="checkbox" name="own4'.$col.'Permi" value="4" '.$val4.' />'.$JoCo->lang['edit'].'&nbsp;&nbsp;
						<input '.$disabled.' type="checkbox" name="own0'.$col.'Permi" value="0" '.$val0.' />'.$JoCo->lang['no_permission'].'&nbsp;&nbsp;
					</td>
				</tr>
			';
			continue;
		}

		if($col=="summaryResPerPage" || $col=="resPerPage")
		{
			$table_rows_sp .= '
				<tr>
					<td>'.$JoCo->lang[$col.'Permi'].'</td>
					<td colspan="2" align="center"><input '.$disabled.' type="text" name="'.$col.'Permi" value="'.$row[$col].'"  style="width:90%" /></td>
				</tr>
			';
			continue;
		}
		
		$checked1 = ($row[$col] == 1) ? 'checked' : "";
		$checked0 = ($row[$col] == 0) ? 'checked' : "";
		$table_rows .= '
			<tr>
				<td>'.$JoCo->lang[$col.'Permi'].'</td>
				<td align="center"><input '.$disabled.' type="radio" name="'.$col.'Permi" value="1" '.$checked1.' /> '.$JoCo->lang['allow'].'</td>
				<td align="center"><input '.$disabled.' type="radio" name="'.$col.'Permi" value="0" '.$checked0.' /> '.$JoCo->lang['disallow'].'</td>
			</tr>
		';
	}
	
	$disabled_defaultTheme = ( in_array($col, $disabledForAll) ) ? 'disabled="disabled"' : "";
	$disabled_defaultTheme = ( in_array($JoCo->userId, $disabledForUser[$col]) ) ? 'disabled="disabled"' : $disabled_defaultTheme;
	$disabled_defaultTheme = ( ! in_array($JoCo->userId, $enabledForUser[$col]) ) ? "" : 'disabled="disabled"';

	$disabled_defaultView = ( in_array($col, $disabledForAll) ) ? 'disabled="disabled"' : "";
	$disabled_defaultView = ( in_array($JoCo->userId, $disabledForUser[$col]) ) ? 'disabled="disabled"' : $disabled_defaultView;
	$disabled_defaultView = ( ! in_array($JoCo->userId, $enabledForUser[$col]) ) ? "" : 'disabled="disabled"';

	$defaultTheme1 = ($row['defaultTheme'] == 1) ? 'checked' : "";
	$defaultTheme2 = ($row['defaultTheme'] == 2) ? 'checked' : "";
	$defaultView_all = ($row['defaultView'] == 2) ? 'selected' : "" ;
	$defaultView_pubs = ($row['defaultView'] == 1) ? 'selected' : "" ;
	$defaultView_unpubs = ($row['defaultView'] == 0) ? 'selected' : "" ;
	$defaultView_recent = ($row['defaultView'] == 3) ? 'selected' : "" ;
	$defaultView_mine = ($row['defaultView'] == 4) ? 'selected' : "" ;
	$defaultView_nothing = ($row['defaultView'] == 5) ? 'selected' : "" ;
	
	$table_rows .= '
			<tr>
				<td>'.$JoCo->lang['defaultThemePermi'].'</td>
				<td align="center"><input '.$disabled_defaultTheme.' type="radio" name="defaultThemePermi" value="1" '.$defaultTheme1.' /> '.$JoCo->lang['normalTheme'].'</td>
				<td align="center"><input '.$disabled_defaultTheme.' type="radio" name="defaultThemePermi" value="2" '.$defaultTheme2.' /> '.$JoCo->lang['compressedTheme'].'</td>
			</tr>
			<tr>
				<td>'.$JoCo->lang['defaultViewPermi'].'</td>
				<td align="center" colspan="2">
					<select '.$disabled_defaultView.' name="defaultViewPermi" style="width:90%">
						<option value="2" '.$defaultView_all.'>'.$JoCo->lang['viewAllPermi'].'</option>
						<option value="1" '.$defaultView_pubs.'>'.$JoCo->lang['viewPublishedPermi'].'</option>
						<option value="0" '.$defaultView_unpubs.'>'.$JoCo->lang['viewUnpublishedPermi'].'</option>
						<option value="3" '.$defaultView_recent.'>'.$JoCo->lang['view_recent'].'</option>
						<option value="4" '.$defaultView_mine.'>'.$JoCo->lang['view_user_comments'].'</option>
						<option value="5" '.$defaultView_nothing.'>'.$JoCo->lang['view_nothing'].'</option>
					</select>
				</td>
			</tr>
			'.$table_rows_sp.'
		</table>
		<input type="hidden" name="setPersmissions" value="'.$_GET['getPermissions'].'"/>';
	
	if($JoCo->permission['webUsers'] == 1)
		$table_rows .= '
		<div class="button" style="width:110px;">
			<a href="javascript:setPermissions('.$_GET['getPermissions'].');">
			<img src="'.$JoCo->gv['sniPath'].'/images/accept_item.png" width="20" />
			 <span style="bottom:5px; position:relative;">'.$JoCo->lang['save_changes'].'</span></a>
		</div>
		';
	echo $table_rows;
	exit;
}



// Set new permissions from table : ajax
if( array_key_exists("setPermissions", $_GET) )
{	// $_GET['setPermissions'] contains internalKey
	if($JoCo->permission['webUsers'] == 0)	exit;
	
	$GET = $JoCo->escape($_GET);
	$table = ($GET['setPermissions'] > 0) ? $modx->getFullTableName("user_attributes") : $modx->getFullTableName("web_user_attributes");
	
	// check for administrators
	$row = $modx->db->getRow( $modx->db->select("role", $table, "internalKey=". abs( $GET['setPermissions'] ) ) );
	if( $row['role'] == 1 && $GET['setPermissions'] != $_SESSION['mgrInternalKey'])
	{
		echo $JoCo->lang['impossible_change_admins_permissions'];
		exit;
	}
	
	$update = array();
	foreach($GET as $key => $val)
	{
		if(strpos($key, "Permi") == false)
			continue;
		
		$key = str_replace("Permi", "", $key);
		$update[$key] = $val;
		
		if($key=="createdDocs" || $key=="publishedDocs" || $key=="editedDocs")
		{
			if(
				$update[$key] == 0
				||
				(
					!strstr($update[$key],"1")
					&& !strstr($update[$key],"2")
					&& !strstr($update[$key],"3")
					&& !strstr($update[$key],"4")
				)
			)
			{
				$update[$key] = 0;
				continue;
			}
		}
	}

	unset($update["setssions"]); // hidden field ("setPermissios") has "Permi" and must unset.
	$modx->db->update($update, $JoCo->gv["joco_permissions_tbl"], "internalKey=".$GET['setPermissions']);
	echo $JoCo->lang['setPermissions_success'];
	exit;
}



// Move user : ajax
if( array_key_exists("moveUser", $_GET) )
{	// $_GET['moveUser'] contains internalKey
	$GET = $JoCo->escape($_GET);
	if($GET['moveUser'] > 0)		$isManager = true;
	
	// check for administrators
	if($isManager)
	{
		$row = $modx->db->getRow( $modx->db->select("role", $modx->getFullTableName("user_attributes"), "internalKey=". $GET['moveUser']) );
		$isAdmin = ( $row['role'] == 1 ) ? true : false;
	}
	else
		$isAdmin = false;	// web user's default permissions are like simple managers
	
	
	if($GET['dir'] == 'in')
	{
		// check for existed users
		if( $modx->db->getRecordCount( $modx->db->select("id", $JoCo->gv["joco_permissions_tbl"], "internalKey=". $GET['moveUser']) ) > 0 )
		{
			echo $JoCo->lang['user_exists'];
			exit;	
		}
		
		if($isAdmin)
			$insert = array(
				"internalKey" => $GET['moveUser'],"publish" => 1,"unpublish" => 1,"edit" => 1
				,"remove" => 1,"submit" => 1,"ip" => 1,"logging" => 1,"viewAll" => 1
				,"viewPublished" => 1,"viewUnpublished" => 1,"publishedDocs" => 1
				,"createdDocs" => 1,"editedDocs" => 1,"search" => 1,"permission" => 1
				,"summary" => 1,"lastActiveTmp" => time()
			);
		else
			$insert = array("internalKey" => $GET['moveUser'], "lastActiveTmp" => time());
		
		if( ! $res = $modx->db->insert($insert, $JoCo->gv["joco_permissions_tbl"]) )
		{
			echo $JoCo->lang['user_creation_failed'];
			exit;	
		}
	}

	if($GET['dir'] == 'out')
	{
		if($JoCo->JoCoUser == $GET['moveUser'])
		{
			echo $JoCo->lang['impossible_romove_yourself'];
			exit;
		}
		
		if($isAdmin)
		{
			echo $JoCo->lang['impossible_change_admins_permissions'];
			exit;
		}
		$modx->db->delete($JoCo->gv["joco_permissions_tbl"], "internalKey=". $GET['moveUser']);
	}
	
	$pos = ($isManager) ? "mgr" : "web";
	echo getUserList("exist",$JoCo, $pos);
	exit;
}



// Refresh user list : ajax
if( array_key_exists("refreshList", $_GET) )
{	// $_GET["refreshList"] contains user position, "mgr" for managers and "web" for webusers.
	echo getUserList("exist",$JoCo, $_GET["refreshList"]);
	exit;
}



// simple view
$PERMISSION_FORM = $JoCo->fileContent('tpls/permission.html');

// Managers
if($JoCo->JoCoUser > 0)
{	// only managers can see see JoCo manager users
	$PERMISSION_FORM = str_replace("[+allMgrUsers+]", getUserList("all",$JoCo), $PERMISSION_FORM);	// Get all users
	$PERMISSION_FORM = str_replace("[+existMgrUsers+]", getUserList("exist",$JoCo), $PERMISSION_FORM);	// Get exist users
}
else
	$PERMISSION_FORM = str_replace(array("[+allMgrUsers+]","[+existMgrUsers+]"), "", $PERMISSION_FORM);

// Web users
$PERMISSION_FORM = str_replace("[+allWebUsers+]", getUserList("all",$JoCo,"web"), $PERMISSION_FORM);	// Get exist users
$PERMISSION_FORM = str_replace("[+existWebUsers+]", getUserList("exist",$JoCo,"web"), $PERMISSION_FORM);
//$PERMISSION_FORM = str_replace(array("[+allWebUsers+]","[+existWebUsers+]"), "", $PERMISSION_FORM);



// Refresh user list
// $type is "exist" or "all" to refresh exist list or all user list
// $JoCo is JoCo object
// $pos ["mgr"|"web"]
function getUserList($type,$JoCo,$pos="mgr")
{
	global $modx;
	
	if($pos == "mgr")
	{
		$table = $modx->getFullTableName("user_attributes");
		$allUserSelect = '<h5>' .$JoCo->lang['mgr_moderators']. '</h5>';
		$existUserSelect = '<h5>' .$JoCo->lang['mgr_moderators']. '</h5>';
	}
	if($pos == "web")
	{
		$table = $modx->getFullTableName("web_user_attributes");
		$allUserSelect = '<h5>' .$JoCo->lang['web_moderators']. '</h5>';
		$existUserSelect = '<h5>' .$JoCo->lang['web_moderators']. '</h5>';
	}

	if($type == "all")
	{
		// get all users
		$allUserSelect .= '<select id="'.$pos.'allUsersSelect" name="'.$pos.'allUsers" multiple="multiple" style="width:100%;" onclick="changeEl(\'all\',\''.$pos.'\');">';
		
		$select = $modx->db->select("internalKey, fullname", $table);
		while( $row = $modx->db->getRow($select) )
		{
			$userId = ($pos == "mgr") ? $row['internalKey'] : $row['internalKey'] * (-1);
			$allUserSelect .= '<option value="'.$userId .'">'.$row['fullname'].'</option>';
		}
		
		return $allUserSelect."</select>";
	}
	
	if($type == "exist")
	{
		// get exist users
		$existUserSelect .= '<select id="'.$pos.'existUsersSelect" name="'.$pos.'existUsers" multiple="multiple" style="width:100%;" onclick="changeEl(\'exists\',\''.$pos.'\');">';
		
		$table = ($pos == "mgr") ? "user_attributes" : "web_user_attributes";
		$where = ($pos == "mgr") ? "internalKey>0" : "internalKey<0";
		$select = $modx->db->select("internalKey", $JoCo->gv["joco_permissions_tbl"], $where);
		while( $row = $modx->db->getRow($select) )
		{
			$existUserSelect .= '<option value="'.$row['internalKey'].'">'.$JoCo->id_to_fullname( abs($row['internalKey']), "fullname", $table).'</option>';
		}
		
		return $existUserSelect."</select>";
	}
}

?>