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

$.extend(true, $.fn.datetimepicker.defaults, {
	locale: $('html').attr('lang')
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
	},

	number_format: function(number, decimals, dec_point, thousands_sep) {
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};

			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || '').length < prec) {
				s[1] = s[1] || '';
				s[1] += new Array(prec - s[1].length + 1).join('0');
			}
		return s.join(dec);
	},

	addUriParam: function(uri, key, value) {
		var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";

		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		} else {
			return uri + separator + key + "=" + value;
		}
	}

};

$(function(){
});
