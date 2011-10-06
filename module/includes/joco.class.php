<?php
/*********************************
Module : JoCo (Jot Comments module) : JoCo class
Version : 0.9.1.3
Author : AHHP ~ Boplo.ir
*********************************/
if(!$modx) die("<h1>Forbidden!</h1>");

class JoCo {

	var $gv = array();	// General variables
	var $lang;	// language array
	var $customFunctions;	// JoCo custom actions object
	var $permission = array();	// JoCo permission array
	var $error = array();	// contains errors
	var $NP;	// an object for elements which need permissions to display
	
	function JoCo()
	{
		global $modx;
		
		// create table
		$modx->db->query("
			CREATE TABLE IF NOT EXISTS `".$modx->db->config['table_prefix']."joco_permissions` (
			  `id` int(10) NOT NULL auto_increment,
			  `internalKey` int(10),
			  `publish` int(1) NOT NULL default '0',
			  `unpublish` int(1) NOT NULL default '0',
			  `edit` int(1) NOT NULL default '0',
			  `remove` int(1) NOT NULL default '0',
			  `submit` int(1) NOT NULL default '0',
			  `logging` int(1) NOT NULL default '0',
			  `viewAll` int(1) NOT NULL default '1',
			  `viewPublished` int(1) NOT NULL default '1',
			  `viewUnpublished` int(1) NOT NULL default '1',
			  `ip` int(1) NOT NULL default '0',
			  `webUsers` int(1) NOT NULL default '0',
			  `createdDocs` int(4) NOT NULL default '0',
			  `publishedDocs` int(4) NOT NULL default '0',
			  `editedDocs` int(4) NOT NULL default '0',
			  `search` int(1) NOT NULL default '0',
			  `permission` int(1) NOT NULL default '0',
			  `jotCall` int(1) NOT NULL default '0',
			  `summary` int(1) NOT NULL default '1',
			  `defaultView` int(1) NOT NULL default '0',
			  `defaultTheme` int(1) NOT NULL default '1',
			  `resPerPage` int(3) NOT NULL default '30',
			  `summaryResPerPage` int(3) NOT NULL default '50',
			  `jotCallResPerPage` int(3) NOT NULL default '50',
			  `changeTheme` int(1) NOT NULL default '1',
			  PRIMARY KEY  (`id`, `internalKey`)
			) ENGINE = MyISAM;"
		);
			
		// General variables
		$this->gv = array(
			'jot_content_tbl' => $modx->getFullTableName("jot_content")
			,'jot_fields_tbl' => $modx->getFullTableName("jot_fields")
			,'jot_subscriptions_tbl' => $modx->getFullTableName("jot_subscriptions")
			,'joco_permissions_tbl' => $modx->getFullTableName("joco_permissions")
			,'basePath' => $modx->config['base_path']
			,'baseUrl' => $modx->config['base_url']
			,'modulePath' => 'assets/modules/JoCo/'
			,'lang' => $modx->config['manager_language']
			,'dir' => $modx->config['manager_direction']
			,'lang_attr' => $modx->config['manager_lang_attribute']
			,'manager_theme' => $modx->config['manager_theme']
			,'alignRight' => ($modx->config['manager_direction']=="rtl") ? "right" : "left"
			,'alignLeft' => ($modx->config['manager_direction']=="rtl") ? "left" : "right"
			,'mKey' => $_GET['id'] // module ID
		);
		
		// include lang file. it contains $JoCo_lang
		if( file_exists($this->gv['basePath'].$this->gv['modulePath'].'langs/'.$this->gv['lang'].'.php') )
			include_once $this->gv['basePath'].$this->gv['modulePath'].'langs/'.$this->gv['lang'].'.php';
		else
			include_once $this->gv['basePath'].$this->gv['modulePath'].'langs/english.php';
		$this->lang = $JoCo_lang;
		
		// includeneedPermissions.php file. it contains elementsPermission class  and CrElements and generals methods.
		include($this->gv['basePath'].$this->gv['modulePath'].'includes/needpermissions.php');
		$this->NP = new elementsPermission;
		
		// include customActions.php file. it contains customFunctions class and onBeforePublish, onBeforeUnpublish, onBeforeDelete, onBeforeEdit, onBeforeSetOutputRow methods.
		include($this->gv['basePath'].$this->gv['modulePath'].'includes/customactions.php');
		$this->customFunctions = new customFunctions();	
		
		$this->hasPermission();	// get permissions from JoCo table
	}
	
	
	
