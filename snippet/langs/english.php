<?php
/*********************************
Snippet : JoCo (Jot Comments snippet) : English language file
By : AHHP ~ Boplo.ir
*********************************/

$JoCo_lang = array();

// labels
$JoCo_lang['name'] = "Name";
$JoCo_lang['email'] = "Email";
$JoCo_lang['website'] = "Website";
$JoCo_lang['user'] = "User";
$JoCo_lang['subject'] = "Subject";



// main page
$JoCo_lang['sectionHeader'] = "Jot Comments snippet";
$JoCo_lang['des_para'] = 'JoCo is a module for MODxCMS to manage all comments of <b>[(site_name)]</b>.';

$JoCo_lang['search'] = "Search";
$JoCo_lang['search_para'] = 'Search and customize results';

$JoCo_lang['permission'] = "Permission";
$JoCo_lang['permission_para'] = 'Set access permissions to view comments';

$JoCo_lang['summary'] = "Summary";
$JoCo_lang['summary_para'] = 'Get a overall view of documents which have Jot call';

$JoCo_lang['help'] = "Help";
$JoCo_lang['help_para'] = 'About JoCo and help';

$JoCo_lang['my_comments'] = "My comments";
$JoCo_lang['view_my_comments_para'] = '<a href="[+pageUrl+]WHERE=createdby=[+JoCoUser+]">[lang+my_comments+]</a>';

$JoCo_lang['loading'] = "Loading......";
$JoCo_lang['display'] = "Display";
$JoCo_lang['go_back_home'] = "Go back to Home >>";
$JoCo_lang['ie6_warning'] = "Other popular browsers like Firefox are recommended!";

$JoCo_lang['check_all'] = "Check all";

$JoCo_lang['compressd_comments'] = "JoCo comments";
$JoCo_lang['expand'] = "Expand";
$JoCo_lang['collapse'] = "Collapse";



// pagination
$JoCo_lang['all_results'] = "Total:";
$JoCo_lang['next_page'] = "Next Page";
$JoCo_lang['previous_page'] = "Previous Page";



// each comment row
$JoCo_lang['sent_on'] = "Sent on";
$JoCo_lang['edit'] = "Edit";
$JoCo_lang['delete'] = "Delete";
$JoCo_lang['save'] = "Save";

$JoCo_lang['published_comments'] = "Published comments";
$JoCo_lang['unpublished_comments'] = "Unpublished comments";
$JoCo_lang['all_comments'] = "All comments";
$JoCo_lang['last_comments'] = "Last comments";
$JoCo_lang['view_last_comments'] = '<a href="[+pageUrl+]show=lastComments">[lang+last_comments+]</a>';
$JoCo_lang['cancel'] = "Cancel";

$JoCo_lang['publish_log'] = "Comment [+comment_id+] was published";
$JoCo_lang['unpublish_log'] = "Comment [+comment_id+] was unpublished";
$JoCo_lang['delete_log'] = "Comment [+comment_id+] was deleted";
$JoCo_lang['edit_log'] = "Comment [+comment_id+] was edited";

$JoCo_lang['publish'] = "Publish";
$JoCo_lang['unpublish'] = "Unpublish";
$JoCo_lang['submit'] = "Submit";

$JoCo_lang['published'] = "Published";
$JoCo_lang['unpublished'] = "Unpublished";
$JoCo_lang['edited'] = "Edited!";
$JoCo_lang['deleted'] = "Deleted!";

$JoCo_lang['are_you_sure_publish'] = "Are you sure that you want to publish?";
$JoCo_lang['are_you_sure_unpublish'] = "Are you sure that you want to unpublish?";
$JoCo_lang['are_you_sure_edit'] = "Are you sure that you want to edit?";
$JoCo_lang['are_you_sure_delete'] = "Are you sure that you want to delete?";

$JoCo_lang['result_from_parents'] = "(Parents result)";
$JoCo_lang['result_from_docgroups'] = "(Docgroups result)";
$JoCo_lang['result_from_docs'] = "(Documents result)";
$JoCo_lang['result_from_webgroups'] = "(Webgroups result)";
$JoCo_lang['result_from_users'] = "(Users result)";
$JoCo_lang['result_from_guestUsers'] = "(Guests result)";



