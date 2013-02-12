<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('framework');
	elgg.provide('framework.forum');

	framework.forum.init = function() {

		$(".forum-category-list").sortable({
			items:                'div.hj-draggable-element',
			connectWith:          '.forum-category-list',
			handle:               '.hj-draggable-element-handle',
			forcePlaceholderSize: true,
			placeholder:          'hj-draggable-element-placeholder',
			opacity:              0.8,
			revert:               500,
			stop:                 framework.forum.orderCategories
		});
		
	}

	framework.forum.orderCategories = function(event, ui) {

		var data = ui.item
		.closest('.forum-category-list')
		.sortable('serialize');

		elgg.action('action/forum/order/categories?' + data);

		// @hack fixes jquery-ui/opera bug where draggable elements jump
		ui.item.css('top', 0);
		ui.item.css('left', 0);
	};

	framework.forum.appendNewCategory = function(name, type, params, value) {

		$('.forum-category-list')
		.prepend($(params.response.output.view));

		return value;
		
	}

	elgg.register_hook_handler('init', 'system', framework.forum.init);

	elgg.register_hook_handler('newcategory', 'framework:forum', framework.forum.appendNewCategory);


<?php if (FALSE) : ?></script><?php
endif;
?>
