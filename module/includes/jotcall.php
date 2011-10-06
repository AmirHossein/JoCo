<?php
/*********************************
Module : JoCo (Jot Comments module) : Jot Calls
Author : AHHP ~ Boplo.ir


File map:
	# Go to chunks : 28
	# Get jot calls (ajax) : 39
		# Check document : 47
		# Check TVs : 73
		# Set output : 114
	# Update Call (ajax) : 128
	# Include HTML tpl file : 163
	# Set output : 208
		# Do pagination : 212
		# Set paginate : 243
		# Complete output : 259
*********************************/

if(!$modx) die("<h1>Forbidden!</h1>");

$perPage = $JoCo->permission['jotCallResPerPage'];	// number of results per page
$where = "&jotCall=1";	// this is added to queris, used for $JoCo->setPaginate()
$page = ( !empty($_GET['page']) ) ? $_GET['page'] : 1; // number of requested page


// Review Chunks
if( array_key_exists("chunks", $_GET) )
{
	include_once "jotcall.chunk.php";
	return;
}

$banned = $JoCo->getForbiddenDocs();
$role = $modx->db->getRow($modx->db->select("view_document,edit_document", $modx->getFullTableName("user_roles"), "id=".$_SESSION['mgrRole']));


// Get jot calls
if( array_key_exists("docId", $_GET) )
{
	$docId = $modx->db->escape($_GET['docId']);
	
	if(in_array($docId, $banned))
		exit;
	
	// Check document
	$content = $modx->db->getValue($modx->db->select("content", $modx->getFullTableName("site_content"), "id=$docId"));
	
	$documentCalls = array();
	preg_match_all('~\[\[Jot(.*?)\]\]~', $content , $cachedCalls);
	if($cachedCount = count($cachedCalls[1]))
		for ($i= 0; $i < $cachedCount; $i++)
		{
			$call = "[[Jot" .$cachedCalls[1][$i]. "]]";
			if($role['edit_document']==1)
				$edit = '<button id="edit_doc_' .$docId. '_' .$i. '" onclick="javascript:goToEdit(\'doc\' , ' .$docId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
			$documentCalls[] = "<p id='doc_{$docId}_{$i}' class='call'>{$edit}<span id='doc_inner_{$docId}_{$i}'>{$call}</span></p>";
		}
	
	preg_match_all('~\[\!Jot(.*?)\!\]~', $content , $uncachedCalls);
	if($uncachedCount = count($uncachedCalls[1]))
		for ($i= 0; $i < $uncachedCount; $i++)
		{
			$call = "[!Jot" .$uncachedCalls[1][$i]. "!]";
			if($role['edit_document']==1)
				$edit = '<button id="edit_doc_' .$docId. '_' .$i. '" onclick="javascript:goToEdit(\'doc\' , ' .$docId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
			$documentCalls[] = "<p id='doc_{$docId}_{$i}' class='call'>{$edit}<span id='doc_inner_{$docId}_{$i}'>{$call}</span></p>";
		}
	unset($cachedCalls, $uncachedCalls);
	
	
	// Check TVs
	$sql = "
		SELECT tv.caption, tvVal.value, tvVal.id
		FROM " . $modx->getFullTableName("site_tmplvars") . " tv INNER JOIN " . $modx->getFullTableName("site_tmplvar_contentvalues") . " tvVal
		WHERE tv.id = tvVal.tmplvarid AND tvVal.contentid=$docId
	";
	$tvSelect = $modx->db->query($sql);
	
	$tvCalls = array();
	while($tvRow = $modx->db->getRow($tvSelect))
	{
		$content = $tvRow['value'];
		$tvId = $tvRow['id'];
		
		preg_match_all('~\[\[Jot(.*?)\]\]~', $content , $cachedCalls);
		if($cachedTvCount = count($cachedCalls[1]))
			for ($i= 0; $i < $cachedTvCount; $i++)
			{
				$call = "[[Jot" . $cachedCalls[1][$i] . "]]";
				if($role['edit_document']==1)
					$edit = '<button id="edit_tv_' .$tvId. '_' .$i. '" onclick="javascript:goToEdit(\'tv\' , ' .$tvId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
				$tvCalls[$tvRow['caption']][] = "<p id='tv_{$tvId}_{$i}' class='call'>{$edit}<span id='tv_inner_{$tvId}_{$i}'>{$call}</span></p>";
			}
		
		preg_match_all('~\[\!Jot(.*?)\!\]~', $content , $uncachedCalls);
		if($uncachedTvCount = count($uncachedCalls[1]))
			for ($i= 0; $i < $uncachedTvCount; $i++)
			{
				$call = "[!Jot" . $uncachedCalls[1][$i] . "!]";
				if($role['edit_document']==1)
					$edit = '<button id="edit_tv_' .$tvId. '_' .$i. '" onclick="javascript:goToEdit(\'tv\' , ' .$tvId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
				$tvCalls[$tvRow['caption']][] = "<p id='tv_{$tvId}_{$i}' class='call'>{$edit}<span id='tv_inner_{$tvId}_{$i}'>{$call}</span></p>";
			}
	}
	unset($content, $tvSelect, $cachedCalls, $uncachedCalls, $sql, $tvRow);

	
	if(!$cachedCount && !$uncachedCount && !$cachedTvCount && !$uncachedTvCount)
		echo $JoCo->lang['jotcall_nothing_found'];
	
	
	// Output
	if( !empty($documentCalls) )	echo( join("<br />", $documentCalls) );
	if( !empty($tvCalls) )
	{
		echo "<br />";
		foreach($tvCalls as $caption => $valArray)
			echo "<b>$caption (TV)</b>" . join("<br />", $valArray);
	}
	echo "<br />";
	exit;
}



