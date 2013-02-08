<?php
$site_url = elgg_get_site_url();
$graphics_url = $site_url . 'mod/hypeForum/graphics/';

$forum_icons = hj_forum_get_forum_icons();

foreach ($forum_icons as $icon) {

	$css = <<<__CSS
.elgg-icon-forum-$icon, .elgg-icon-forum-$icon:hover {
	background:transparent url({$graphics_url}forumtopic/$icon.png) 50% 50%;
	height:32px;
	width:32px;
}

__CSS;

	echo $css;
}
?>
<?php if (FALSE) : ?>
	<style type="text/css">
<?php endif; ?>

	.table-header-topics, .table-header-posts {
		width: 10%;
	}
	.table-header-forum, .table-header-last_post {
		width: 37%;
	}
	.table-header-menu {
		width:6%;
	}
	table.hj-forumlist {
		background:white;
	}
	table.hj-forumlist th {
		padding: 10px;
	}
	table.hj-forumlist td {
		padding: 10px;
	}

	table.hj-forumlist td.table-cell-menu {
		padding:0 5px;
	}
	table.hj-forumlist tr:hover {
		background: #f0f0f0;
	}
	table.hj-forumlist .elgg-entity-description {
		margin: 0 0 0 7px;
		color: #666;
	}
	.elgg-module-forum-category {
		border: 1px solid #ccc;
		background: #f4f4f4;
		margin-top:32px;
		position:relative;
		overflow:visible;
	}
	.elgg-module-forum-category:focus {
		-moz-box-shadow:0px 0px 1px #999;
		-webkit-box-shadow:0px 0px 1px #999;
		-khtml-box-shadow:0px 0px 1px #999;
		box-shadow: 0px 0px 1px #999;
	}
	.elgg-module-forum-category > .elgg-head {
		height: 60px;
	}
	.elgg-module-forum-category > .elgg-head h3 {
		padding: 0 0 0 10px;
		color:black;
		line-height:40px;
	}
	.elgg-module-forum-category > .elgg-head .elgg-menu-entity {
		margin: 10px 10px 0 0;
	}
	.elgg-module-forum-category > .elgg-body {
		margin: 0;
		overflow:visible;
	}
	.elgg-module-forum-category .elgg-output {
		margin-top: 0;
	}
	.elgg-module-forum-category .elgg-pagination {
		text-align:right;
	}
	.elgg-module-forum-category .elgg-pagination a,
	.elgg-module-forum-category .elgg-pagination span {
		border:0;
		font-weight:bold;
	}
	.elgg-list.forum-category-list {
		border: 0;
	}
	.elgg-list.forum-category-list > .elgg-item {
		border-bottom: 0;
	}
	table.hj-forumlist .elgg-entity-title {
		color: #666;
		font-weight: bold;
		font-size: 1.2em;
	}
	.elgg-output.elgg-entity-description p {
		line-height: 15px;
	}

	.elgg-menu.elgg-menu-forum-category {
		position: absolute;
		top: 0px;
		right: 10px;
		zoom:1.2;
	}

	.elgg-module-forum-category .elgg-output.elgg-entity-description {
		margin-left: 10px;
	}

	.hj-forumlist .elgg-menu-hjentityhead {
		display:none;
	}

	.hj-forumlist tr:hover .elgg-menu-hjentityhead {
		display:block;
	}
	
<?php if (FALSE) : ?>
	</style>
<?php endif; ?>