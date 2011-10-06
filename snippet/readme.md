# JoCo 
JoCo is a tool to manage and moderate comments created by Jot snippet in [MODX Evolution](http://modx.com/evolution/).

This package contains **JoCo Snippet** to moderate comments in front-end by trusted webusers.


## Features
* Moderate comments created by **you**.
* Moderate last comments from your last login.
* Moderate comments in resources you've created, edited or published.
* Search in comments by string and highlighting, by webgroups, users, guests, document groups, documents, date ranges and etc.
* Moderators need permissions to do critical actions. Managers can set full or limited access for moderators.
* Review list of resources have comments and subscriptions in summary page.
* Moderators only can access to comments in resources they have permission to see them.
* JoCo snippet allows MODX managers to set permissions for webusers to moderate comments. So some trusted users can moderate comments without access to MODX manager.
* Log webusers actions in HTML files:

		/assets/snippets/JoCo/logs/logs.html


## Installation
1. Upload snippet files to

		/assets/snippets/JoCo
2. Go to MODX manager and create new snippet. Name it **JoCo**, **_Jot comments snippet 0.9.1.4_** as descriptions and contents of file below as codes:

		/assets/snippets/JoCo/JoCo.snippet.txt
3. Create a new resource, set template to **BLANK** and put snippet call in it.

## Notes
* JoCo snippet uses its own styles and scripts so put snippet call in a resource with blank template. If you want add something to template please modify JoCo tpls:

		/assets/snippets/JoCo/tpls/
* JoCo does NOT allow any other snippets to be called in current resource for security reasons.


## Snippet parameters
* **&lang**: Language of snippet. Default: _english_
* **&trustedGroups**: Comma separated webgroup names who can SEE the JoCo manager. Default: "" (No Group)
* **&trustedUsers**: Comma separated user IDs who can SEE the JoCo manager. Default: "" (No one)
* **&redirectPage**: ID of resource to redirect untrusted users who are not in _&trustedUsers_ and _&trustedGroups_. Default: _1_
* **&userDisabledPermissions**: Disabled options of PERMISSION part for specified users. These users can not change these permissions. e.g. `logging:1,2,3|search:1,2,3` means users with ID of 1 and 2 and 3 are NOT able to change their permissions for search and logging. Default: ""
* **&allDisabledPermissions**: Comma separated disabled options of PERMISSION part for all users. All users can not change these permissions. e.g. `publish,unpublish` means all users are NOT able to change their permissions for publish and unpublish. Default: _webUsers_
* **&userEnabledPermissions**: Enabled options of PERMISSION part for specifid users ID. These users can change these permissions. e.g. `logging:1,2,3|search:1,2,3` means users with ID of 1 and 2 and 3 are able to change their permissions for search and logging. Default: ""
* **&publishComments**: Allow publishing comments in snippet [1|0]. Default: _0_
* **&unpublishComments**: Allow unpublishing comments in snippet [1|0]. Default: _0_
* **&editComments**: Allow editing comments in snippet [1|0]. Default: _0_
* **&removeComments**: Allow deleting comments in snippet [1|0]. Default: _0_
* **&useSubmitButton**: Allow to see and use submit button in snippet [1|0]. Default: _0_
* **&logActions**: Allow to **LOG** user's actions in a HTML file in snippet [1|0]. Default: _0_
* **&viewAllComments**: Allow to use _view all_ link and see all comments in snippet [1|0]. Default: _0_
* **&viewPublishedComments**: Allow to use _view published_ link and see published comments in snippet [1|0]. Default: _0_
* **&viewUnpublishedComments**: Allow to use _view unpublished_ link and see unpublished comments in snippet [1|0]. Default: _0_
* **&seeIP**: Allow to see IP of each comment in snippet [1|0]. Default: _0_
* **&changeWebUsersPermission**: Allow to change WebUsers permissions in snippet [1|0]. Default: _0_
* **&createdDocsPermissions**: Allow to change comments of resources which current user has CREATED them.
value is a 4digit number:
        
        1: publish
        2: unpublish
        3: delete
        4: edit
        0: no permission   (Default)
So _1234_ means full permissions, _124_ means no delete access and etc. Default: _0_
* **&editedDocsPermissions**: Allow to change comments of resources which current user is the last editor of them.
value is a 4digit number:
        
        1: publish
        2: unpublish
        3: delete
        4: edit
        0: no permission   (Default)
So _1234_ means full permissions, _124_ means no delete access and etc. Default: _0_
* **&publishedDocsPermissions**: Allow to change comments of resources which current user is the last publisher of them.
value is a 4digit number:
        
        1: publish
        2: unpublish
        3: delete
        4: edit
        0: no permission   (Default)
So _1234_ means full permissions, _124_ means no delete access and etc.
* **&useSearch**: Allow to see and use _Search_ part in snippet [1|0]. Default: _0_
* **&usePermission**: Allow to see and use _Permission_ part in snippet [1|0]. Default: _0_
* **&useSummary**: Allow to see and use _Summary_ part in snippet [1|0]. Default: _0_
* **&defaultMainView**: Set default view:

    	0: unpublished (Default)
    	1: published
    	2: all
    	3: recent
    	4: user's comments
    	5: nothing
* **&defaultTheme**: Set default theme:

    	1: normal (Default)
    	2: compressed
* **&changeTheme**: Allow to change theme (see theme links) [1|0]. Default: _1_
* **&main_resultPerPage**: Number of results per page in _Main_ part. Default: _30_
* **&summary_resultPerPage**: Number of results per page in _Summary_ part. Default: _50_
* **&dir**: Snippet direction [ltr|rtl]. Default: Manager direction
* **&changeTheme**: Allow to change theme (see theme links) [1|0]. Default: _1_




## Development
* ### Custom fields
JoCo reads custom fields from _jot_fields_ table. Label of custom fields come from Jot parameters. You can set readable labels for custom fields. To do that just add a string key to language file: Key is custom field name.
For example you have set custom field named _userGender_ and prefer to see _Gender_ in JoCo. So you add like below to lang file:

		$JoCo_lang['userGender'] = "Gender";
So you would see _Gender_ instead of _userGender_ in JoCo.
There are some predefined labels:
		
		$JoCo_lang['name'] = "Name";
		$JoCo_lang['email'] = "Email";
		$JoCo_lang['website'] = "Website";
		$JoCo_lang['title'] = "Subject";
* ### Events & Custom filters
There are 5 functions are executed on some important events. You can write your own codes there and change behaviour or something special without hacking JoCo.
Functions have been placed in

		/joco/includes/customactions.php


 	1. **onBeforeSetOutputRow($row,$tpl)**: It runs after comments data were collected from _jot_content_ and _jot_fields_ tables. `$row` is a row of _jot_content_ as an associative array. `$row['label']` is another associative array contains _jot_fields_ rows are owned by current comment.

		This function **must** return an array with 2 items, first is associative array to be **merged** to `$row` and second is `$tpl`. Default template for single row has been placed in

			/joco/tpls/indv.tpl.html
			/joco/tpls/indv.tpl.compressed.html
JoCo tries to replace all placeholders is `$tpl` with `$row` data. So you can create `$row['customKey']` and put `[+customKey+]` in `$tpl` to add custom data to single comment box.
	2. **onBeforeCheckRow($row)**: It runs right before parsing comment to decide accepting (and parsing) comment or ignoring that. Return _TRUE_ to accept comment and _FALSE_ to reject it. `$row` is comment DB row.
	3. **onBeforePublish($comment_id)**: It runs right before updating comment and returns void.
	4. **onBeforeEdit($comment_id)**: It runs right before updating comment and returns void.
	5. **onBeforeDelete($comment_id)**: It runs right before deleting comment and returns void.

	**Note**: All custom functions are run in fetching loops.

## Known bugs
JoCo has problems with IE7.

## Licence
GPL

## Author
Amir Hossein Hodjati Pour - [Boplo.ir](http://boplo.ir)
