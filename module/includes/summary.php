<?php
/*********************************
Module : JoCo (Jot Comments module) : summary
Author : AHHP ~ Boplo.ir


File map:
	# Get Subscriptions (ajax) : 28
	# Include HTML tpl file : 51
	# Get number of comments for each document : 54
	# Get number of subscriptions for each document : 93
	# Set sort link : 122
	# Set output : 159
		# Sorting by commnets : 160
		# Main data loop : 174
		# Do pagination : 179
		# Set paginate : 227
		# Complete output : 243
*********************************/

if(!$modx) die("<h1>Forbidden!</h1>");

$perPage = $JoCo->permission['summaryResPerPage'];	// number of results per page
$where = "&summary=1";	// this is added to queris, used for $JoCo->setPaginate()
$sortLink = "";
$page = ( !empty($_GET['page']) ) ? $_GET['page'] : 1; // number of requested page

// Get Subscriptions by ajax
if( array_key_exists("getSubs", $_GET) )
{
	$fullname_table = ($row['internalKey'] > 0) ? "user_attributes" : "web_user_attributes";
	$data = array();
	$uparent = $modx->db->escape($_GET['getSubs']);
	
	$select = $modx->db->select("userid, tagid", $JoCo->gv['jot_subscriptions_tbl'] , "uparent=$uparent");
	while( $row = $modx->db->getRow($select) )
	{
		$selectUser = $modx->db->select("fullname,email", $modx->getFullTableName($fullname_table), "internalKey=".abs($row['userid']));
		$r = $modx->db->getRow($selectUser);
		
		$tagid = ( !empty($row['tagid']) ) ? $row['tagid'] : "no_tagid";
		$data[$tagid][] = '<b>'.$r['fullname'].'</b> <small>('.$r['email'].')</small>';
	}

	foreach($data as $tag => $subs)
		echo "<u>$tag</u>: " . join(", ", $subs) . "<br />";
	exit;
}


// Include HTML tpl file
$SUMMARY = $JoCo->fileContent($JoCo->gv['basePath'].$JoCo->gv['modulePath'].'tpls/summary.html');

// Get number of comments for each document
$comment = array();
$select = $modx->db->select("uparent,createdon,createdby", $JoCo->gv['jot_content_tbl'] , "", "uparent ASC");
$i = 0; // counter for comments
while( $row = $modx->db->getRow($select) )
{
	if( empty($temp) ) // first loop: give value to $temp
	{
		$temp = $row['uparent'];
		$comment['lastActive'][$temp] = $row['createdon'];
		$comment['lastUser'][$temp] = $row['createdby'];
		$comment['comments'][$temp] = $i++;
		continue;
	}
	else
	{
		if( $temp == $row['uparent']  )
		{
			$comment['comments'][$temp] = $i++;
			if($row['createdon'] > $comment['lastActive'][$temp])
			{
				$comment['lastActive'][$temp] = $row['createdon'];
				$comment['lastUser'][$temp] = $row['createdby'];
			}
			continue;
		}
		else
		{
			$i = 0;
			$temp = $row['uparent'];
			$comment['comments'][$temp] = $i++;
			$comment['lastActive'][$temp] = $row['createdon'];
			$comment['lastUser'][$temp] = $row['createdby'];
		}
	}
}
unset($temp);


// Get number of subscriptions for each document
$subs = array();
$select = $modx->db->select("uparent", $JoCo->gv['jot_subscriptions_tbl'] , "", "uparent ASC");
$i = 0;
while( $row = $modx->db->getRow($select) )
{
	if( empty($temp) ) // first loop: give value to $temp
	{
		$temp = $row['uparent'];
		$subs[$temp] = $i++;
		continue;
	}
	else
	{
		if( $temp == $row['uparent']  )
		{
			$subs[$temp] = $i++;
			continue;
		}
		else
		{
			$i = 0;
			$temp = $row['uparent'];
			$subs[$temp] = $i++;
		}
	}
}


// Set sort link
if( array_key_exists('sortDir', $_GET) )
{
	if($_GET['sortDir'] == "ASC")
		$sortLink = '
			<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&sortBy=comments&sortDir=DESC" title="'.$JoCo->lang['DESC'].'">
				<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/down_arrow.png" width="16" alt="'.$JoCo->lang['DESC'].'" />
			</a>
		';
	if($_GET['sortDir'] == "DESC")
		$sortLink = '
			<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&sortBy=comments&sortDir=ASC" title="'.$JoCo->lang['ASC'].'">
				<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/up_arrow.png" width="16" alt="'.$JoCo->lang['ASC'].'" />
			</a>
		';
}
else
	$sortLink = '
		<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&sortBy=comments&sortDir=ASC" title="'.$JoCo->lang['ASC'].'">
			<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/down_arrow.png" width="16" alt="'.$JoCo->lang['ASC'].'" />
		</a>
	';


