(function($, cfct_builder) {

	$(cfct_builder).bind('row_option_disable_response', function(evt, ret) {
		var $row = $('#'+ret.row_id);
		var $opts = $row.find('.cfct-module-status').remove();

		if (!ret.success) {
			ret.html = ret.html + "<p>Could not update classes.</p>";
			cfct_builder.doError(ret);
			return false;
		}

		console.log(ret);
		if (ret.enabled) {
			$row.removeClass('cfct-row-disabled');
			$row.find('.js-row-option-disable')
				.data('enabled', true)
				.text('Disable Row');
		}
		else {
			$row.addClass('cfct-row-disabled');
			$row.find('.js-row-option-disable')
				.data('enabled', false)
				.text('Enable Row');
		}
	});

	$(function() {


		$('.js-row-option-disable').live('click', function(e) {
			var $this = $(this);
			var toggle_link = $this.closest('div.cfct-build-options').find('a.popover-trigger');
			var row_id = toggle_link.attr('href').slice(toggle_link.attr('href').indexOf('cfct-row-'));

			var popup_div = $this.closest('.cfct-popup-content');
			cfct_builder.module_spinner(popup_div);

			console.log($this.data('enabled'));
			var row_data = {
				'row_id':row_id,
				'enabled': !$this.data('enabled')
			};

			cfct_builder.fetch('row_option_disable_update',
				row_data,
				'row_option_disable_response'
			);

			return false;
		});

	});
})(jQuery, cfct_builder);
//<?php /* vim: set filetype=javascript: */ ?>
