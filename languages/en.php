<?php

$english = array(

    'forum' => 'Forum',
	'forums' => 'Forums',
	'hj:forum:siteforums' => 'Site-wide Forums',
	
	'item:object:hjforum' => 'forum',
	'item:object:hjforumtopic' => 'forum topic',
	'item:object:hjforumpost' => 'forum post',
	'item:object:hjforumcategory' => 'forum category',
	
	'items:object:hjforum' => 'forums',
	'items:object:hjforumtopic' => 'forum topics',
	'items:object:hjforumpost' => 'forum posts',
	'items:object:hjforumcategory' => 'forum categories',
	
	// Form Elements
	'edit:object:hjforum:cover' => 'Cover Image',
	'edit:object:hjforum:title' => 'Forum Title',
	'edit:object:hjforum:description' => 'Subtitle',
	'edit:object:hjforum:access_id' => 'Visibility',
	'edit:object:hjforum:category' => 'Category',
	'edit:object:hjforum:enable_subcategories' => 'Enable subcategories',

	'edit:object:hjforumtopic:cover' => 'Cover',
	'edit:object:hjforumtopic:icon' => 'Icon',
	'edit:object:hjforumtopic:title' => 'Title',
	'edit:object:hjforumtopic:description' => 'Description',
	'edit:object:hjforumtopic:category' => 'Category',
	'edit:object:hjforumtopic:access_id' => 'Visibility',
	

	'hj:forum:tablecol:forum' => 'Forum',
	'hj:forum:tablecol:topics' => 'Topics',
	'hj:forum:tablecol:posts' => 'Posts',
	'hj:forum:tablecol:last_post' => 'Latest Post',

	'river:in:forum' => ' in %s',
	'river:create:object:hjforum' => '%s created a new forum | %s',
	'river:create:object:hjforumtopic' => '%s started a new forum topic | %s',
	'river:create:object:hjforumpost' => '%s posted a reply to topic %s',
	
	'edit:object:hjforumcategory:title' => 'Category Name',
	'edit:object:hjforumcategory:description' => 'Brief Description',

	'edit:object:hjforumpost:description' => 'Reply',
	
	'hj:forum:nocategories' => 'You have not yet setup any categories. Please do so, using the form below',

	'hj:forum:notsetup' => 'This forum has not yet been configured',

	'hj:forum:create:forum' => 'New forum',
	'hj:forum:create:subforum' => 'New sub-forum',
	'hj:forum:create:topic' => 'New forum topic',
	'hj:forum:create:post' => 'Reply',
	'hj:forum:create:post:quote' => 'Quote & Reply',
	'hj:forum:create:category' => 'New forum category',

	'hj:forum:dashboard:site' => 'Site-wide Forums',
	'hj:forum:dashboard:groups' => 'Group Forums',
	'hj:forum:dashboard:bookmarks' => 'Bookmarked Forum Topics',
	'hj:forum:dashboard:subscriptions' => 'Forum Subscriptions',

	'hj:forum:dashboard:tabs:site' => 'Site-wide Forums',
	'hj:forum:dashboard:tabs:groups' => 'Group Forums',
	'hj:forum:dashboard:tabs:bookmarks' => 'Bookmarks',
	'hj:forum:dashboard:tabs:subscriptions' => 'Subscription',

	'edit:plugin:hypeforum:params[categories_top]' => 'Enable categories for top-level Site and Group forums',
	'edit:plugin:hypeforum:params[categories]' => 'Enable categories for nested forums and topics',
	'edit:plugin:hypeforum:params[subforums]' => 'Enable sub-forums',
	'edit:plugin:hypeforum:params[forum_cover]' => 'Enable cover images for forums',
	'edit:plugin:hypeforum:params[forum_topic_cover]' => 'Enable cover images for topics',
	'edit:plugin:hypeforum:params[forum_topic_icon]' => 'Enable topic icons',
	'edit:plugin:hypeforum:params[forum_topic_icon_types]' => 'List of topic icon types',
	'edit:plugin:hypeforum:topic_icon_hint' => 'Separated by comma. Icons need to be uploaded into mod/hypeForum/graphics/forumtopic/',

	'edit:plugin:hypeforum:params[forum_forum_river]' => 'Add new forums to river',
	'edit:plugin:hypeforum:params[forum_topic_river]' => 'Add new topics to river',
	'edit:plugin:hypeforum:params[forum_post_river]' => 'Add new posts to river',

	'edit:plugin:hypeforum:params[forum_subscriptions]' => 'Enable notification subscriptions',
	'edit:plugin:hypeforum:params[forum_bookmarks]' => 'Enable bookmarks',
	'edit:plugin:hypeforum:params[forum_group_forums]' => 'Enable Group forums',
	
	'hj:forum:filter' => 'Filter Forums',

	'hj:forum:quote:author' => '%s wrote:',

	'hj:forum:groups:notamember' => 'You have not joined any group yet',

	'hj:forum:groupoption:enableforum' => 'Enable group forums',
	'hj:forum:group' => 'Group forums',

	'hj:forum:dashboard:group' => '%s\'s Forums',
	
);


add_translation("en", $english);
?>