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
    handleForm('#cpb-general-settings-form','cpb_save_main_entity');
    handleForm('#cpb-style-settings-form','cpb_save_main_entity');

    if($('#cpb-entity-list').length){
        $.post(cpbAjax.ajaxurl,{action:'cpb_read_main_entity',_ajax_nonce:cpbAjax.nonce},function(response){
            var $list=$('#cpb-entity-list');
            if(response.success && response.data.entities.length){
                response.data.entities.forEach(function(ent){
                    var $item=$('<div class="item"></div>');
                    var $header=$('<div class="item-header"></div>').text(ent.name);
                    var $content=$('<div class="item-content"></div>');
                    $content.append('<p><strong>'+cpbAdmin.placeholder1+':</strong> '+ent.placeholder_1+'</p>');
                    $content.append('<p><strong>'+cpbAdmin.thing1+':</strong> '+ent.thing_1+'</p>');
                    $content.append('<p><strong>'+cpbAdmin.thing2+':</strong> '+ent.thing_2+'</p>');
                    $content.append('<button type="button" class="button cpb-delete" data-id="'+ent.id+'">'+cpbAdmin.delete+'</button>');
                    $item.append($header).append($content);
                    $list.append($item);
                });
            }else{
                $list.append('<p>'+cpbAdmin.none+'</p>');
            }
        });

        $('#cpb-entity-list').on('click','.cpb-delete',function(){
            var id=$(this).data('id');
            var $row=$(this).closest('.item');
            $('#cpb-spinner').fadeIn();
            $.post(cpbAjax.ajaxurl,{action:'cpb_delete_main_entity',id:id,_ajax_nonce:cpbAjax.nonce},function(resp){
                $('#cpb-spinner').fadeOut();
                if(resp.success){
                    $row.remove();
                }
            });
        });
    }

    $('.cpb-accordion').on('click','.item-header',function(){
        $(this).next('.item-content').slideToggle();
        $(this).parent().toggleClass('open');
    });
});