	/**
	 * publish / unpublish / edit and delete a comment.
	 *
	 * @param	integer		$id : ID of comment.
	 * @param	array		$update : associative array for UPDATE comment row.
	 * @param	string		$type : type of action, [ "publish" | "unpublish" | "edit" | "delete" ]
	 * @return	void
	 */	
	function changeComment($ids, $update, $type)
	{
		global $modx;
		$comment_ids = array();
		
		if( is_array($ids) )		$comment_ids = $ids;	//get checkbox IDs
		else		$comment_ids[] = $ids;	// get ajax ID
		
		foreach($comment_ids as $comment_id)
		{
			$comment_id = $modx->db->escape($comment_id);
			
			switch($type)
			{
				case 'publish' :
					$this->customFunctions->onBeforePublish($comment_id); /* CUSTOM FUNCTION */
					$modx->db->update($update, $this->gv['jot_content_tbl'], "id=$comment_id");
					break;
				
				case 'unpublish' :
					$this->customFunctions->onBeforeUnpublish($comment_id); /* CUSTOM FUNCTION */
					$modx->db->update($update, $this->gv['jot_content_tbl'], "id=$comment_id");
					break;
				
				case 'edit' :
					$this->customFunctions->onBeforeEdit($comment_id); /* CUSTOM FUNCTION */
					$modx->db->update($update, $this->gv['jot_content_tbl'], "id=$comment_id");
					break;
				
				case 'delete' :
					$this->customFunctions->onBeforeDelete($comment_id); /* CUSTOM FUNCTION */
					$modx->db->delete($this->gv['jot_content_tbl'], "id=$comment_id");
					$modx->db->delete($this->gv['jot_fields_tbl'], "id=$comment_id");
					break;
				
				default :
					break;
			}
			// log publish action in "manager_log" table if $JoCo->permission['logging'] is 1
			$this->managerLog( str_replace("[+comment_id+]", $comment_id, $this->lang[$type.'_log']) );
		}
	}
	
	
	
