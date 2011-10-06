<?php
/*********************************
Module : JoCo (Jot Comments module)
Version : 0.9.2
Author : AHHP ~ Boplo.ir

File map:
	# Include JoCo class : 35
		# JoCo object : 37
	# Change theme : 50
	# Include each row tpl : 61
	# Include main container tpl : 63
	# Permission part request : 68
	# Summary part request : 90
	# Jot Calls part request : 113
	# Help part request : 135
	# Comment actions by checkboxes and ajax : 148
		# Publish : 153
		# Unpublish : 165
		# Delete : 176
		# Edit : 186
	# Main part : 206
		# Search part request : 209
		# Check view and WHERE values : 238
		# Get user's forbidden docs : 278
		# SELECT from "jot_content" table : 281
		# Initialize pagination : 286
		# comment rows loop : 294
		# Set paginate : 336
		# Set output : 363
		# Print ouput : 402
*********************************/
if(!$modx) die("<h1>Forbidden!</h1>");


// Include JoCo class
require_once($modx->config['base_path'].'assets/modules/JoCo/includes/joco.class.php');
$JoCo = new JoCo();
$GET = $JoCo->escape($_GET);	// $JoCo->escape use $modx->db->escape() and $modx->stripTags() on $_GET and given $GET
$elements = array();
$checkedElements = $JoCo->NP->generals($JoCo->permission);	// general elements that need permission to show
if( function_exists("date_default_timezone_set") )
{
	$timezone = ( $TZ = ini_get('date.timezone') ) ? $TZ : 'UTC';
	date_default_timezone_set($timezone); // timezone set for PHP 5
}
if( !isset($JoCo->gv['mKey']) )	$JoCo->gv['mKey'] = $_GET['id']; // module ID
/**************************************************************/


// change theme
if( array_key_exists("theme", $GET) )
{
	$modx->db->update("defaultTheme=".$GET['theme'], $JoCo->gv['joco_permissions_tbl'], "internalKey=".$_SESSION['mgrInternalKey']);
	$JoCo->permission['defaultTheme'] = $GET['theme'];
	$checkedElements = $JoCo->NP->generals($JoCo->permission);
}
$themes = ($JoCo->permission['defaultTheme'] == 1) ? "" : ".compressed";
/**************************************************************/


// include each row tpl
$indvTpl = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath']."tpls/indv.tpl$themes.html");
// include main container tpl
$moduleHTML = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath']."tpls/module.tpl$themes.html");
/**************************************************************/


// Permission part request
if( array_key_exists("permission", $GET) )
{
	if($JoCo->permission['permission'] == 1)
	{
		// include permission.php, it contains $PERMISSION_FORM
		include($JoCo->gv['basePath'].$JoCo->gv['modulePath']."includes/permission.php");
		if( isset($PERMISSION_FORM) )
		{
			$PERMISSION_FORM = $JoCo->replace_all($checkedElements, $PERMISSION_FORM);
			$PERMISSION_FORM = $JoCo->replace_all($JoCo->gv, $PERMISSION_FORM);
			$PERMISSION_FORM = $JoCo->replace_all($JoCo->lang, $PERMISSION_FORM, true);
			echo $PERMISSION_FORM;
			return;
		}
	}
	else
		$JoCo->error[] = $JoCo->lang['permission_denied'];
}
/**************************************************************/


//Summary part request
if( array_key_exists("summary", $GET) )
{
	if($JoCo->permission['summary'] == 1)
	{
		// include summary.php, it contains $SUMMARY
		include($JoCo->gv['basePath'].$JoCo->gv['modulePath']."includes/summary.php");
		if( isset($SUMMARY) )
		{
			$SUMMARY = $JoCo->replace_all($checkedElements, $SUMMARY);
			$SUMMARY = $JoCo->replace_all($JoCo->gv, $SUMMARY);
			$SUMMARY = $JoCo->replace_all($JoCo->lang, $SUMMARY, true);
			echo $SUMMARY;
			return;
		}
	}
	else
		$JoCo->error[] = $JoCo->lang['summary_denied'];
}
/**************************************************************/


// JotCall part request
if( array_key_exists("jotCall", $GET) )
{
	if($JoCo->permission['jotCall'] == 1)
	{
		// include jotcall.php, it contains $JOTCALL
		include($JoCo->gv['basePath'].$JoCo->gv['modulePath']."includes/jotcall.php");
		if( isset($JOTCALL) )
		{
			$JOTCALL = $JoCo->replace_all($checkedElements, $JOTCALL);
			$JOTCALL = $JoCo->replace_all($JoCo->gv, $JOTCALL);
			$JOTCALL = $JoCo->replace_all($JoCo->lang, $JOTCALL, true);
			echo $JOTCALL;
			return;
		}
	}
	else
		$JoCo->error[] = $JoCo->lang['jotcall_denied'];
}
/**************************************************************/


