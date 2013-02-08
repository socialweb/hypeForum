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

		$('form#form-edit-object-hjforumpost')
		.submit(function(eventSubmit) {

			$form = $(this);

			var data = new Object();
			data['X-Requested-With'] = 'XMLHttpRequest';
			data.view = 'xhr'; // set viewtype
			data.endpoint = 'layout'; // 'pageshell', 'layout', 'layout-elements'

			var params = ({
				dataType : 'json',
				data : data,
				beforeSend : function() {
					elgg.system_message(elgg.echo('hj:framework:ajax:saving'));
				},
				complete : function() {
				},
				success : function(response, status, xhr) {

					var hookParams = new Object();
					response.event = 'submitForm';
					hookParams.response = response;
					hookParams.data = $form.serialize();

					elgg.trigger_hook('ajax:success', 'framework', hookParams, true);

					if (response.status < 0) {
						$element.trigger('click');
						return false;
					}

					hookParams.href = framework.ajax.updateUrlQuery(window.location.href, { '__goto' : response.output.guid });
					elgg.trigger_hook('refresh:lists', 'framework', hookParams);

					$form.resetForm();

				}
			});

			if ($form.find('input[type=file]')) {
				params.iframe = true;
			} else {
				params.iframe = false;
			}

			$form.ajaxSubmit(params);

			return false;
		})
		
	}

	framework.forum.orderCategories = function(event, ui) {

		var data = new Object();
		data.priorities = new Array();
		
		ui.item.closest('.forum-category-list').find('.hj-draggable-element').each(function(key, item) {
			data.priorities[key] = {
				priority : key*100,
				guid : $(item).data('uid')
			}
		})

		elgg.action('forum/order/categories', {
			data: data
		});

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