	/**
	 * Create a comment row output included HTML tpls, permission check and etc.
	 *
	 * @param	array		$r : a comment row associative array from "jot_content" table.
	 * @param	string		$tpl : a comment row HTML template, contains columns name of "jot_content" table as placeholder such as [+tagid+]
	 * @param	array		$checkedElements : associative array contains some elements that need permissions.
	 * @param	integer		$count : each row counter, for alternative format in output.
	 * @return	string		ready to print template of comments.
	 */	
	function getIndv($r, $tpl, $checkedElements, $count=false, $edit=false)
	{
		
		/* CUSTOM FUNCTION */
		/*****************************************************************/
		if(!$edit)	// editing use this method too. so filtering should not affect edit output.
			if( $this->customFunctions->onBeforeCheckRow($r) == false)
				return "";
		/*****************************************************************/
		
		global $modx;
		$this->gv['un_publish'] = ($r['published'] == 1) ? 'published' : 'unpublished';
		
		$r['content'] = htmlspecialchars($r['content'], ENT_NOQUOTES);
		$r['content'] = str_replace('\"', '"', $r['content']);
		$r['content'] = str_replace("\'", "'", $r['content']);
		
		// check user's permissions for documnets which user has created / published or edited.
		$newPermission = $this->myZone($r['uparent']);
		foreach($newPermission as $key => $val)		$this->permission[$key] = $val;
		
		$checkedElements = array_merge($this->NP->CrElements($this->permission, $r['published']),$this->NP->generals($this->permission));
		$tpl = $this->replace_all($checkedElements, $tpl); // replace new elements
		
		// get comment custom fields if exists
		if( $select_fields = $modx->db->select("*", $this->gv['jot_fields_tbl'], "id=".$r['id']) )
			while( $row = $modx->db->getRow($select_fields) )
				$r['label'][ $row["label"] ] = $row["content"];
		
		// get labels from a comment that a site user created it.
		if( $r['createdby'] != 0 )	// if a GEUST creates a comment, value of "createdby" column will be 0
		{
			if( $r['createdby'] > 0 ) 	$userTable = $modx->getFullTableName("user_attributes");	// manager user ID
			if( $r['createdby'] < 0 ) 	$userTable = $modx->getFullTableName("web_user_attributes");	// Jot saves web user IDs as negative
			
			$userRow = $modx->db->getRow( $modx->db->select("fullname, email", $userTable , "internalKey=".$r['createdby']) );
			$r['label']['user'] = $userRow["fullname"];
			$r['label']['email'] = $userRow["email"];
		}
		
		/* CUSTOM FUNCTION */
		/*****************************************************************/
		$r_alt = $this->customFunctions->onBeforeSetOutputRow($r, $tpl);
		$r = array_merge($r, $r_alt[0]);
		if($r_alt[1])	$tpl = $r_alt[1];
		/*****************************************************************/
		
		// [+ "jot_content" column name +]  replaces with their values in row tpl
		foreach($r as $k => $v)
			$tpl = str_replace("[+".$k."+]" , "$v" , $tpl);
		
		// set labels output
		if( array_key_exists('label', $r) )
		{
			$labels = $r['label'];
			$label_p = array();
			foreach($labels as $k => $v)
			{
				// seacrh lang file for a label if exists
				$new_k = strtolower($k);
				$lang_k = ($this->lang[$new_k]) ? $this->lang[$new_k] : $k;
				
				$label_p[] = '<span class="unpublishedNode">'.$lang_k.':</span> '.$v.'<br />';	// an inline class!!!
			}
			$tpl = str_replace("[+labels+]",implode('', $label_p), $tpl);
		}
		else
			$tpl = str_replace("[+labels+]", '', $tpl);	// remove [+labels+] if there was no label.
		
		
		/*
		* highlight strings search from search script
		* highlight codes from str_highlight function
			* @author      Aidan Lister <aidan@php.net>
			* @version     3.1.1
			* @link        http://aidanlister.com/repos/v/function.str_highlight.php
		*
		*/
		if( array_key_exists("highlight", $_POST) )
		{
			$pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#i';
	      $needle_s = preg_quote($_POST['highlight']);
			$regex = sprintf($pattern, $needle_s);
			$r['content'] = preg_replace($regex, '<span class="highlight">\1</span>', $r['content']);
		}
		
		
		$tpl = str_replace("[+pagetitle+]" , $this->id_to_title($r['uparent']) , $tpl);
		$tpl = str_replace("[+message+]" , nl2br($r['content']) , $tpl);
		$tpl = str_replace("[+url+]" , $modx->makeUrl($r['uparent']) , $tpl);
		$tpl = str_replace("[+time+]" , $this->JoCoDate("%d %B %Y at %H:%S", $r['createdon']) , $tpl);
		
		// add 'myComment' to class attribute of each row tpl for indicating user's comments
		$myComment = ($_SESSION['mgrInternalKey'] == $r['createdby']) ? 'myComment' : '';
		$tpl = str_replace("[+myComment+]" , $myComment , $tpl);
		
		// create a short message for compressed theme.
		$subMessage = str_replace(array("<br>","<br/>","<br />", "<BR>","<BR/>","<BR />"), " ", substr( nl2br($r['content']), 0, 100));
		$tpl = str_replace("[+smallMessage+]" , "$subMessage...." , $tpl); // compressed message
		
		// set alternative class
		$class = ($count % 2 == 0) ? "commentsTable" : "commentsTable-alt";
		$tpl = str_replace("[+commentsTable+]" , $class , $tpl); // compressed alt class
		
		// indicator after search, start *************************************************
		if( array_key_exists("search", $_POST) )	$tpl = $this->searchResult_refrences($r, $tpl);
		$tpl = str_replace("[+resultFrom+]" , "" , $tpl); // remove placeholder if search has not run
		// indicator after search, end *************************************************
		
		
		$tpl = $this->replace_all($this->gv, $tpl);	// replace general vars
		$tpl = $this->replace_all($this->lang, $tpl, true);	// replace lang values
		return $tpl;
	}

	
	
