jQuery( function($) {
    var kuatowc_payment= {
        init: function() {
            $( document ).on( 'change', '#sms-notify', function() {
                if( $( this ).is( ':checked' ) ) {
                    $( '#kuatowc-phone-number' ).show();
                } else {
                    $( '#kuatowc-phone-number' ).hide();
                }
            });
            $( document ).on( 'focusout', '#phone-number', function() {
                if( $( '#sms-notify' ).is( ':checked' ) ) {
                  if(null === $( '#phone-number' ).val().match(/^\d{3}-?\d{4}-?\d{4}$/)){
                      alert('電話番号の値が正しくありません。')
                  }
                }
            });
        }

    };

    kuatowc_payment.init();
});