jQuery( function($) {

    var kuatowc_admin = {

        init: function() {

            if( $( '#woocommerce_kuronekoatobarai_settlement_fee' ).is( ':checked' ) ) {
                $( '#woocommerce_kuronekoatobarai_settlement_fee_table' ).closest( 'tr' ).show();
            } else {
                $( '#woocommerce_kuronekoatobarai_settlement_fee_table' ).closest( 'tr' ).hide();
            }

            $( document ).on( 'change', '#woocommerce_kuronekoatobarai_settlement_fee', function() {
                if( $( this ).is( ':checked' ) ) {
                    $( '#woocommerce_kuronekoatobarai_settlement_fee_table' ).closest( 'tr' ).show();
                } else {
                    $( '#woocommerce_kuronekoatobarai_settlement_fee_table' ).closest( 'tr' ).hide();
                }
            });
            

            kuatowc_admin.createFeeTable();

            $( document ).on( 'click', '.insert_fee', function() {
                kuatowc_admin.onAddNewRow();
            });

            $( document ).on( 'click', '.remove_fee', function() {
                kuatowc_admin.onDeleteRow();
            });

            $( document ).on( 'click', '.fee-input', function() {
                kuatowc_admin.onSelectedRow();
            });
        },

        createFeeTable: function() {
            var fee_table = '<table class="kuronekoatobarai_settlement_fee wc_input_table widefat" id="kuronekoatobarai-fee-table"></table>';
            var fee_table_thead = '<thead><tr><th width="8%">'+kuronekoatobarai_admin_params.label.amount_from+'</th><th width="8%">'+kuronekoatobarai_admin_params.label.amount_to+'</th><th width="8%">'+kuronekoatobarai_admin_params.label.fee+'</th></tr></thead>';
            var fee_table_tfoot = '<tfoot><tr><th colspan="3"><a href="javascript:void(0);" class="button insert_fee">'+kuronekoatobarai_admin_params.label.insert_row+'</a><a href="javascript:void(0);" class="button remove_fee">'+kuronekoatobarai_admin_params.label.remove_row+'</a></th></tr></tfoot>';
            var fee_table_tbody = '<tbody id="kuronekoatobarai-fee"></tbody>';
            $( '#woocommerce_kuronekoatobarai_settlement_fee_table' ).closest( 'td' ).append( fee_table );
            $( '#kuronekoatobarai-fee-table' ).append( fee_table_thead );
            $( '#kuronekoatobarai-fee-table' ).append( fee_table_tfoot );
            $( '#kuronekoatobarai-fee-table' ).append( fee_table_tbody );

            $.each( kuronekoatobarai_admin_params.fees, function( index, fees ) {
                $( '#kuronekoatobarai-fee' ).append( '<tr><td><input type="text" value="'+fees.amount_from+'" placeholder="*" name="amount_from[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td>'
                    +'<td><input type="text" value="'+fees.amount_to+'" placeholder="*" name="amount_to[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td>'
                    +'<td><input type="text" value="'+fees.fee+'" placeholder="*" name="fee[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td></tr>' );
            });
            var rows = $( '#kuronekoatobarai-fee-table tbody' ).children().length;
            if( rows < 1 ) {
                kuatowc_admin.onAddNewRow();
            }
        },

        onAddNewRow: function( event ) {
            var amount_from = 0;
            var amount_to = 0;
            var rows = $( '#kuronekoatobarai-fee-table tbody' ).children().length;
            if( 0 < rows ) {
                $( 'input[name^="amount_to"]' ).each( function( index, elem ) {
                    amount_to = $( this ).val();
                });
                amount_from = ( amount_to ) ? parseInt( amount_to ) + 1 : '';
            }
            $( '#kuronekoatobarai-fee' ).append( '<tr><td><input type="text" value="'+amount_from+'" placeholder="*" name="amount_from[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td>'
                +'<td><input type="text" value="" placeholder="*" name="amount_to[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td>'
                +'<td><input type="text" value="" placeholder="*" name="fee[]" class="ui-autocomplete-input fee-input" style="text-transform:uppercase" autocomplete="off"></td></tr>' );
        },

        onDeleteRow: function() {
            $( '.current-row' ).remove();
        },

        onSelectedRow: function() {
            var focused = $( ':focus' );
            $( '#kuronekoatobarai-fee-table tr' ).removeClass( 'current-row' );
            $( focused ).closest( 'tr' ).addClass( 'current-row' );
        },

    };

    kuatowc_admin.init();
});