	/**
	 * Check "joco_permissions" table to get users permissions
	 *
	 * @return	void.
	 */		
	function hasPermission()
	{
		global $modx;
		
		$select = $modx->db->select("*", $this->gv['joco_permissions_tbl'], "internalKey=".$_SESSION['mgrInternalKey']);
		
		// this user is new so JoCo inserts a new row for him/her
		if($modx->db->getRecordCount($select) == 0)
		{
			// Administrators are full access
			if($_SESSION['mgrRole'] == 1)
				$insert = array(
					"internalKey" => $_SESSION['mgrInternalKey'],"publish" => 1,"unpublish" => 1
					,"edit" => 1,"remove" => 1,"submit" => 1,"ip" => 1, "webUsers" => 1,"logging" => 1
					,"viewAll" => 1,"viewPublished" => 1,"viewUnpublished" => 1,"publishedDocs" => 1234
					,"createdDocs" => 1234,"editedDocs" => 1234,"search" => 1,"permission" => 1,"summary" => 1, "jotCall" => 1
				);
			else
				$insert = array("internalKey" => $_SESSION['mgrInternalKey']);
			
			$insert = $modx->db->insert($insert, $this->gv['joco_permissions_tbl']);
			$this->hasPermission();	// user's row exists now so get his/her permissions
		}
		else
		{
			$Row = $modx->db->getRow($select);	// get user permissions
			$this->permission = $Row;
			$this->permission['lastActive'] = $this->id_to_fullname($_SESSION['mgrInternalKey'], "lastlogin");
			/*
			$this->permission['publish'] 		$this->permission['unpublish']		$this->permission['edit']
			$this->permission['remove']		$this->permission['submit']		$this->permission['ip']		$this->permission['webUsers']
			$this->permission['logging'] = log actions in "manager_log" table permission
			$this->permission['viewAll']		$this->permission['viewPublished']		$this->permission['viewUnpublished']
			$this->permission['createdDocs'] 		$this->permission['publishedDocs']		$this->permission['editedDocs']
			$this->permission['search']		$this->permission['permisson']		$this->permission['summary']
			$this->permission['defaultView']		$this->permission['defaultTheme']		$this->permission['changeTheme']
			$this->permission['resPerPage'] 		$this->permission['summaryResPerPage']
			*/
		}
	}
	
	
	
	/**
	 * Check WHERE statement to find actions which need permissions
	 *
	 * @param	string		$WHERE : SQL WHERE statement
	 * @return	string		checked SQL WHERE statement
	 */	
	function checkWHERE($WHERE)
	{
		if(strstr($WHERE,"id>0"))
		{
			if($this->permission['viewAll'] == 0)
			{
				$this->error[] = $this->lang['viewAll_denied'];
				$replace = $this->check_AND("id>0"); // check AND words before slice WHERE
				$new_WHERE = str_replace("", $replace, $WHERE);
			}
		}

		if(strstr($WHERE,"published=1"))
		{
			if($this->permission['viewPublished'] == 0)
			{
				$this->error[] = $this->lang['viewPublished_denied'];
				$replace = $this->check_AND("published=1"); // check AND words before slice WHERE
				$new_WHERE = str_replace("", $replace, $WHERE);
			}
		}

		if(strstr($WHERE,"published=0"))
		{
			if($this->permission['viewUnpublished'] == 0)
			{
				$this->error[] = $this->lang['viewUnpublished_denied'];
				$replace = $this->check_AND("published=0"); // check AND words before slice WHERE
				$new_WHERE = str_replace("", $replace, $WHERE);
			}
		}
		
		if($this->checkReferer() == false)
			return "id<0";
		
		return ($new_WHERE) ? $new_WHERE : $WHERE;
	}
	
	
	
	/**
	 * Check WHERE statement to find useless AND words that make SQL errors
	 *
	 * @param	string		$piece : SQL WHERE statement
	 * @return	string		checked SQL WHERE statement
	 */	
	function check_AND($piece)
	{
		if( strstr($WHERE, "AND $piece") )
			$replace = "AND $piece";
		elseif( strstr($WHERE, "$piece AND") )
			$replace = "$piece AND";
		else
			$replace = $piece;
		
		return $replace;
	}
	
	
	
