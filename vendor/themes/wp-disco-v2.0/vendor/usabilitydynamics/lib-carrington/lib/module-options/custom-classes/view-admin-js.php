(function($, cfct_builder) {

	$(cfct_builder).bind('option_custom_classes_response', function(evt, ret) {
		var module_parents = $('#' + ret.module_id + ', #cfct-options-layout-inner-' + ret.module_id);
		module_parents.find('.cfct-module-status').remove();

		if (!ret.success) {
			ret.html = ret.html + "<p>Could not update classes.</p>";
			cfct_builder.doError(ret);
			return false;
		}

		// update all instances
		module_parents.find('.cfct_custom_class_input').val(ret.data);
		if (ret.data) {
			module_parents.find('.cfct-module-option-custom-css .custom-css').text(ret.data);
			module_parents.find('.cfct-module-option-custom-css .cfct-option .add-class-btn').text('Edit');
		}
		else {
			module_parents.find('.cfct-module-option-custom-css .custom-css').html('<span class="option-note"><em>none specified</em></span>');
			module_parents.find('.cfct-module-option-custom-css .cfct-option .add-class-btn').text('Add');
		}

		module_parents.find('.cfct-module-option-custom-css .cfct-option').show();
		module_parents.find('.cfct-module-option-custom-css .form').hide();
	});

	$(document).on('click', '.cfct-module-option-custom-css a.trigger, .cfct-module-option-custom-css a.cancel', function(e) {
		var $this = $(this);
		$this.closest('.cfct-module-option-custom-css').find('.cfct-option').toggle();
		$this.closest('.cfct-module-option-custom-css').find('.form').toggle();
		return false;
	});


	$(document).on('click', '.cfct-module-option-custom-css .save', function(e) {
		var $this = $(this);
		var toggle_link = $this.closest('div.cfct-build-options').find('a.popover-trigger');
		var module_id = toggle_link.attr('href').slice(toggle_link.attr('href').indexOf('cfct-module-'));
		var module_div = $('#' + module_id);

		var popup_div = $this.closest('.cfct-popup-content');
		cfct_builder.module_spinner(popup_div);

		var module_data = {
			'module_id':module_id,
			'data': $this.closest('.form').find('.cfct_custom_class_input').val()
		};

		cfct_builder.fetch('option_custom_classes_update',
			module_data,
			'option_custom_classes_response'
		);

		return false;
	});

})(jQuery, cfct_builder);
//<?php /* vim: set filetype=javascript: */ ?>