// Update Call
if( array_key_exists("saveIt", $_GET) )
{
	if($role['edit_document'] !=1 )
		exit;
	
	if($_POST['position'] == "doc" && in_array($_POST['sourceId'], $banned))
		exit;
	
	$position = $_POST['position'];	// doc | tv
	$sourceId = $modx->db->escape($_POST['sourceId']);	// site_content.id | site_tmplvar_contentvalues.id
	$oldCall = $_POST['oldCall'];	// current Jot call
	$newCall = $_POST['newCall'];	// updated Jot call
	
	$tbl = ($position == "doc") ? $modx->getFullTableName("site_content") : $modx->getFullTableName("site_tmplvar_contentvalues");
	$contentField = ($position == "doc") ? "content" : "value";
	
	$select = mysql_query("SELECT $contentField FROM $tbl WHERE id=$sourceId");
	$row = $modx->db->getRow($select);
	$contentValue = $row[$contentField];
	
	// Remove differnce in "&" and "&amp;" in DB-content and JoCo-generated call
	$oldCall = html_entity_decode($oldCall);
	$contentValue = html_entity_decode($contentValue);
	
	$update = array($contentField => str_replace($oldCall, $newCall, $contentValue));
	unset($contentValue);
	
	$modx->db->update($update, $tbl , "id=$sourceId");
	exit;
}




// Include HTML tpl file
$JOTCALL = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'tpls/jotcall.html');

// Get number of comments for each document
$comment = array();
$select = $modx->db->select("uparent", $JoCo->gv['jot_content_tbl'] , "", "uparent ASC");
$i = 0; // counter for comments
while( $row = $modx->db->getRow($select) )
{
	if(in_array($row['uparent'], $banned))
		continue;
	
	if( empty($temp) ) // first loop: give value to $temp
	{
		$temp = $row['uparent'];
		$comment['comments'][$temp] = $i++;
		continue;
	}
	else
	{
		if( $temp == $row['uparent']  )
		{
			$comment['comments'][$temp] = $i++;
			continue;
		}
		else
		{
			$i = 0;
			$temp = $row['uparent'];
			$comment['comments'][$temp] = $i++;
		}
	}
}
unset($temp);

$table = '
<table id="callTable" align="center" style="width:90%">
	<tr>
		<th class="gridHeader">'.$JoCo->lang['id'].'</th>
		<th class="gridHeader">'.$JoCo->lang['title'].'</th>
		<th class="gridHeader">'.$JoCo->lang['comments'].'</th>
		<th class="gridHeader">'.$JoCo->lang['jotcall_get_calls'].'</th>
	</tr>';


/************************ Set output ************************/
$id = 1; // item counter for $JoCo->doPaginate()
foreach($comment['comments'] as $uparent => $count)	// Main data loop
{
	// Do pagination
	$pagin = $JoCo->doPaginate($id, $page, $perPage);
	if($id > $page*$perPage) // all items of page have been parsed and others are for next pages
		break;
	if( $pagin == false )
	{
		$id++;
		continue;
	}

	
	$table .= '
		<tr>
			<td>'.$uparent.'</td>
			<td><a href="'.$modx->makeUrl($uparent).'" target="_blank">'.$JoCo->id_to_title($uparent).'</a></td>
			<td><b>'.($comment['comments'][$uparent]+1).'</b></td>
			<td><a id ="control_'.$uparent.'" href="javascript:getJotCalls('.$uparent.');">'.$JoCo->lang['jotcall_show'].'</a></td>
		</tr>
		<tr><td colspan="4" id="calls_'.$uparent.'" style="display:none;text-align:left;"></td></tr>
	';
	$id++;
}
$table .= '</table>';

// Check user 's roles.
if($role['view_document'] != 1)
	$table = $JoCo->lang['jotcall_view_document_role'];

$JOTCALL = str_replace("[+jotCallResults+]", $table, $JOTCALL);


// Set paginate
$allRecs = count($comment['comments']);

$paginate = $JoCo->setPaginate($allRecs, $perPage, $where, $page);
$JOTCALL = str_replace("[+paginate+]", join("", $paginate), $JOTCALL);

$nextPage = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.($page+1).'">'.$JoCo->lang['next_page'].'</a>';
$prePage = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.( $page==1 ? 1 : ($page-1)).'">'.$JoCo->lang['previous_page'].'</a>';

$lastPage = ($allRecs % $perPage == 0) ? ($allRecs / $perPage) : ($allRecs / $perPage) + 1;
settype($lastPage, "integer");
if( $page == $lastPage )
	$nextPage = '<span class="unpublishedNode">'.$JoCo->lang['next_page'].'</span>';
if($page == 1)
	$prePage = '<span class="unpublishedNode">'.$JoCo->lang['previous_page'].'</span>';

// Complete output
$JOTCALL = str_replace("[+nextPage+]", $nextPage, $JOTCALL);
$JOTCALL = str_replace("[+previousPage+]", $prePage, $JOTCALL);
$JOTCALL = str_replace("[+allResults+]", $allRecs, $JOTCALL);
// JoCo will prints $JOTCALL contents
?>