	/**
	 * Explode a string by comma
	 *
	 * @param	string		$str : input string that have comma
	 * @return	array		array contains exploded strings
	 */	
	function explodeByComma($str)
	{
		$str = str_replace(" ", "", $str);	//remove spaces
		if( strpos($str, ",") )
		{
			// remove bad comma ing
			$invalid_comma = array(", ", " ,", " , ");
			$str = str_replace($invalid_comma, ",", $str);
			
			$arr = explode(",", $str);
		}
		else
			$arr = ($str == "") ? array() : array($str);
		
		return $arr;
	}
	
	
	
	/**
	 * replace values of an associative array with it's key
	 *
	 * @param	array		$assoc_array : array content data.
	 * @param	string		$source : string to find and replace in it.
	 * @param	boolean		$isLang : if true, method replaces strings that start with "lang"
	 * @return	string		replced string.
	 */
	function replace_all($assoc_array, $source, $isLang=false)
	{
		if( !is_array($assoc_array) )	$assoc_array = array();
		$lang = ($isLang) ? "lang" : "";
		
		// $isLang=false : $assoc_array['alpha'] replaces with [+alpha+] 
		// $isLang=true : $assoc_array['alpha'] replaces with [lang+alpha+] 
		foreach($assoc_array as $key => $val)
			$source = str_replace("[".$lang."+".$key."+]", "$val", $source);
		
		return $source;
	}
	
	
	
	/**
	 * Gets a comma delimated document group names and retuen document IDs of them.
	 *
	 * @param	string		$docGroups : comma delimated document groups name.
	 * @return	array		document IDs of document groups.
	 */
	function getDocGroupDocs($docGroups)
	{
		global $modx;
		
		$docGroup_arr = $this->explodeByComma($docGroups);	// explode string to array
		
		$where = "name IN(" .implode(",", $docGroup_arr). ")";
		$select = $modx->db->select("id", $modx->getFullTableName("documentgroup_names"), $where);
		
		if( $modx->db->getRecordCount($select) == 0 )
			return false;
		
		$wg_tbl =  $modx->getFullTableName("document_groups");
		$docs = array();
		while( $r = $modx->db->getRow($select) )
		{
			$select_docs = $modx->db->select("document", $wg_tbl, "document_group=".$r['id']);
			while( $row = $modx->db->getRow($select_docs) )
				$docs[] = $row["document"];
		}
		
		return $docs;
	}
	
	
	
	/**
	 * Gets a comma delimated web group names and retuen user IDs of them.
	 *
	 * @param	string		$webGroup : comma delimated web groupsnames.
	 * @return	array		user IDs of web groups.
	 */
	function getWebGroupUsers($webGroup)
	{
		global $modx;
		
		if( empty($webGroup) )		return array();
		$webGroup_arr = $this->explodeByComma($webGroup);	// explode string to array
		
		$where = "name IN(" .implode(",", $webGroup_arr). ")";
		$select = $modx->db->select("id", $modx->getFullTableName("membergroup_names"), $where);
		
		if( $modx->db->getRecordCount($select) == 0 )
			return false;		
		
		$wg_tbl =  $modx->getFullTableName("member_groups");
		$members = array();
		while( $r = $modx->db->getRow($select) )
		{
			$select_members = $modx->db->select("member", $wg_tbl, "user_group=".$r['id']);
			while( $row = $modx->db->getRow($select_members) )
				$members[] = $row["member"];
		}
		
		return $members;
	}



	/**
	 * finds column value of a document by it's ID from "site_content" table.
	 *
	 * @param	integer		$id : ID of document.
	 * @param	string		$titleField : column name of output.
	 * @param	string		$where : custom WHERE statement.
	 * @return	string		value of $titleField column.
	 */
	function id_to_title($id, $titleField="pagetitle", $where="")
	{
		global $modx;
		
		$where = ( empty($where) ) ? "id=$id" : "id=$id AND $where";
		$select = $modx->db->select($titleField, $modx->getFullTableName("site_content"), $where);
		if( $modx->db->getRecordCount($select) == 0 )
			return false;
		$row = $modx->db->getRow($select);
		
		return $row[$titleField];
	}





