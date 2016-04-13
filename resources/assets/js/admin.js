$(function() {

	if ( $.fn.select2 ) {
		$.fn.select2.defaults.set('theme', 'bootstrap');
	}

	for (var t=0; t<ready_callbacks.length; t++) {
		ready_callbacks[t]();
	}

});