// Help part request
if( array_key_exists("help", $GET) )
{
	$HELP = ( file_exists($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'help/'.$JoCo->gv['lang'].'-help.html') ) ? $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'help/'.$JoCo->gv['lang'].'-help.html') : $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'help/english-help.html');
	$HELP = $JoCo->replace_all($checkedElements, $HELP);
	$HELP = $JoCo->replace_all($JoCo->gv, $HELP);
	$HELP = $JoCo->replace_all($JoCo->lang, $HELP, true);
	echo $HELP;
	return;
}
/**************************************************************/


// publish / unpublish / edit / delete actions by checkboxes and ajax
if( array_key_exists("comment_action", $GET) || array_key_exists("normal_submit", $_POST) )
{
	$GET_comment_action = ( array_key_exists("comment_action", $GET) ) ? $GET["comment_action"] : '';

	// publish : ajax : ajaxActions.js : GET : ajaxAction("publish", id); 
	// publish : non-ajax : POST : form checkbox.
	if( $GET_comment_action == "publish" || isset($_POST["comment_publish_ids"]) )
	{
		$update = array("published"=>1, "publishedon"=>time(), "publishedby"=>$_SESSION['mgrInternalKey']);
		
		if(isset($GET["comment_id"]))
			$JoCo->changeComment($GET["comment_id"], $update, 'publish');
		elseif( isset($_POST["comment_publish_ids"]) )
			$JoCo->changeComment($_POST["comment_publish_ids"], $update, 'publish');
	}

	// unpublish : ajax : ajaxActions.js : GET : ajaxAction("unpublish", id); 
	// unpublish : non-ajax : POST : form checkbox.
	if( $GET_comment_action == "unpublish" || isset($_POST["comment_unpublish_ids"]) )
	{
		$update = array("published" => 0);
		if(isset($GET["comment_id"]))
			$JoCo->changeComment($GET["comment_id"], $update, 'unpublish');
		elseif( isset($_POST["comment_unpublish_ids"]) )
			$JoCo->changeComment($_POST["comment_unpublish_ids"], $update, 'unpublish');
	}

	// delete : ajax : ajaxActions.js : GET : ajaxAction("delete", id); 
	// delete : non-ajax : POST : form checkbox.
	if( $GET_comment_action == "delete" || isset($_POST["comment_delete_ids"]) )
	{
		if(isset($GET["comment_id"]))
			$JoCo->changeComment($GET["comment_id"], $update, 'delete');
		elseif( isset($_POST["comment_delete_ids"]) )
			$JoCo->changeComment($_POST["comment_delete_ids"], $update, 'delete');
	}

	// edit : ajax : ajaxActions.js : ajaxAction("edit", id);
	if($GET_comment_action == "edit")
	{
		$ajaxId = isset($GET["comment_id"]) ? $GET["comment_id"] : "";
		$message = str_replace("<BR>", "\n", $GET["message"]);
		$message = htmlspecialchars_decode($message, ENT_NOQUOTES);
		$update = array("content"=>$message, "editedon"=>time(), "editedby"=>$_SESSION['mgrInternalKey']);
		$JoCo->changeComment($ajaxId, $update, 'edit');
		$row = $modx->db->getRow( $modx->db->select("*", $JoCo->gv['jot_content_tbl'], "id=$ajaxId") );
		if($JoCo->permission['defaultTheme']==1)
			echo '<div id="row_'.$ajaxId.'" class="editedRow">'.$JoCo->getIndv($row, $indvTpl, $checkedElements, false, true).'</div>';
		else
			echo '<table id="row_'.$ajaxId.'">'.$JoCo->getIndv($row, $indvTpl, $checkedElements, false, true).'</table>';
		return;
	}
}
// END: if( array_key_exists("comment_action", $GET) || array_key_exists("normal_submit", $_POST) )
/**************************************************************/