	/**
	 * finds column value of a user by it's ID from "user_attributes" table.
	 *
	 * @param 	integer		$id : ID of user.
	 * @param	string		$field : column name of output.
	 * @param	string		$where : custom WHERE statement.
	 * @return	string		value of $field column.
	 */	
	function id_to_fullname($id, $field="fullname", $table="user_attributes", $where="")
	{
		global $modx;
		
		if($table == "CHECKIT")
			$table = ($id > 0) ? "user_attributes" : "web_user_attributes";
		
		$where = ( empty($where) ) ? "internalKey=".abs($id) : "internalKey=".abs($id)." AND $where";
		$select = $modx->db->select($field, $modx->getFullTableName($table), $where);
		if( $modx->db->getRecordCount($select) == 0 )
			return false;
		$row = $modx->db->getRow($select);
		
		return ( isset($row[$field]) ) ? $row[$field] : $row['email'];
	}



	/**
	 * gets content of a file.
	 *
	 * @param	string		$url : URL of file. "base_path" will add automatically.
	 * @return	string		content of file.
	 */	
	function fileContent($url)
	{
		if( function_exists("file_get_contents") )
			return file_get_contents($this->gv['base_path'].$url);
		else
		{
			$output = "";
			$fp = fopen($this->gv['base_path'].$url , "r");
			while( ! feof($fp) )
				$output .= fgets($fp);
			return $output;
		}
	}



	/**
	 * gets an associative array and filters the values by escape and <s>stripTags</s> functions.
	 *
	 * @param	array		$assoc : an associative array.
	 * @return	array		an associative array.
	 */		
	function escape($assoc, $validTags="<br>")
	{
		global $modx;
		
		$output = array();
		foreach($assoc as $key => $val)
		{
			if( is_string($val) )
				$val = $modx->db->escape($val);
			$output[$key] = $val;
		}
		return $output;
	}



	/**
	 * log an event in "manager_log" table with a custom message.
	 *
	 * @param	string		$message : log message.
	 * @return	void
	 */	
	function managerLog($message)
	{
		if($this->permissiom['logging'] == 0)
			return;
		
		global $modx;
		$insertLog = array(
			"timestamp" => time(),
			"internalKey" => $_SESSION['mgrInternalKey'],
			"username" => $_SESSION['mgrShortname'],
			"action" => 112, // module execution
			"itemid" => $_REQUEST['itemid'],
			"itemname" => $_REQUEST['itemname'],
			"message" => $message
		);
		$modx->db->insert($insertLog,$modx->getFullTableName("manager_log"));
	}



	/**
	 * generate a special farsi date string from a timestamp.
	 *
	 * @param	integer		$stamp : input timestamp.
	 * @param	string		$format : output format. only " Y, F, m, d, H, i " are valid.
	 * @return	string		output by $format format.
	 */	
	function jdate($stamp, $format="d F Y H:i")
	{
		global $modx;
		
		if( file_exists($this->gv['base_path'].$this->gv['modulePath']."includes/jcalender.class.php") )
		{
			require_once($this->gv['base_path'].$this->gv['modulePath']."includes/jcalender.class.php");
			$cal = new Calendar();
		}
		else
			return date($format, $stamp);
		
		list($year,$month,$day,$hour,$minute) = explode( "-", date( "Y-m-d-H-i", $stamp ) );
		list($year,$month,$day) = $cal->gregorian_to_jalali($year,$month,$day);
		$monthName = $cal->ReturnMonthName($month);
		
		$format = str_replace("Y", $year, $format);
		$format = str_replace("F", $monthName, $format);
		$format = str_replace("m", $month, $format);
		$format = str_replace("d", $day, $format);
		$format = str_replace("H", $hour, $format);
		$format = str_replace("i", $minute, $format);
		$format = str_replace("at", "ساعت", $format);
		
		return $format;
	}
	
	
	
	/**
	 * change a jalali date to a timestamp.
	 *
	 * @param	string		$dateString : a jalali date with these formats: 1387-5-17 and 1387/7/23. 
	 * @return	integer		timestamp.
	 */		
	function jalali_to_stamp($dateString)
	{
		global $modx;
		if( strpos($dateString, "-") )
			list($year,$month,$day) = explode( "-", $dateString );
		if( strpos($dateString, "/") )
			list($year,$month,$day) = explode( "/", $dateString );
		
		if( file_exists($this->gv['base_path'].$this->gv['modulePath']."includes/jcalender.class.php") )
		{
			require_once($this->gv['base_path'].$this->gv['modulePath']."includes/jcalender.class.php");
			$cal = new Calendar();
			list($year,$month,$day) = $cal->jalali_to_gregorian($year,$month,$day);
		}
		
		return mktime(0, 0, 0, $month, $day, $year);
	}
	
	
	
