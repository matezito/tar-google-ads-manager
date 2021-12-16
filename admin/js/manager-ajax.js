(function($) {
    $(document).ready(function() {
        $('.remove-size').on('click', function() {
            var id = $(this).data('id');
            $(this).parent().remove(); 
            $.ajax({
                type: 'post',
                url: ajax_delete_size.url,
                data:{
                    action: ajax_delete_size.action,
                    _ajax_nonce: ajax_delete_size._ajax_nonce,
                    delete_size: ajax_delete_size.delete_size,
                    id_size: id
                },
                success: function (result) {
                  if(result){
                      $('.notice').not('#_notice').remove();
                      $('#ajax-response').html('<div class="notice notice-success is-dismissible" id="_notice"><p>' + ajax_delete_size.success + '</p></div>');                   
                  }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    });
})(jQuery);