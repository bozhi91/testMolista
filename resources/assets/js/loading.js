var LOADING = {
	show: function() {
		if ( $('#molista-loading-div').length < 1 ) {
			$('body').append('<div id="molista-loading-div"><div class="background"></div><div class="loading"></div></div>');
		}
		$('#molista-loading-div').show();
	},
	hide: function() {
		$('#molista-loading-div').hide();
	}
};