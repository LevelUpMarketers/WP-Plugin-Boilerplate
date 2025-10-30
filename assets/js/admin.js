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
        $('[data-cpb-accordion-group="communications"]').each(function(){
            var $group = $(this);

            function closeRow($summary, $panelRow){
                if (!$summary.length || !$panelRow.length) {
                    return;
                }

                $summary.removeClass('is-open').attr('aria-expanded', 'false');

                var $panel = $panelRow.find('.cpb-accordion__panel');

                $panel.stop(true, true).slideUp(200, function(){
                    $panelRow.hide();
                });

                $panelRow.attr('aria-hidden', 'true');
            }

            $group.find('.cpb-accordion__summary-row').each(function(){
                var $summary = $(this);
                var panelId = $summary.attr('aria-controls');
                var $panelRow = $('#' + panelId);

                if (!$panelRow.length) {
                    return;
                }

                $summary.removeClass('is-open').attr('aria-expanded', 'false');
                $panelRow.hide().attr('aria-hidden', 'true');
                $panelRow.find('.cpb-accordion__panel').hide();
            });

            function toggleRow($summary){
                var panelId = $summary.attr('aria-controls');
                var $panelRow = $('#' + panelId);

                if (!$panelRow.length) {
                    return;
                }

                if ($summary.hasClass('is-open')) {
                    closeRow($summary, $panelRow);
                    return;
                }

                $group.find('.cpb-accordion__summary-row.is-open').each(function(){
                    var $openSummary = $(this);
                    var openPanelId = $openSummary.attr('aria-controls');
                    var $openPanelRow = $('#' + openPanelId);

                    closeRow($openSummary, $openPanelRow);
                });

                $summary.addClass('is-open').attr('aria-expanded', 'true');
                $panelRow.show().attr('aria-hidden', 'false');
                $panelRow.find('.cpb-accordion__panel').stop(true, true).slideDown(200);
            }

            $group.on('click', '.cpb-accordion__summary-row', function(e){
                if ($(e.target).closest('a, button, input, textarea, select, label').length) {
                    return;
                }

                toggleRow($(this));
            });

            $group.on('keydown', '.cpb-accordion__summary-row', function(e){
                var key = e.key || e.keyCode;

                if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
                    e.preventDefault();
                    toggleRow($(this));
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
