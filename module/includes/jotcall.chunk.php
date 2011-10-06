<?php
/*********************************
Module : JoCo (Jot Comments module) : Jot Calls - chunks
Author : AHHP ~ Boplo.ir


File map:
	# Get jot calls (ajax) : 29
		# Check chunk : 33
		# Set output : 59
	# Update Call (ajax) : 67
	# Include HTML tpl file : 94
	# Set output : 116
		# Do pagination : 120
		# Set paginate : 146
		# Complete output : 162
*********************************/

if(!$modx) die("<h1>Forbidden!</h1>");

$perPage = $JoCo->permission['jotCallResPerPage'];	// number of results per page
$where = "&jotCall=1&chunks=1";	// this is added to queris, used for $JoCo->setPaginate()
$page = ( !empty($_GET['page']) ) ? $_GET['page'] : 1; // number of requested page

$role = $modx->db->getRow($modx->db->select("edit_chunk", $modx->getFullTableName("user_roles"), "id=".$_SESSION['mgrRole']));

// Get jot calls
if( array_key_exists("chunkId", $_GET) )
{
	$chunkId = $modx->db->escape($_GET['chunkId']);
	
	// Check Chunk
	$content = $modx->db->getValue($modx->db->select("snippet", $modx->getFullTableName("site_htmlsnippets"), "id=$chunkId"));
	
	$chunkCalls = array();
	preg_match_all('~\[\[Jot(.*?)\]\]~', $content , $cachedCalls);
	if($cachedCount = count($cachedCalls[1]))
		for ($i= 0; $i < $cachedCount; $i++)
		{
			$call = "[[Jot" .$cachedCalls[1][$i]. "]]";
			if($role['edit_chunk']==1)
				$edit = '<button id="edit_chunk_' .$chunkId. '_' .$i. '" onclick="javascript:goToEdit(\'chunk\' , ' .$chunkId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
			$chunkCalls[] = "<p id='chunk_{$chunkId}_{$i}' class='call'>{$edit}<span id='chunk_inner_{$chunkId}_{$i}'>{$call}</span></p>";
		}
	
	preg_match_all('~\[\!Jot(.*?)\!\]~', $content , $uncachedCalls);
	if($uncachedCount = count($uncachedCalls[1]))
	{
		for ($i= 0; $i < $uncachedCount; $i++)
		{
			$call = "[!Jot" .$uncachedCalls[1][$i]. "!]";
			if($role['edit_chunk']==1)
				$edit = '<button id="edit_chunk_' .$chunkId. '_' .$i. '" onclick="javascript:goToEdit(\'chunk\' , ' .$chunkId. ' , ' .$i. ');" title="' .$JoCo->lang['edit']. '" class="editLink">' .$JoCo->lang['edit']. '</button>';
			$chunkCalls[] = "<p id='chunk_{$chunkId}_{$i}' class='call'>{$edit}<span id='chunk_inner_{$chunkId}_{$i}'>{$call}</span></p>";
		}
	}
	unset($content, $tvSelect, $cachedCalls, $uncachedCalls, $sql, $tvRow);

	// Set Output
	if( !empty($chunkCalls) )	echo( join("<br />", $chunkCalls) );
	echo "<br />";
	exit;
}



// Update Call
if( array_key_exists("saveIt", $_GET) )
{
	if($role['edit_chunk'] != 1)
		exit;
	
	$sourceId = $modx->db->escape($_POST['sourceId']);	// site_htmlsnippets.id
	$oldCall = $_POST['oldCall'];	// current Jot call
	$newCall = $_POST['newCall'];	// updated Jot call
	
	$select = mysql_query("SELECT `snippet` FROM " .$modx->getFullTableName("site_htmlsnippets"). " WHERE id=$sourceId");
	$row = $modx->db->getRow($select);
	$contentValue = $row['snippet'];
	
	// Remove differnce in "&" and "&amp;" in DB-content and JoCo-generated call
	$oldCall = html_entity_decode($oldCall);
	$contentValue = html_entity_decode($contentValue);
	
	$update = array("snippet" => str_replace($oldCall, $newCall, $contentValue));
	unset($contentValue);
	
	$modx->db->update($update, $modx->getFullTableName("site_htmlsnippets") , "id=$sourceId");
	exit;
}



// Include HTML tpl file
$JOTCALL = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'tpls/jotcall.html');

$table = '
<table id="callTable" align="center" style="width:90%">
	<tr>
		<th class="gridHeader" width="5%">'.$JoCo->lang['id'].'</th>
		<th class="gridHeader">'.$JoCo->lang['title'].'</th>
		<th class="gridHeader">'.$JoCo->lang['description'].'</th>
		<th class="gridHeader" width="15%">'.$JoCo->lang['jotcall_get_calls'].'</th>
	</tr>';

	

$select = $modx->db->query("
	SELECT id, name, description
	FROM " .$modx->getFullTableName("site_htmlsnippets"). " 
	WHERE  `snippet` LIKE  '%[!Jot%' OR `snippet` LIKE  '%[[Jot%'
	ORDER BY id ASC
");


/************************ Set output ************************/
$id = 1; // item counter for $JoCo->doPaginate()
while($row = $modx->db->getRow($select))	// Main data loop
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
			<td>'.$row['id'].'</td>
			<td>'.$row['name'].'</td>
			<td>'.$row['description'].'</td>
			<td><a id ="control_'.$row['id'].'" href="javascript:getJotCalls('.$row['id'].',1);">'.$JoCo->lang['jotcall_show'].'</a></td>
		</tr>
		<tr><td colspan="4" id="calls_'.$row['id'].'" style="display:none;text-align:left;"></td></tr>
	';
	$id++;
}
$table .= '</table>';
$JOTCALL = str_replace("[+jotCallResults+]", $table, $JOTCALL);


// Set paginate
$allRecs = $modx->db->getRecordCount($select);

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