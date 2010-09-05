jQuery('object').each(function() {
		var userAgent = navigator.userAgent;
		var expression = '/(webOS|SymbianOS|Nokia|Android)/';
		var match = userAgent.match(expression);
                var type = jQuery(this).attr('type');
		if(match != null && type == "application/x-shockwave-flash") {
			jQuery(this).children('param').remove();
			jQuery(this).children().insertBefore(this);
			jQuery(this).remove();
		}
	}
);