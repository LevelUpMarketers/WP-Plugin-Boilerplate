jQuery(document).ready(function($){
    function handleForm(selector, action){
        $(selector).on('submit', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $('#cpb-spinner').fadeIn();
            $.post(cpbAjax.ajaxurl, data + '&action=' + action + '&_ajax_nonce=' + cpbAjax.nonce, function(response){
                $('#cpb-spinner').fadeOut();
                var $fb = $('#cpb-feedback');
                $fb.hide().text(response.data.message).fadeIn();
            });
        });
    }
    handleForm('#cpb-create-form','cpb_save_main_entity');
    handleForm('#cpb-settings-form','cpb_save_main_entity');

    $('.cpb-accordion').on('click','.item-header',function(){
        $(this).next('.item-content').slideToggle();
        $(this).parent().toggleClass('open');
    });
});
