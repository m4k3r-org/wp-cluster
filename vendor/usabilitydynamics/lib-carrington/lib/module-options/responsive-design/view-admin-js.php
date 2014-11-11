/**
 * Responsive Design
 * Admin Page Elements
 */
(function($, cfct_builder) {
	
	$(function() {

		var responsive_lists = $('div.cfct-responsive-list');

		// Add disable icon DIV when popover is activated
		$(window).bind('popover-show', function() {
			$('.cfct-responsive-icon-container').each(function() {
				if($('.cfct-responsive-disable-icon', this).length == 0) {
					$(this).append('<div class="cfct-responsive-disable-icon" />');
				}
			});
		});

		// Responsive design callback
		$(cfct_builder).bind('responsive-update-response', function(evt, ret) {

			var classes = [];

			var module_parents = $('#' + ret.module_id + ', #cfct-build-options-' + ret.module_id);
			module_parents.find('.cfct-module-status').remove();
			if (!ret.success) {
				ret.html = ret.html + "<p>Could not update classes.</p>";
				cfct_builder.doError(ret);
				return false;
			}

			$.each(ret.css_classes, function (i, val) {
				classes.push(val);
			});
			
			// Set module options form hidden input value for dialog save action
			$('#cfct-popup-responsive-design :hidden').prop('value', classes.join(' '));

			// Used to indicate at least one device is checked
			var hasHiddenDevice = false;

			module_parents.find('div.cfct-responsive-list :checkbox').each(function(i, e) {
				var hasClass = $.inArray($(this).attr('name'), classes) >= 0;
				$(this).prop('checked', hasClass);
				$(this).closest('li').toggleClass('cfct-responsive-disabled', hasClass);
				hasHiddenDevice = hasClass || hasHiddenDevice;
			});
			
			// Toggle the 'hidden-devices' class on the module to show/hide state notification
			$('#' + ret.module_id).toggleClass('hidden-devices', hasHiddenDevice);
		});

		// Responsive CSS class selector handlers
		$('li span', responsive_lists).live('click', function() {
			var _this = $(this);
			_this.parent().find(':checkbox').each(function() {
				$(this).trigger('click');
			});
			return false;
		});

		$(':checkbox', responsive_lists).live('change', function() {
			$(this).parents('div.cfct-responsive-list ul').trigger('cfct-responsive-update');
		});

		$('ul', responsive_lists).live('cfct-responsive-update', function() {
			var _this = $(this);
			var toggle_link = _this.closest('div.cfct-build-options').find('a.popover-trigger');
			var module_id = toggle_link.attr('href').slice(toggle_link.attr('href').indexOf('cfct-module-'));
			var module_div = $('#' + module_id);
			var popup_div = _this.closest('.cfct-popup-content');
			cfct_builder.module_spinner(popup_div);

			var module_data = {
				'module_id':module_id,
				'block_id':module_div.parents('.cfct-block').attr('id'),
				'row_id':module_div.parents('.cfct-row').attr('id'),
				'class_data': {}
			};

			_this.find(':checkbox').each(function(idx, ele) {
				var isChecked = $(ele).is(':checked');
				module_data.class_data[$(ele).attr('name')] = (isChecked ? 1 : 0);
				$(ele).closest('li').find('img').each(function() {
					var imgUrl = $(this).attr(isChecked ? 'cfct_disabled' : 'cfct_enabled');
					$(this).prop('src', imgUrl);
				});
			});

			cfct_builder.fetch('responsive_update',
				module_data,
				'responsive-update-response'
			);
			return false;
		});
	});
})(jQuery, cfct_builder);

<?php /* vim: set filetype=javascript: */ ?>
