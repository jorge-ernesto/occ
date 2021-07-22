$( document ).ready(function() {
	if($('#username').length) {
		$('#username').focus();
	}

	$(document).on('click', '.btn-login', function() {
		login();
	});
	$('.keypress').keypress(function( event ) {
		if ( event.which == 13 ) {
			login();
		}
	});

	$(document).on('click', '.btn-search', function() {
		console.log($(this).attr('data-ismarket'));
		$(this).prop('disabled', true);
		if ($('#typeStation').val() == 0) {
			searchValesClientesCredito($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 1) {
			searchComprobantesCobranza($(this).attr('data-ismarket'));
		}
	});
});