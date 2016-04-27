$.validator.addMethod( "alphanumericHypen", function( value, element ) {
	return this.optional( element ) || /^[\w\-]+$/i.test( value );
}, "Letters, numbers, hypen and underscores only please" );


$.extend(true, $.magnificPopup.defaults, {
	callbacks: {
		open: function() {
			$('.if-overlay-then-blurred').addClass('blurred');
		},
		close: function() {
			$('.if-overlay-then-blurred').removeClass('blurred');
		}
	}
});

var SITECOMMON = {

	confirm: function(message,callback) {
		$('.if-overlay-then-blurred').addClass('blurred');
		alertify.confirm(message, function (e) {
			$('.if-overlay-then-blurred').removeClass('blurred');
			callback(e);
		});
	},

	prompt: function(message,callback,def) {
		$('.if-overlay-then-blurred').addClass('blurred');
		alertify.prompt(message, function (e, str) {
			$('.if-overlay-then-blurred').removeClass('blurred');
			callback(e, str);
		}, def);
	}

};

$(function(){
});
