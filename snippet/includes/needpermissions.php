<?php
/*********************************
Module : JoCo (Jot Comments module) : needPermissions
Author : AHHP ~ Boplo.ir
*********************************/



class elementsPermission
{
	// General elements
	function generals($permissions)
	{
		$allowed = array();
		$checkedElements = array();
		
		$allowed['submit'] = '<input type="submit" name="normal_submit" value="[lang+submit+]" style="width:120px; height:30px; text-align:center; background-color:#444; color:#9ebc5a; font-weight:bold; border:0" />';
		$allowed['viewAll'] = '<a href="[+pageUrl+]WHERE=id>0">[lang+all_comments+]</a><br />';
		$allowed['viewPublished'] = '<a href="[+pageUrl+]WHERE=published=1">[lang+published_comments+]</a><br />';
		$allowed['viewUnpublished'] = '<a href="[+pageUrl+]WHERE=published=0">[lang+unpublished_comments+]</a>';
		$allowed['permission'] = '
			<td class="button">
				<a href="[+pageUrl+]permission=1" onmouseover="document.getElementById(\'action_des\').innerHTML=\'[lang+permission_para+]\';"
				onmouseout="document.getElementById(\'action_des\').innerHTML=\'\';"><img src="[+sniPath+]/images/login.png" /><br />[lang+permission+]</a>
			</td>';
		$allowed['search'] = '
			<td class="button">
				<a href="[+pageUrl+]search=1" onmouseover="document.getElementById(\'action_des\').innerHTML=\'[lang+search_para+]\';"
				onmouseout="document.getElementById(\'action_des\').innerHTML=\'\';"><img src="[+sniPath+]/images/search.png" /><br />[lang+search+]</a>
			</td>';
		$allowed['summary'] = '
			<td class="button">
				<a href="[+pageUrl+]summary=1" onmouseover="document.getElementById(\'action_des\').innerHTML=\'[lang+summary_para+]\';"
				onmouseout="document.getElementById(\'action_des\').innerHTML=\'\';"><img src="[+sniPath+]/images/note_accept.png" /><br />[lang+summary+]</a>
			</td>';
		$allowed['ip'] = '[+secip+]<br />';


		foreach($allowed as $key => $value)
			$checkedElements[$key] = ($permissions[$key] == 1) ? $value : '';
		
		
		if($permissions['changeTheme'] == 1)
			$checkedElements['theme'] = ($permissions['defaultTheme'] == 1) ? '[lang+normalTheme+]  |  <a href="[+pageUrl+]theme=2">[lang+compressedTheme+]</a>' : '<a href="[+pageUrl+]theme=1">[lang+normalTheme+]</a>  |  [lang+compressedTheme+]';
		else
			$checkedElements['theme'] = ($permissions['defaultTheme'] == 1) ? '[lang+normalTheme+]': '[lang+compressedTheme+]';
		
		
		return $checkedElements;
	}

	
	// Critical elements
	function CrElements($permissions, $status=null)
	{
		$checkedElements = array();

		if($permissions['publish'] == 1)
		{
			$checkedElements['publish_ajax'] = '
				<a id="act_publish_[+id+]" href="javascript:'.($status==0 ? 'ajaxAction(\'publish\',\'[+id+]\')' : 'void(0)' ).';" title="[lang+publish+]" class="qbutton">
					<img id="img_act_publish_[+id+]" src="[+sniPath+]images/publish'.($status==0?'':'_dis').'.png" />
				</a>';
			$checkedElements['publish_chkbx'] = '
				<td width="3%" id="pub_col_[+id+]" class="chkbx-pub" style="background-image:url([+sniPath+]images/publish-col'.($status==0?'.gif':'_dis.png').');" title="[lang+publish+]">
					<input type="checkbox" id="pub_chkbx_[+id+]" name="comment_publish_ids[]" value="[+id+]" '.($status==0?'':'disabled="disabled"').' />
				</td>';
		}
		else
		{
			$checkedElements['publish_ajax'] = '
				<a id="act_publish_[+id+]" href="javascript:void(0);" title="[lang+publish+]" class="qbutton" style="display:none;">
					<img id="img_act_publish_[+id+]" src="[+sniPath+]images/publish.png" style="display:none;" />
				</a>';
			$checkedElements['publish_chkbx'] = '
				<td style="display:none;" width="3%" id="pub_col_[+id+]" class="chkbx-pub" style="background-image:url([+sniPath+]images/publish-col.gif);" title="[lang+publish+]">
					<input style="display:none;" type="checkbox" id="pub_chkbx_[+id+]" name="comment_publish_ids[]" value="[+id+]" />
				</td>';
		}
		

		if($permissions['unpublish'] == 1)
		{
			$checkedElements['unpublish_ajax'] = '
				<a id="act_unpublish_[+id+]" href="javascript:'.($status==1 ? 'ajaxAction(\'unpublish\',\'[+id+]\')' : 'void(0)').';" title="[lang+unpublish+]" class="qbutton">
					<img id="img_act_unpublish_[+id+]" src="[+sniPath+]images/unpublish'.($status==1?'':'_dis').'.png" />
				</a>';
			$checkedElements['unpublish_chkbx'] = '
				<td width="3%" id="unpub_col_[+id+]" class="chkbx-unpub" style="background-image:url([+sniPath+]images/unpublish-col'.($status==1?'.gif':'_dis.png').');" title="[lang+unpublish+]">
					<input type="checkbox" id="unpub_chkbx_[+id+]" name="comment_unpublish_ids[]" value="[+id+]" '.($status==1?'':'disabled="disabled"').' />
				</td>';
		}
		else
		{
			$checkedElements['unpublish_ajax'] = '
				<a id="act_unpublish_[+id+]" href="javascript:void(0);" title="[lang+unpublish+]" class="qbutton" style="display:none;">
					<img id="img_act_unpublish_[+id+]" src="[+sniPath+]images/unpublish.png"  style="display:none;" />
				</a>';
			$checkedElements['unpublish_chkbx'] = '
				<td style="display:none;" width="3%" id="unpub_col_[+id+]" class="chkbx-unpub" style="background-image:url([+sniPath+]images/unpublish-col.gif);" title="[lang+unpublish+]">
					<input type="checkbox"  style="display:none;" id="unpub_chkbx_[+id+]" name="comment_unpublish_ids[]" value="[+id+]" />
				</td>';
		}


		if($permissions['remove'] == 1)
		{
			$checkedElements['delete_ajax'] = '
				<a href="javascript:ajaxAction(\'delete\',\'[+id+]\');" title="[lang+delete+]" class="qbutton">
					<img src="[+sniPath+]images/delete.png" />
				</a>';
			$checkedElements['delete_chkbx'] = '
				<td width="3%" id="delete_col_[+id+]" class="chkbx-delete" title="[lang+delete+]">
					<input id="delete_chkbx_[+id+]" type="checkbox" name="comment_delete_ids[]" value="[+id+]" />
				</td>';
		}
		else
		{
			$checkedElements['delete_ajax'] = '
				<a style="display:none;" href="javascript:javascript:void(0);" title="[lang+delete+]" class="qbutton"></a>';
			$checkedElements['delete_chkbx'] = '
				<td style="display:none;" width="3%" id="delete_col_[+id+]" class="chkbx-delete" title="[lang+delete+]">
					<input style="display:none;" id="delete_chkbx_[+id+]" type="checkbox" name="comment_delete_ids[]" value="[+id+]" />
				</td>';
		}
		
		
		if($permissions['edit'] == 1)
		{
			$checkedElements['edit_ajax'] = '<a id="edit_[+id+]" href="javascript:editForm(\'[+id+]\');" title="[lang+edit+]" class="qbutton"><img src="[+sniPath+]images/blue-edit.gif" width="16" height="16"/></a>';
			$checkedElements['edit_form'] = '<div id="form_[+id+]" align="center" style="display:none;margin-top:20px;"><textarea id="textarea_[+id+]" cols="50" rows="5"></textarea><p class="button"><a href="javascript:ajaxAction(\'edit\',\'[+id+]\');">[lang+save+]</a></p></div>';
		}
		else
		{
			$checkedElements['edit_ajax'] = '<a id="edit_[+id+]" href="javascript:void(0);" title="[lang+edit+]" class="qbutton" style="display:none;" ></a>';
			$checkedElements['edit_form'] = '<div id="form_[+id+]" align="center" style="display:none;margin-top:20px;"><textarea id="textarea_[+id+]" cols="50" rows="5"></textarea><p class="button"><a href="javascript:void(0);">[lang+save+]</a></p></div>';
		}
		
		return $checkedElements;
	}
}







?>