// Main part
if( !array_key_exists("comment_action", $GET) )
{
	// include search script and get it's output as a WHERE statement
	if( isset($_REQUEST["search"]) )
	{
		if($JoCo->permission['search'] == 1)
		{
			include($JoCo->gv['basePath'].$JoCo->gv['modulePath']."includes/search.php");
			
			if( isset($SEARCH_FORM) )
			{
				$SEARCH_FORM = $JoCo->replace_all($checkedElements, $SEARCH_FORM);
				$SEARCH_FORM = $JoCo->replace_all($JoCo->gv, $SEARCH_FORM);
				$SEARCH_FORM = $JoCo->replace_all($JoCo->lang, $SEARCH_FORM, true);
				
				$lastActive = $JoCo->JoCoDate("%d %B %Y at %H:%S", $JoCo->permission['lastActive']);
				$SEARCH_FORM = str_replace("[+lastActive+]", $lastActive, $SEARCH_FORM);
				
				echo $SEARCH_FORM;
				return;
			}
		}
		else
		{
			$JoCo->error[] = $JoCo->lang['search_denied'];
			if($res == false)
				exit;
		}
	}
	/*******************************/
	
	$JoCo->permission['defaultView'] = empty($GET['show']) ? $JoCo->permission['defaultView'] : $GET['show'];
	// set default view WHERE
	switch( $JoCo->permission['defaultView'] )
	{
		case 0 : $defView = "published=0"; break;	// published comments
		case 1 : $defView = "published=1"; break;	// unpublished comments
		case 2 : $defView = "id>0"; break;	// all comments
		case 3 : $defView = "createdon>" .$JoCo->permission['lastActive']; break;	// last comments
		case 4 : $defView = "createdby=" .$_SESSION['mgrInternalKey']; break;	// user's comments
		case 5 : $defView = "id<0"; break;	// no comment
		case 6 : $defView = 
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".createdby = ".$_SESSION['mgrInternalKey'];
			$from = $JoCo->gv['jot_content_tbl'].",".$modx->getFullTableName("site_content");
			break;	// user created docs
		case 7 : $defView = 
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".publishedby = ".$_SESSION['mgrInternalKey'];
			$from = $JoCo->gv['jot_content_tbl'].",".$modx->getFullTableName("site_content");
			break;	// user published docs
		case 8 : $defView = 
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".editedby = ".$_SESSION['mgrInternalKey'];
			$from = $JoCo->gv['jot_content_tbl'].",".$modx->getFullTableName("site_content");
			break;	// user edited docs
	}
	$WHERE = empty($WHERE) ? $defView : $WHERE; // WHERE value from search script
	$_GET_WHERE = array_key_exists('WHERE', $_GET) ? $_GET['WHERE'] : $WHERE;
	$_GET_WHERE = $JoCo->checkWHERE($_GET_WHERE);
	
	$LIMIT = isset($LIMIT) ? $LIMIT : ""; // LIMIT value from search script, it is max number of results and not SQL LIMIT statement
	$_GET['LIMIT'] = isset($_GET['LIMIT']) ? $_GET['LIMIT'] : $LIMIT;
	
	$SORT = isset($SORT) ? $SORT : "uparent ASC"; // ORDERBY value from search script
	$_GET['SORT'] = isset($_GET['SORT']) ? $_GET['SORT'] : $SORT;
	/*******************************/

	// Get documents that user can not see them
	$getForbiddenDocs = $JoCo->getForbiddenDocs();
	
	// SELECT comment by default WHERE or search WHERE statement
	$from = isset($from) ? $from : $JoCo->gv['jot_content_tbl'];
	$select = $modx->db->select($JoCo->gv['jot_content_tbl'].".*", $from, $_GET_WHERE, $_GET['SORT']);
	$allRecords = $modx->db->getRecordCount($select);	// number of all valid rows by WHERE
	
	// paginate
	$perPage = $JoCo->permission['resPerPage'];
	$countForPaginate = 1;
	$page = ( array_key_exists('page', $_GET) ) ? $_GET['page'] : 1;
	
	$indvs = array();
	while( $r = $modx->db->getRow($select) )
	{
		// JoCo has found $_GET['LIMIT'] number rows and loop must break.
		if(!empty($_GET['LIMIT']) && $countForPaginate > $_GET['LIMIT'])
			break;
		
		// reject IDs which user has not permission by DocGroups limiations OR IDs which customFunctions->onBeforeCheckRow rejects them.
		if( in_array($r['uparent'], $getForbiddenDocs) || !$rowOutput = $JoCo->getIndv($r, $indvTpl, $checkedElements, $countForPaginate) )
		{
			$allRecords--;	// this loop is not a valid result
			continue;
		}
		
		// do paginate
		$pagin = $JoCo->doPaginate($countForPaginate, $page, $perPage);
		if($countForPaginate > $page*$perPage)	// all rows of this page have parsed
			break;
		if( $pagin == false )
		{
			$countForPaginate++;
			continue;
		}
		
		// Get a complete and ready to show row and collect it in an array
		$indvs[] = '<div id="row_'.$r['id'].'">'.$rowOutput.'</div>';	// an inline HTML!!!		---	$rowOutput gets value from line 275
		$countForPaginate++;
	}
	
	// There is no result to show
	if( empty($indvs) )
	{
		$JoCo->error[] = $JoCo->lang['nothing_to_dispaly'];
		$elements["paginate"]
		= $elements['next_page']
		= $elements['next_page']
		= $elements['previous_page']
		= $elements['count']
		= $JoCo->lang['all_results']
		= $checkedElements['submit']
		= "";
		$elements['display_or_somthing_exists'] = 'style="display:none;"'; // hide checkAll links
	}
	else // $indvs is not empty
	{
		// Set pagination
		if(!empty($_GET['LIMIT']))
			$allRecs = $elements['count'] = ($_GET['LIMIT']>$allRecords ? $allRecords : $_GET['LIMIT']);
		else
			$allRecs = $elements['count'] = $allRecords;
		
		$where = '&WHERE='.urlencode($_GET_WHERE).'&SORT='.urlencode($_GET['SORT']).'&LIMIT='.urlencode($_GET['LIMIT']);
		$paginate = $JoCo->setPaginate($allRecs, $perPage, $where, $page);
		$elements["paginate"] = join("", $paginate);

		$n_page = $page + 1;
		$p_page = ($page == 1) ? 1 : ($page - 1);
		$elements['next_page'] = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.$n_page.'">'.$JoCo->lang['next_page'].'</a>';
		$elements['previous_page'] = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.$p_page.'">'.$JoCo->lang['previous_page'].'</a>';

		$lastPage = ($allRecs % $perPage == 0) ? ($allRecs / $perPage) : ($allRecs / $perPage) + 1;
		settype($lastPage, "integer");
		if( $page == $lastPage )
			$elements['next_page'] = '<span class="unpublishedNode">'.$JoCo->lang['next_page'].'</span>';
		if($page == 1)
			$elements['previous_page'] = '<span class="unpublishedNode">'.$JoCo->lang['previous_page'].'</span>';
	}
	
	$output = implode("",$indvs);
	$output = $JoCo->replace_all($JoCo->gv, $output);
	$output = $JoCo->replace_all($JoCo->lang, $output, true);
	
	// Set comments output as [+comments+]
	$elements['comments'] = $output;
}