	/**
	 * replace refrences of results from search with placeholders. used in getInv()
	 *
	 * @param	array	$r : comment row associative array.
	 * @param	string	$tpl : comment row tpl.
	 * @return	string	updated comment row tpl.
	 */	
	function searchResult_refrences($r, $tpl)
	{
		if( array_key_exists("indicateParents", $_POST) )
			if( in_array($r['uparent'], $_POST["indicateParents"]) )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_parents'] , $tpl);
		
		if( array_key_exists("indicateDocGroups", $_POST) )
			if( in_array($r['uparent'], $_POST["indicateDocGroups"]) )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_docgroups'] , $tpl);
		
		if( array_key_exists("indicateDocs", $_POST) )
			if( in_array($r['uparent'], $_POST["indicateDocs"]) )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_docs'] , $tpl);
		
		if( array_key_exists("indicateWebGroups", $_POST) )
			if( in_array($r['createdby'], $_POST["indicateWebGroups"]) )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_webgroups'] , $tpl);
		
		if( array_key_exists("indicateUsers", $_POST) )
			if( in_array($r['createdby'], $_POST["indicateUsers"]) )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_users'] , $tpl);
		
		if( array_key_exists("guestUsers", $_POST) )
			if( $r['createdby'] == 0 )
				$tpl = str_replace("[+resultFrom+]" , $this->lang['result_from_guestUsers'] , $tpl);

		return $tpl;
	}
	
	
	
	/**
	 * print date. if manager language is Persian, date will be Jalali.
	 *
	 * @param	string	$format : strftime() format.
	 * @param	integer	$stamp : timestamp.
	 * @return	string	date string.
	 */
	function JoCoDate($format=false, $stamp=false)
	{
		$format = ($format) ? $format : "%d %B %Y at %H:%S";
		$stamp = ($stamp) ? $stamp : time();
		return ($this->gv['lang'] == "persian") ? $this->jdate($stamp) : strftime($format, $stamp);
	}
	
	
	
	/**
	 * Create paginate numbers and links
	 *
	 * @param	integer	$all : number of all items.
	 * @param	integer	$perPage : number of items in a page.
	 * @param	string	$where : page GET queries to add to page url links.
	 * @param	integer	$curr : number of current page.
	 * @return	array	array contains page links.
	 */
	function setPaginate($all, $perPage, $where, $curr)
	{
		$nums = array();
		
		if($all >= $perPage) // number of results is NOT less than per page so there are more than one page.
			$allPages = ($all % $perPage == 0) ? ($all / $perPage) : ($all / $perPage) + 1;
		else
			$allPages = 1; // number of results is less than per page so there is only one page.
		
		for($i=1 ; $i<=$allPages ; $i++)
		{
			if($curr == $i) // this is current page
				$nums[] = '<span class="curr">'.$i.'</span>'; // an inline class!!!
			else
				$nums[] = '<span class="pageNum"><a href="index.php?a=112&id='.$this->gv['mKey'].$where.'&page='.$i.'">'.$i.'</a></span>'; // an inline class!!!
		}
		return $nums;
	}
	
	
	
	/**
	 * Check valid items of the page.
	 *
	 * @param	integer	$current : number of current item.
	 * @param	integer	$page : number of current page.
	 * @param	integer	$perPage :  number of items in a page.
	 * @return	array	true if item is valid for this page and false if not.
	 */
	function doPaginate($current, $page, $perPage)
	{
		$from = ($page - 1) * $perPage;
		$to = $page * $perPage;
		return ($current > $from && $current <= $to) ? true : false;
	}
	
	
	
