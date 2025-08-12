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

    $(document).on('click','.cpb-upload',function(e){
        e.preventDefault();
        var target=$(this).data('target');
        var frame=wp.media({title:cpbAdmin.mediaTitle,button:{text:cpbAdmin.mediaButton},multiple:false});
        frame.on('select',function(){
            var attachment=frame.state().get('selection').first().toJSON();
            $(target).val(attachment.id);
            $(target+'-preview').html('<img src="'+attachment.url+'" style="max-width:100px;height:auto;" />');
        });
        frame.open();
    });

    if($('#cpb-entity-list').length){
        $.post(cpbAjax.ajaxurl,{action:'cpb_read_main_entity',_ajax_nonce:cpbAjax.nonce},function(response){
            var $list=$('#cpb-entity-list');
            if(response.success && response.data.entities.length){
                response.data.entities.forEach(function(ent){
                    var $item=$('<div class="item"></div>');
                    var $header=$('<div class="item-header"></div>').text(ent.name);
                    var $content=$('<div class="item-content"></div>');
                    cpbAdmin.placeholders.forEach(function(label,index){
                        var key='placeholder_'+(index+1);
                    if(index===26){
                        var urlKey='placeholder_'+(index+1)+'_url';
                            if(ent[urlKey]){
                                $content.append('<p><img src="'+ent[urlKey]+'" style="max-width:100px;height:auto;" /></p>');
                            }
                        }else{
                            $content.append('<p><strong>'+label+':</strong> '+(ent[key]||'')+'</p>');
                        }
                    });
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

    $(document).on('click','#cpb-add-item',function(){
        var count = $('#cpb-items-container .cpb-item-row').length + 1;
        var row = $('<div class="cpb-item-row" style="margin-bottom:8px; display:flex; align-items:center;"></div>');
          row.append('<input type="text" name="placeholder_25[]" class="regular-text cpb-item-field" placeholder="'+cpbAdmin.itemPlaceholder.replace('%d',count)+'" />');
        row.append('<button type="button" class="cpb-delete-item" aria-label="Remove" style="background:none;border:none;cursor:pointer;margin-left:8px;"><span class="dashicons dashicons-no-alt"></span></button>');
        $('#cpb-items-container').append(row);
    });

    $(document).on('click','.cpb-delete-item',function(){
        $(this).closest('.cpb-item-row').remove();
    });
});
