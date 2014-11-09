$j = jQuery.noConflict();
		$j(document).ready(function(){
	$j('form#contactForm').submit(function() {
		$j('form#contactForm .error').css({"display":"none"});
		var hasError = false;
		$j('.requiredField').each(function() {
			if(jQuery.trim($j(this).val()) == '') {
				var labelText = $j(this).prev('label').text();
				$j(this).parent().find('.error').css({"display":"block"});
				hasError = true;
			} else if($j(this).hasClass('email')) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(!emailReg.test(jQuery.trim($j(this).val()))) {
					var labelText = $j(this).prev('label').text();
					$j(this).parent().find('.error').css({"display":"block"});
					hasError = true;
				}
			}
		});
		if(!hasError) {
			var formInput = $j(this).serialize();
			$j.post($j(this).attr('action'),formInput, function(data){
				$j('form#contactForm').slideUp("fast", function() {
					$j('.thanks').css({"display":"block"});
				});
			});
		}
		  return false;

	});
});