// search
$JoCo_lang['search_in_comments'] = "Search in comments";
$JoCo_lang['search_in_docs'] = "Search in documents";
$JoCo_lang['search_by_parents'] = "Parents";
$JoCo_lang['search_by_parents_des'] = "Comma delimated Parent IDs to search in themselves and their childern.";
$JoCo_lang['search_by_docgroups'] = "Docgroups";
$JoCo_lang['search_by_docgroups_des'] = "Comma delimated Docgroup names to search in their members.";
$JoCo_lang['search_by_docs'] = "Documents";
$JoCo_lang['search_by_docs_des'] = "Comma delimated Document IDs.";
$JoCo_lang['search_by_notdocs'] = "NOT in documents";
$JoCo_lang['search_by_notdocs_des'] = "Comma delimated Document IDs to remove from results.";
$JoCo_lang['search_by_users'] = "Search by users";
$JoCo_lang['search_users'] = "Users";
$JoCo_lang['search_users_des'] = "Comma delimated User IDs (Webusers have negative ID).";
$JoCo_lang['search_by_quests'] = "Guest users";
$JoCo_lang['search_guests'] = "Guests, the users who has not login or are not member of [(site_name)].";
$JoCo_lang['search_by_webgroups'] = "Webgroups";
$JoCo_lang['search_by_webgroups_des'] = "Comma delimated Webgroup names to search in their members.";
$JoCo_lang['search_by_notusers'] = "NOT by users";
$JoCo_lang['search_by_notusers_des'] = "Comma delimated User IDs to remove from results.";
$JoCo_lang['search_string'] = "Search string";
$JoCo_lang['search_not_string'] = "Search NOT string";
$JoCo_lang['search_regex'] = "Search by Regex";
$JoCo_lang['search_not_regex'] = "Search by NOT Regex";
$JoCo_lang['in_content'] = "in content";
$JoCo_lang['in_title'] = "in title";
$JoCo_lang['in_tagid'] = "in tagid";
$JoCo_lang['in_flags'] = "in flags";
$JoCo_lang['limit_number'] = "Maximum number of results";
$JoCo_lang['fields_are_empty'] = "Fields are empty!";
$JoCo_lang['getNames_des_para'] = "Striked names have not any comment and italic striked IDs do not exists.";
$JoCo_lang['recentComments'] = "Recent comments";
$JoCo_lang['only_recent_from_my_last_active'] = "Only recent results from your last login";
$JoCo_lang['see_users_you_choosen'] = "See users you have choosen....";
$JoCo_lang['see_docs_you_choosen'] = "See documents you have choosen....";

$JoCo_lang['id_option'] = "ID of comments";
$JoCo_lang['title_option'] = "Subject of comments";
$JoCo_lang['tagid_option'] = "Tagid of Jot calls";
$JoCo_lang['uparent_option'] = "Documents";
$JoCo_lang['secip_option'] = "IPs";
$JoCo_lang['createdby_option'] = "Created by";
$JoCo_lang['createdon_option'] = "Created on";
$JoCo_lang['editedby_option'] = "Edited by";
$JoCo_lang['editedon_option'] = "Edited on";
$JoCo_lang['publishedby_option'] = "Published by";
$JoCo_lang['publishedon_option'] = "Published on";

$JoCo_lang['date_limit'] = "Date limitation";
$JoCo_lang['from'] = "From";
$JoCo_lang['to'] = "To";
$JoCo_lang['sample'] = "YYYY-mm-dd";
$JoCo_lang['limit'] = "Limit";
$JoCo_lang['sort'] = "Sort";
$JoCo_lang['ASC'] = "Ascending";
$JoCo_lang['DESC'] = "Descending";
$JoCo_lang['sortBy'] = "Order by";



// permissions
$JoCo_lang['actions'] = "Actions";
$JoCo_lang['permissions'] = "Permissions";
$JoCo_lang['all_users'] = "All users";
$JoCo_lang['exist_users'] = "Existed users";

