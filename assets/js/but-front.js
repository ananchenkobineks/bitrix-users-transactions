jQuery(document).ready(function ($) {

	$('.wpcf7-form input[name="fname"]').prop('readonly', true);
	$('.wpcf7-form input[name="lname"]').prop('readonly', true);
	$('.wpcf7-form input[name="email"]').prop('readonly', true);
	$('.wpcf7-form input[name="phone"]').prop('readonly', true);


	if( user.user_address.length > 0 ) {

		var select_html = '<select name="address">';
		$.each( user.user_address, function(index, item) {
			select_html += '<option value="'+item+'">'+item+'</option>';
		});
		select_html += '</select>';

		$('.wpcf7-form span.address').prepend( select_html );
		$('.wpcf7-form span.address input').remove();
	}

});