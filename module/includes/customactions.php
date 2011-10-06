<?php
/*********************************
Module : JoCo (Jot Comments module) : custom actions
Author : AHHP ~ Boplo.ir
*********************************/

class customFunctions
{
	/**
	 * merges extra data to commets data.
	 *
	 * @param	array		$row: KEY is "jot_content" column name and VALUE is value of column. $row['label'] is same but contains "jot_fields".
	 * @return	array		associative array contains $Row with extra comment data and new $tpl.
	 */
	function onBeforeSetOutputRow($row,$tpl)
	{
		$new_row = array();
		$new_tpl = "";
		return array($new_row, $new_tpl);
	}
	
	
	
	/**
	 * Check a comment before parsing.
	 *
	 * @param	array		$row: KEY is "jot_content" column name and VALUE is value of column. $row['label'] is same but contains "jot_fields".
	 * @return	boolean			TRUE will accept comment and FALSE will reject it in output.
	 */
	function onBeforeCheckRow($row)
	{
		return true;
	}	
	
	
	
	/**
	 * runs before update DB for publishing comment.
	 *
	 * @param	integer		$comment_id : ID of comment.
	 * @return	void
	 */
	function onBeforePublish($comment_id){}


	
	/**
	 * runs before update DB for unpublishing comment.
	 *
	 * @param	integer		$comment_id : ID of comment.
	 * @return	void
	 */
	function onBeforeUnpublish($comment_id){}
	
	
	
	/**
	 * runs before update DB for deleting comment.
	 *
	 * @param	integer		$comment_id : ID of comment.
	 * @return	void
	 */
	function onBeforeDelete($comment_id){}
	
	
	
	/**
	 * runs before update DB for editing comment
	 *
	 * @param	integer		$comment_id : ID of comment.
	 * @return	void
	 */
	function onBeforeEdit($comment_id){}
}
?>