	/**
	 * Check doc groups that user is not assigned to them and returns documnet that user has no permission to access them.
	 *
	 * @return	array	contains documnet IDs which user has no permission to access to them.
	 */
	function getForbiddenDocs()
	{
		global $modx;
		
		// get user docgroups
		$userDocGroups = $modx->getUserDocGroups();
		$userDocGroups = ($userDocGroups) ? $userDocGroups : false;
		
		$docGroupIds = array();
		if($userDocGroups)
		{
			// get ID of docgroups
			$where = "name IN(". implode(",", $userDocGroups) .")";
			$select = $modx->db->select("id", $modx->getFullTableName("documentgroup_names"), $where);
			while( $r = $modx->db->getRow($select) )
				$docGroupIds = $r['id']; // ID of user docGroups
		}
			
		// get documents in user docgroups
		$select = $modx->db->select("document,document_group", $modx->getFullTableName("document_groups"));
		
		$forbiddenDocs = array();
		while( $r = $modx->db->getRow($select) )
		{
			if( in_array($r['document_group'], $docGroupIds) )
				continue;
			$forbiddenDocs[] = $r['document']; // this document is member of other docgroups that user is not assigned to.
		}
		
		return $forbiddenDocs;
	}
	
	
	
	/**
	 * Check permission for documents that user has created / published or edited them
	 *
	 * @param	integer	$uparent : document ID of document contains comments.
	 * @return	array	new permission associative array.
	 */
	function myZone($uparent)
	{
		$newPermission = array();
		$docInfo = $this->getDocInfo($uparent);
		
		if( $docInfo["createdby"] == $_SESSION['mgrInternalKey'] )
		{
			if( strstr($this->permission["createdDocs"], "1")	)	$newPermission['publish'] = 1;
			if( strstr($this->permission["createdDocs"], "2")	)	$newPermission['unpublish'] = 1;
			if( strstr($this->permission["createdDocs"], "3")	)	$newPermission['remove'] = 1;
			if( strstr($this->permission["createdDocs"], "4")	)	$newPermission['edit'] = 1;
		}
		if( $docInfo["editedby"] == $_SESSION['mgrInternalKey'] )
		{
			if( strstr($this->permission["editedDocs"], "1")	)	$newPermission['publish'] = 1;
			if( strstr($this->permission["editedDocs"], "2")	)	$newPermission['unpublish'] = 1;
			if( strstr($this->permission["editedDocs"], "3")	)	$newPermission['remove'] = 1;
			if( strstr($this->permission["editedDocs"], "4")	)	$newPermission['edit'] = 1;
		}
		if( $docInfo["publishedby"] == $_SESSION['mgrInternalKey'] )
		{
			if( strstr($this->permission["publishedDocs"], "1")	)	$newPermission['publish'] = 1;
			if( strstr($this->permission["publishedDocs"], "2")	)	$newPermission['unpublish'] = 1;
			if( strstr($this->permission["publishedDocs"], "3")	)	$newPermission['remove'] = 1;
			if( strstr($this->permission["publishedDocs"], "4")	)	$newPermission['edit'] = 1;
		}	
		
		return $newPermission;
	}
	
	
	
	/**
	 * Get a documnet data
	 *
	 * @param	integer	$id : document ID
	 * @return	array	associative array of document row in "site_content" table.
	 */
	function getDocInfo($id)
	{
		global $modx;
		$row = $modx->db->getRow( $modx->db->select("*", $modx->getFullTableName("site_content"), "id=$id") );
		return $row;
	}
	
	
	
	/**
	 * Check referer
	 *
	 * @return	boolean 	True if query is valid else false.
	 */
	function checkReferer()
	{
		if(empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "")
		{
			// &WHERE and &show queries are no allowed without referer.
			if( isset($_GET['WHERE']) || isset($GET['show']) )
				return false;
		}
		else
		{
			// &WHERE and &show queries are allowed when referer is JoCo.
			if( isset($_GET['WHERE']) || isset($GET['show']) )
			{
				list($site , $query) = explode("?", $_SERVER['HTTP_REFERER']);
				$ampersand = strpos($query , "&amp;") ? "&amp;" : "&";
				$var =explode($ampersand, $query);
				
				$keys = array();
				foreach($var as $tmp)
				{
					list($key, $val) = explode("=", $tmp);
					$keys[$key] = $val;
				}
				
				if( ! (isset($keys['a']) && $keys['a']==112 && isset($keys['id']) && $keys['id']==$this->gv['mKey']) )
					return false;
			}
		}
		return true;
	}
	// end JoCo class
}
?>