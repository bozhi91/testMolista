var LOADING = {
	show: function() {
		if ( $('#Contromia-loading-div').length < 1 ) {
			$('body').append('<div id="Contromia-loading-div"><div class="background"></div><div class="loading"></div></div>');
		}
		$('#Contromia-loading-div').show();
		$('.if-overlay-then-blurred').addClass('blurred');
	},
	hide: function() {
		$('#Contromia-loading-div').hide();
		$('.if-overlay-then-blurred').removeClass('blurred');
	}
};