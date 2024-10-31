jQuery(document).ready(function($) {
  
  $( 'body' ).on( 'click', '.osn-send-test-notification', function(e) {
    e.preventDefault();

    var _self = $(this);

    if ( _self.hasClass('processing') ) {
      return false;
    }
    
    var Table = _self.parents('table');
    var phone = Table.find('#osn_test_phone').val()
    var message  = Table.find('#osn_test_message').val();
    var service_name = _self.parents('form').find('input[name="osn_service"]:checked').val();

    if ( phone !== '' && message !== '' ) {

      _self.addClass('processing');
      
      data = {
        action        : 'osn_test_notification',
        phone_number  : phone,
        message       : message,
        service_name  : service_name,
      };

      $.ajax({
        type : "POST",
        data : data,
        dataType : "json",
        url : osn_admin_params.ajax_url,
        success: function( response ) {

          _self.removeClass('processing');

          if ( response.status == 'success' ) {
            $.toast({
              text     : osn_admin_params.success_msg,
              position: {
                right: 80,
                bottom: 70
              },
              heading: osn_admin_params.success_msg_heading,
              showHideTransition: 'fade',
              icon: 'success',
            });
          }
          else {
            
            var html = '';

            for ( i = 0; i < response.errors.length; i++ ) {
              html += response.errors[i] +  "\n";
            }

            $.toast({
              text     : html,
              position: {
                right: 80,
                bottom: 70
              },
              heading: osn_admin_params.error_msg_heading,
              showHideTransition: 'fade',
              icon: 'error',
              hideAfter: 8000,
            });
          }
        }
      });

    }

  });

});