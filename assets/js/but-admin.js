// Admin js

jQuery( document ).ready(function($) {

    $('#transaction-type').on('change', function() {

    	if( $(this).val() <= 1 ) {
    		$('#transaction-status').prop('disabled', true);
    	} else if( $(this).val() == 4 ) {
    		$('#transaction-price').prop('disabled', true);
    		$('#transaction-status').prop('disabled', true);
    	} else {
    		$('#transaction-price').prop('disabled', false);
    		$('#transaction-status').prop('disabled', false);
    	}
    	
    });

});