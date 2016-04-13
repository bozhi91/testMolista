$(function() {

	for (var t=0; t<ready_callbacks.length; t++) {
		ready_callbacks[t]();
	}

});
