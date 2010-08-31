jQuery('object').each(function(index) {
		var userAgent = navigator.userAgent;
		var expression = '/(webOS|SymbianOS|Nokia|Android)/';
		var match = userAgent.match(expression);
		if(match != null) {
			jQuery(this).children('param').remove();
			jQuery(this).children().insertBefore(this);
			jQuery(this).remove();
		}
	}
)