// set class for current view link
if( isset($_GET_WHERE) )
{
	$hereImg = '<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'images/accept_item.png" width="16" />';
	switch( $_GET_WHERE )
	{
		case "id>0" : $elements["hereImg_2"] = $hereImg; break;
		case "published=0" : $elements["hereImg_0"] = $hereImg; break;
		case "published=1" : $elements["hereImg_1"] = $hereImg; break;
		case "createdon>".$JoCo->permission['lastActive'] : $elements["hereImg_3"] = $hereImg; break;
		case "createdby=".$_SESSION['mgrInternalKey'] : $elements["hereImg_4"] = $hereImg; break;
		case 	
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".createdby = ".$_SESSION['mgrInternalKey']
			: $elements["hereImg_5"] = $hereImg; break;
		case 	
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".publishedby = ".$_SESSION['mgrInternalKey']
			: $elements["hereImg_6"] = $hereImg; break;
		case 	
			$JoCo->gv['jot_content_tbl'].".uparent=".$modx->getFullTableName("site_content").".id 
			AND 
			".$modx->getFullTableName("site_content").".editedby = ".$_SESSION['mgrInternalKey']
			: $elements["hereImg_7"] = $hereImg; break;
		
		default :
			if($_REQUEST['reffer'] == 1)
				break;
			$elements["hereImg_".$JoCo->permission['defaultView']] = $hereImg; break;
	}
}


// Print output
if( !empty($JoCo->error) )
	$moduleHTML = str_replace("[+errors+]", '<div id="errors">'.implode("<br />",$JoCo->error).'</div>', $moduleHTML);
else
	$moduleHTML = str_replace("[+errors+]", "", $moduleHTML);

$moduleHTML = $JoCo->replace_all($checkedElements, $moduleHTML);
$moduleHTML = $JoCo->replace_all($JoCo->gv, $moduleHTML);
$moduleHTML = $JoCo->replace_all($elements, $moduleHTML);
$moduleHTML = $JoCo->replace_all($JoCo->lang, $moduleHTML, true);
$moduleHTML = preg_replace('~\[\+(.*?)\+\]~', "", $moduleHTML );	// remove forgotten placeholders ;)  : document.parser.class.inc.php 2217

echo $moduleHTML;
// JoCo end
?>