# JoCo 
JoCo is a tool to manage and moderate comments created by Jot snippet in [MODX Evolution](http://modx.com/evolution/).

This package contains **JoCo Module** for moderating comments in MODX manager.


## Features
* Moderate comments created by **you**.
* Moderate last comments from your last login.
* Moderate comments in resources you've created, edited or published.
* Search in comments by string and highlighting, by webgroups, users, guests, document groups, documents, date ranges and etc.
* Moderators need permissions to do critical actions. Managers can set full or limited access for moderators.
* Review list of resources have comments and subscriptions in summary page.
* Moderators only can access to comments in resources they have permission to see them.
* MODX modules can be accessed only in backend so just MODX managers can use it.
* Review and edit all Jot calls of site saved in TVs, chunks and resources contents.


## Installation
1. Upload module file to

		/assets/modules/JoCo/
2. Go to MODX manager and create new module. Name it **JoCo**, **_Jot comments module 0.9.2_** as descriptions and contents of file below as codes:

		/assets/modules/JoCo/JoCo.mudule.txt



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
