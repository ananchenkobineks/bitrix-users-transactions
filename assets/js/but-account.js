// Front js

jQuery(document).ready(function ($) {

    $('.tooltip-wrap').click(function(){
        var tooltip = $(this).find('.tooltiptext');
        $('.tooltiptext').each(function(){
            if($(this).is(":visible") && $(this).get(0) != tooltip.get(0)){
                $(this).hide();
            }
        });
        $(this).find('.tooltiptext').toggle();
    });

    $('#edit-order').on('click', function(e){
    	e.preventDefault();

    	$('.order-action-wrap').hide();

        $('.products-table').addClass('edit-active');

    	var save_edit_html = '<div class="save-edit">\
    	<a class="btn btn-default accept">Сохранить</a>\
    	<a class="btn btn-default dismiss">Отменить</a>\
    	</div>';

    	$( ".products-wrap" ).prepend( save_edit_html );

    	$('.save-edit .accept').on('click', function(){
    		$('#edit-products-form').submit();
    	});

    	$('.save-edit .dismiss').on('click', function(){

            $('.products-table').removeClass('edit-active');

    		$('.order-action-wrap').show();
    		$('.save-edit').remove();
    	});
    });

    $('#edit-address').on('click', function() {

        $('#edit-addresses-list input').prop( "disabled", false );

        $(this).hide();
        $("#save-address").show();
    });

});