jQuery(document).ready( function($) {
    $('#kuroneko_atobarai_sms #back_button').on('click',function () {
        $('#kuroneko_atobarai_sms .kuroneko_sms_fields input').each(function () {
            $(this).prop('required', false);
        });
        $(this).closest('form').submit();
    })
    $('input.kuroneko_sms_field').focusin(function () {
        $(this).val('');
    })
    $('input.kuroneko_sms_field').keyup(function() {
        if ($(this).val().length >= 1) {
            if($(this).attr('id') === 'kuroneko_sms_4'){
                $('#purchase_button').focus();
            } else {
                $(this).parent('label').next().find('input.kuroneko_sms_field').focus();
            }
        }
    });
})