$table = '
<table id="summaryTable" align="center" style="width:90%">
	<tr>
		<th class="gridHeader">'.$JoCo->lang['id'].$sortLink.'</th>
		<th class="gridHeader">'.$JoCo->lang['title'].'</th>
		<th class="gridHeader" colspan="2">'.$JoCo->lang['comments'].'</th>
		<th class="gridHeader" colspan="2">'.$JoCo->lang['subscriptions'].'</th>
		<th class="gridHeader">'.$JoCo->lang['last_comment_on'].'</th>
		<th class="gridHeader">'.$JoCo->lang['last_comment_by'].'</th>
		<th></th>
	</tr>';


/************************ Set output ************************/
// Sorting by commnets
if( !empty($_GET['sortBy']) )
{
	if($_GET['sortBy'] == "comments")
	{
		if($_GET['sortDir'] == "ASC")
			ksort($comment["comments"]);
		
		if($_GET['sortDir'] == "DESC")
			krsort($comment["comments"]);
	}
}

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

	$comment['subs'][$uparent] = ( isset($subs[$uparent]) ) ? ($subs[$uparent]+1) : 0; // number of subs
	$lastUser = ($user = $JoCo->id_to_fullname($comment['lastUser'][$uparent])) ? $user : $JoCo->lang['guest'];
	
	$table .= '
		<tr>
			<td>'.$uparent.'</td>
			
			<td><a href="'.$modx->makeUrl($uparent).'" target="_blank">'.$JoCo->id_to_title($uparent).'</a></td>
			
			<td align="center"><b>'.($comment['comments'][$uparent]+1).'</b></td>
			
			<td align="center">
				<a href="index.php?a=112&id='.$JoCo->gv['mKey'].'&WHERE=uparent='.$uparent.'&reffer=1" title="'.$JoCo->lang['see_this_comments'].'">
						<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/home.png" width="20" alt="'.$JoCo->lang['see_this_comments'].'" />
					</a>
			</td>
			
			<td align="center"><b>'.$comment['subs'][$uparent].'</b></td>
			
			<td align="center" id="subscriptions_'.$id.'">
				'.(
					$comment['subs'][$uparent] > 0 ?
					'<a href="javascript:getSubscriptions('.$uparent.','.$id.');" title="'.$JoCo->lang['get_subscriptions'].'">
						<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/search_magnifier.png" width="16" alt="'.$JoCo->lang['get_subscriptions'].'" />
					</a>'
					:
					'<img src="'.$JoCo->gv['baseUrl'].$JoCo->gv['modulePath'].'/images/search_magnifier_dis.png" width="16" title="'.$JoCo->lang['there_is_no_subscription_here'].'" alt="'.$JoCo->lang['there_is_no_subscription_here'].'" />'
				).'
			</td>
			
			<td align="center">'.$JoCo->JoCoDate(false,$comment['lastActive'][$uparent]).'</td>
			
			<td align="center">'.$lastUser.'</td>
		</tr>
	';
	$id++;
}
$table .= '</table>';
$SUMMARY = str_replace("[+summaryResults+]", $table, $SUMMARY);


// Set paginate
$allRecs = count($comment['comments']);

$paginate = $JoCo->setPaginate($allRecs, $perPage, $where, $page);
$SUMMARY = str_replace("[+paginate+]", join("", $paginate), $SUMMARY);

$nextPage = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.($page+1).'">'.$JoCo->lang['next_page'].'</a>';
$prePage = '<a href="index.php?a=112&id='.$JoCo->gv['mKey'].$where.'&page='.( $page==1 ? 1 : ($page-1)).'">'.$JoCo->lang['previous_page'].'</a>';

$lastPage = ($allRecs % $perPage == 0) ? ($allRecs / $perPage) : ($allRecs / $perPage) + 1;
settype($lastPage, "integer");
if( $page == $lastPage )
	$nextPage = '<span class="unpublishedNode">'.$JoCo->lang['next_page'].'</span>';
if($page == 1)
	$prePage = '<span class="unpublishedNode">'.$JoCo->lang['previous_page'].'</span>';

// Complete output
$SUMMARY = str_replace("[+nextPage+]", $nextPage, $SUMMARY);
$SUMMARY = str_replace("[+previousPage+]", $prePage, $SUMMARY);
$SUMMARY = str_replace("[+allResults+]", $allRecs, $SUMMARY);
// JoCo will prints $SUMMARY contents
?>