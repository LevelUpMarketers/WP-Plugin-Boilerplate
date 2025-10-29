jQuery(document).ready(function($){
    function handleForm(selector, action){
        var spinnerHideTimer;
        $(selector).on('submit', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            var $spinner = $('#cpb-spinner');
            var $feedback = $('#cpb-feedback');
            if ($feedback.length) {
                $feedback.removeClass('is-visible').text('');
            }
            if (spinnerHideTimer) {
                clearTimeout(spinnerHideTimer);
            }
            $spinner.addClass('is-active');
            $.post(cpbAjax.ajaxurl, data + '&action=' + action + '&_ajax_nonce=' + cpbAjax.nonce)
                .done(function(response){
                    if ($feedback.length && response && response.data) {
                        var message = response.data.message || response.data.error;
                        if (message) {
                            $feedback.text(message).addClass('is-visible');
                        }
                    }
                })
                .fail(function(){
                    if ($feedback.length && cpbAdmin.error) {
                        $feedback.text(cpbAdmin.error).addClass('is-visible');
                    }
                })
                .always(function(){
                    spinnerHideTimer = setTimeout(function(){
                        $spinner.removeClass('is-active');
                    }, 150);
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
            var $spinner = $('#cpb-spinner');
            $spinner.addClass('is-active');
            $.post(cpbAjax.ajaxurl,{action:'cpb_delete_main_entity',id:id,_ajax_nonce:cpbAjax.nonce})
                .done(function(resp){
                    if(resp.success){
                        $row.remove();
                    }
                })
                .always(function(){
                    $spinner.removeClass('is-active');
                });
        });
    }

    $('.cpb-accordion').on('click','.item-header',function(){
        $(this).next('.item-content').slideToggle();
        $(this).parent().toggleClass('open');
    });

    function initCommunicationsAccordions(){
        $('.cpb-accordion-group').each(function(){
            var $group = $(this);

            $group.find('.cpb-accordion__item').each(function(){
                var $item = $(this);
                var $panel = $item.find('.cpb-accordion__panel');

                if ($item.hasClass('is-open')) {
                    $item.find('.cpb-accordion__header').attr('aria-expanded', 'true');
                    $panel.attr('aria-hidden', 'false').show();
                } else {
                    $item.find('.cpb-accordion__header').attr('aria-expanded', 'false');
                    $panel.attr('aria-hidden', 'true').hide();
                }
            });

            $group.on('click', '.cpb-accordion__header', function(e){
                e.preventDefault();

                var $button = $(this);
                var $item = $button.closest('.cpb-accordion__item');
                var panelId = $button.attr('aria-controls');
                var $panel = $('#' + panelId);
                var isOpen = $item.hasClass('is-open');

                $group.find('.cpb-accordion__item').not($item).each(function(){
                    var $openItem = $(this);

                    if (!$openItem.hasClass('is-open')) {
                        return;
                    }

                    var $openButton = $openItem.find('.cpb-accordion__header');
                    var openPanelId = $openButton.attr('aria-controls');
                    var $openPanel = $('#' + openPanelId);

                    $openItem.removeClass('is-open');
                    $openButton.attr('aria-expanded', 'false');
                    $openPanel.stop(true, true).slideUp(200).attr('aria-hidden', 'true');
                });

                if (isOpen) {
                    $item.removeClass('is-open');
                    $button.attr('aria-expanded', 'false');
                    $panel.stop(true, true).slideUp(200).attr('aria-hidden', 'true');
                } else {
                    $item.addClass('is-open');
                    $button.attr('aria-expanded', 'true');
                    $panel.stop(true, true).slideDown(200).attr('aria-hidden', 'false');
                }
            });
        });
    }

    initCommunicationsAccordions();

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