$JoCo_lang['publishPermi'] = "Publish comments";
$JoCo_lang['unpublishPermi'] = "Unpublish comments";
$JoCo_lang['editPermi'] = "Edit comments";
$JoCo_lang['removePermi'] = "Delete comments";
$JoCo_lang['loggingPermi'] = "Log actions";
$JoCo_lang['viewAllPermi'] = "View all comments";
$JoCo_lang['viewPublishedPermi'] = "View published comments";
$JoCo_lang['viewUnpublishedPermi'] = "View unpublished comments";
$JoCo_lang['ipPermi'] = "View IP";
$JoCo_lang['webUsersPermi'] = "Manage Webuser's permissions";
$JoCo_lang['createdDocsPermi'] = "Comments of docs user has created";
$JoCo_lang['publishedDocsPermi'] = "Comments of docs user has published";
$JoCo_lang['editedDocsPermi'] = "Comments of docs he user edited";
$JoCo_lang['searchPermi'] = "View search";
$JoCo_lang['permissionPermi'] = "View permissions";
$JoCo_lang['summaryPermi'] = "View summary";
$JoCo_lang['changeThemePermi'] = "Change theme";
$JoCo_lang['defaultThemePermi'] = "Default theme";
$JoCo_lang['defaultViewPermi'] = "Default view";
$JoCo_lang['summaryResPerPagePermi'] = "Number of results in summary";
$JoCo_lang['resPerPagePermi'] = "Number of results in main frame";
$JoCo_lang['impossible_change_admins_permissions'] = "There is no way to change admins permissions!";
$JoCo_lang['user_exists'] = "This user currently exists.<br />Wait please...";
$JoCo_lang['impossible_romove_yourself'] = "It is impossible to romove your JoCo-account!<br />Wait please...";
$JoCo_lang['user_creation_failed'] = "User creation failed!<br />Wait please...";
$JoCo_lang['move_in_confirm'] = "Are you sure that you want to add this user?";
$JoCo_lang['move_out_confirm'] = "Are you sure that you want to remove this user?";
$JoCo_lang['view_recent'] = "View recent comments";
$JoCo_lang['view_user_comments'] = "View user's comments";
$JoCo_lang['setPermissions_success'] = "Permissions changed successfully!";
$JoCo_lang['save_changes'] = "Save changes";
$JoCo_lang['allow'] = "Allow";
$JoCo_lang['disallow'] = "Disallow";
$JoCo_lang['add_user'] = "Hire user!";
$JoCo_lang['remove_user'] = "Fire user!";
$JoCo_lang['view_nothing'] = "View nothing";
$JoCo_lang['no_permission'] = "No permission";
$JoCo_lang['permission_part_para'] = "Select user from all-user column and click enable button to add him/her to JoCo users. select existed user to remove him/her and see permissions.<br />Do NOT select more then one user at same time!";
$JoCo_lang['mgr_moderators'] = "Managers";
$JoCo_lang['web_moderators'] = "Web users";
$JoCo_lang['choose_a_user'] = "Please choose a user!";



// summary
$JoCo_lang['subscriptions'] = "Subscriptions";
$JoCo_lang['guest'] = "Guest";
$JoCo_lang['title'] = "Title";
$JoCo_lang['id'] = "ID";
$JoCo_lang['comments'] = "Comments";
$JoCo_lang['last_comment_on'] = "Last comment on";
$JoCo_lang['last_comment_by'] = "Last comment by";
$JoCo_lang['get_subscriptions'] = "Get subscriptions!";
$JoCo_lang['get_permissions'] = "Get permissions!";
$JoCo_lang['see_this_comments'] = "See this comments.";
$JoCo_lang['there_is_no_comment_here'] = "There is no comment here.";
$JoCo_lang['there_is_no_subscription_here'] = "There is no subscription here.";

$JoCo_lang['comments_img'] = '<img src="[(base_url)]assets/modules/JoCo/images/home.png" width="20" alt="[lang+there_is_no_comment_here+]" title="[lang+there_is_no_comment_here+]" />';
$JoCo_lang['subscriptions_img'] = '<img src="[(base_url)]assets/modules/JoCo/images/search_magnifier.png" width="16" alt="[lang+get_subscriptions+]" />';

$JoCo_lang['summary_part_para'] = "There is some summary information about your comments in <b>[(site_name)]</b> documnets. You can go to document on click on documnet title. you can see and manage comments of each documnet on click on [lang+comments_img+] too.<br />Clicking on [lang+subscriptions_img+], give you subscriptions of each document.";







// theme
$JoCo_lang['select_theme'] = "Theme";
$JoCo_lang['normalTheme'] = "Normal";
$JoCo_lang['compressedTheme'] = "Compressed";


// errors
$JoCo_lang['permission_denied'] = "You have not permission to set or change permissions!";
$JoCo_lang['viewAll_denied'] = "You have not permission to see all comments!";
$JoCo_lang['viewPublished_denied'] = "You have not permission to see published comments!";
$JoCo_lang['viewPublished_denied'] = "You have not permission to see unpublished comments!";
$JoCo_lang['search_denied'] = "You have not permission to search comments!";
$JoCo_lang['summary_denied'] = "You have not permission to search comments!";
$JoCo_lang['nothing_to_dispaly'] = "There is nothing to dispaly!";



// help
$JoCo_lang['help_joco'] = "JoCo Help";

?>