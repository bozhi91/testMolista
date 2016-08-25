$(function() {

	ready_callbacks.push(function(){

		// Select placeholder effect
		$('body').on('change', 'select.has-placeholder', function() {
			if ( $(this).val() ) {
				$(this).removeClass('is-placeholder');
			} else {
				$(this).addClass('is-placeholder');
			}
		});		
		$('body').find('select.has-placeholder').trigger('change');

	});

});
