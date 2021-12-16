(function( $ ) {
	'use strict';

	$(document).ready(function() {
		$('#add-row').append('<span class="add_row"><span class="dashicons dashicons-plus"></span></span>');
		$('.add_row').on('click', function() {
			$('#table-sizes').append('<tr class="otro"><th scope="row"></th><td><label class="sizes"><input type="number" name="ad_width[]" class="small-text" style="width:100% !important" value=""></label><label for="" class="sizes"> <input type="number" name="ad_height[]" class="small-text" style="width:100% !important" value="" placeholder=""></label><div class="sizes-row dos"><span class="remove_row"><span class="dashicons dashicons-trash"></span></span></div></td></tr>');
		});
	});

	$(document).on('click', '.remove_row', function() {
		$(this).parent().parent().parent().remove();
	});

	$(document).ready(function() {
		$('.list-item-header').on('click', function() {
			var id = $(this).data('id');
			if($('.sizes-list').not('#size-' + id).is(':visible')){
				$('.sizes-list').not('size-' + id).slideUp();
			}	
			if($('#size-'+id).is(':visible')){
				$('#size-'+id).slideUp();
			} else {
				$('#size-'+id).slideDown();
			}
		});
	});

	$(document).ready(function(){
		$('.add_new_size').on('click', function(){
			$(this).before('<span class="show-size sizes-fields" style="margin-right:1%"><input type="number" name="new_edit_width[]" id="" value="" class="size-input small-text"> x <input type="number" name="new_edit_height[]" id="" value="" class="size-input small-text"> <span class="remove-new-size"><span class="dashicons dashicons-trash"></span></span></span>').last();
		});
	});

	
	$(document).on('click', '.remove-new-size', function() {
		$(this).parent().remove();
	});


})( jQuery );