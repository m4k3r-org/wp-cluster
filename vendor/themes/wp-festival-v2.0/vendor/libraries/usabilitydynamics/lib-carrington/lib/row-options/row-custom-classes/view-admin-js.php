 (function($, cfct_builder) {

	$(cfct_builder).bind('row_option_custom_classes_response', function(evt, ret) {
		var $row = $('#'+ret.row_id);
		var $opts = $row.find('.cfct-module-status').remove();

		if (!ret.success) {
			ret.html = ret.html + "<p>Could not update classes.</p>";
			cfct_builder.doError(ret);
			return false;
		}

		if (ret.css_classes) {
			$row.find('.cfct-row-option-custom-classes .cfct-option .row-custom-classes').text(ret.css_classes);
			$row.find('.cfct-row-option-custom-classes .cfct-option .add-class-btn').text('Edit');
		}
		else {
			$row.find('.cfct-row-option-custom-classes .cfct-option .row-custom-classes').html('<span class="option-note"><em>none specified</em></span>');
			$row.find('.cfct-row-option-custom-classes .cfct-option .add-class-btn').text('Add');
		}
		$row.find('.cfct-row-option-custom-classes .cfct-option').show();
		$row.find('.cfct-row-option-custom-classes .form').hide();
	});

	$(function() {

		$(document).on('click', '.cfct-row-option-custom-classes a.trigger, .cfct-row-option-custom-classes a.cancel', function(e) {
			var $this = $(this);
			$this.closest('.cfct-row-option-custom-classes').find('.cfct-option').toggle();
			$this.closest('.cfct-row-option-custom-classes').find('.form').toggle();
			e.preventDefault();
		});

		$(document).on('click', '.cfct-row-option-custom-classes .save', function(e) {
			var $this = $(this);
			var toggle_link = $this.closest('div.cfct-build-options').find('a.popover-trigger');
			var row_id = toggle_link.attr('href').slice(toggle_link.attr('href').indexOf('cfct-row-'));

			var popup_div = $this.closest('.cfct-popup-content');
			cfct_builder.module_spinner(popup_div);

			var row_data = {
				'row_id':row_id,
				'data': $this.closest('.form').find('.cfct_custom_class_input').val()
			};

			cfct_builder.fetch('row_option_custom_classes_update',
				row_data,
				'row_option_custom_classes_response'
			);

			return false;
		});

	});
})(jQuery, cfct_builder);
//<?php /* vim: set filetype=javascript: */ ?>
