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

    function formatString(template){
        if (typeof template !== 'string') {
            return '';
        }

        var args = Array.prototype.slice.call(arguments, 1);
        var usedIndexes = {};
        var result = template.replace(/%(\d+)\$s/g, function(match, number){
            var index = parseInt(number, 10) - 1;

            if (typeof args[index] === 'undefined') {
                usedIndexes[index] = true;
                return '';
            }

            usedIndexes[index] = true;
            return args[index];
        });

        var sequentialIndex = 0;

        return result.replace(/%s/g, function(){
            while (usedIndexes[sequentialIndex]) {
                sequentialIndex++;
            }

            var value = typeof args[sequentialIndex] !== 'undefined' ? args[sequentialIndex] : '';
            usedIndexes[sequentialIndex] = true;
            sequentialIndex++;
            return value;
        });
    }

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
        var $entityTableBody = $('#cpb-entity-list');
        var perPage = parseInt($entityTableBody.data('per-page'), 10) || 20;
        var columnCount = parseInt($entityTableBody.data('column-count'), 10) || 6;
        var $pagination = $('#cpb-entity-pagination');
        var $paginationContainer = $pagination.closest('.tablenav');
        var $entityFeedback = $('#cpb-entity-feedback');
        var placeholderMap = cpbAdmin.placeholderMap || {};
        var placeholderList = Array.isArray(cpbAdmin.placeholders) ? cpbAdmin.placeholders : [];
        var placeholderCount = Object.keys(placeholderMap).length || placeholderList.length || 28;
        var currentPage = 1;
        var emptyValue = '—';

        if ($entityFeedback.length){
            $entityFeedback.hide().removeClass('is-visible');
        }

        if ($paginationContainer.length){
            $paginationContainer.hide();
        }

        function clearFeedback(){
            if ($entityFeedback.length){
                $entityFeedback.text('').hide().removeClass('is-visible');
            }
        }

        function showFeedback(message){
            if (!$entityFeedback.length){
                return;
            }

            if (message){
                $entityFeedback.text(message).show().addClass('is-visible');
            } else {
                clearFeedback();
            }
        }

        function getPlaceholderLabel(index){
            var mapKey = 'placeholder_' + index;

            if (Object.prototype.hasOwnProperty.call(placeholderMap, mapKey) && placeholderMap[mapKey]){
                return placeholderMap[mapKey];
            }

            if (placeholderList.length >= index){
                return placeholderList[index - 1];
            }

            return 'Placeholder ' + index;
        }

        function formatValue(value){
            if (value === null || typeof value === 'undefined' || value === ''){
                return emptyValue;
            }

            return String(value);
        }

        function updatePagination(total, totalPages, page){
            if (!$pagination.length){
                return;
            }

            if (!total || total <= 0){
                $pagination.empty();

                if ($paginationContainer.length){
                    $paginationContainer.hide();
                }

                return;
            }

            var totalPagesSafe = totalPages && totalPages > 0 ? totalPages : 1;
            var pageSafe = page && page > 0 ? page : 1;
            var html = '<span class="displaying-num">' + formatString(cpbAdmin.totalRecords, total) + '</span>';

            if (totalPagesSafe > 1){
                html += '<span class="pagination-links">';

                if (pageSafe > 1){
                    html += '<a class="first-page button cpb-entity-page" href="#" data-page="1"><span class="screen-reader-text">' + cpbAdmin.firstPage + '</span><span aria-hidden="true">&laquo;</span></a>';
                    html += '<a class="prev-page button cpb-entity-page" href="#" data-page="' + (pageSafe - 1) + '"><span class="screen-reader-text">' + cpbAdmin.prevPage + '</span><span aria-hidden="true">&lsaquo;</span></a>';
                } else {
                    html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
                    html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
                }

                html += '<span class="tablenav-paging-text">' + formatString(cpbAdmin.pageOf, pageSafe, totalPagesSafe) + '</span>';

                if (pageSafe < totalPagesSafe){
                    html += '<a class="next-page button cpb-entity-page" href="#" data-page="' + (pageSafe + 1) + '"><span class="screen-reader-text">' + cpbAdmin.nextPage + '</span><span aria-hidden="true">&rsaquo;</span></a>';
                    html += '<a class="last-page button cpb-entity-page" href="#" data-page="' + totalPagesSafe + '"><span class="screen-reader-text">' + cpbAdmin.lastPage + '</span><span aria-hidden="true">&raquo;</span></a>';
                } else {
                    html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
                    html += '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
                }

                html += '</span>';
            } else {
                html += '<span class="tablenav-paging-text">' + formatString(cpbAdmin.pageOf, pageSafe, totalPagesSafe) + '</span>';
            }

            $pagination.html(html);

            if ($paginationContainer.length){
                $paginationContainer.show();
            }
        }

        function renderEntities(data){
            var entities = data && Array.isArray(data.entities) ? data.entities : [];
            currentPage = data && data.page ? data.page : 1;
            var total = data && typeof data.total !== 'undefined' ? data.total : 0;
            var totalPages = data && data.total_pages ? data.total_pages : 1;

            $entityTableBody.empty();

            if (!entities.length){
                var $emptyRow = $('<tr class="no-items"></tr>');
                var $emptyCell = $('<td/>').attr('colspan', columnCount).text(cpbAdmin.none);
                $emptyRow.append($emptyCell);
                $entityTableBody.append($emptyRow);
                updatePagination(total, totalPages, currentPage);
                return;
            }

            entities.forEach(function(entity){
                var entityId = entity.id || 0;
                var headerId = 'cpb-entity-' + entityId + '-header';
                var panelId = 'cpb-entity-' + entityId + '-panel';

                var $summaryRow = $('<tr/>', {
                    id: headerId,
                    'class': 'cpb-accordion__summary-row',
                    tabindex: 0,
                    role: 'button',
                    'aria-expanded': 'false',
                    'aria-controls': panelId
                });

                var $titleCell = $('<td/>', {'class': 'cpb-accordion__cell cpb-accordion__cell--title'});
                var $titleText = $('<span/>', {'class': 'cpb-accordion__title-text'}).text(formatValue(entity.placeholder_1));
                $titleCell.append($titleText);
                $summaryRow.append($titleCell);

                for (var index = 2; index <= 5; index++) {
                    var label = getPlaceholderLabel(index);
                    var valueKey = 'placeholder_' + index;
                    var value = formatValue(entity[valueKey]);
                    var $metaCell = $('<td/>', {'class': 'cpb-accordion__cell cpb-accordion__cell--meta'});
                    var $metaText = $('<span/>', {'class': 'cpb-accordion__meta-text'});
                    $metaText.append($('<span/>', {'class': 'cpb-accordion__meta-label'}).text(label + ':'));
                    $metaText.append(' ');
                    $metaText.append($('<span/>', {'class': 'cpb-accordion__meta-value'}).text(value));
                    $metaCell.append($metaText);
                    $summaryRow.append($metaCell);
                }

                var $actionsCell = $('<td/>', {'class': 'cpb-accordion__cell cpb-accordion__cell--actions'});
                var $editText = $('<span/>', {'class': 'cpb-accordion__action-link', 'aria-hidden': 'true'}).text(cpbAdmin.editAction);
                var $icon = $('<span/>', {'class': 'dashicons dashicons-arrow-down-alt2 cpb-accordion__icon', 'aria-hidden': 'true'});
                var $srText = $('<span/>', {'class': 'screen-reader-text'}).text(cpbAdmin.toggleDetails);
                $actionsCell.append($editText);
                $actionsCell.append($icon).append($srText);
                $summaryRow.append($actionsCell);
                $entityTableBody.append($summaryRow);

                var $panelRow = $('<tr/>', {
                    id: panelId,
                    'class': 'cpb-accordion__panel-row',
                    role: 'region',
                    'aria-labelledby': headerId,
                    'aria-hidden': 'true'
                }).hide();

                var $panelCell = $('<td/>').attr('colspan', columnCount);
                var $panel = $('<div/>', {'class': 'cpb-accordion__panel'}).hide();

                var $nameField = $('<p/>', {'class': 'cpb-entity__field cpb-entity__field--name'});
                $nameField.append($('<strong/>').text(cpbAdmin.nameLabel + ':'));
                $nameField.append(' ');
                $nameField.append($('<span/>').text(formatValue(entity.name)));
                $panel.append($nameField);

                for (var i = 1; i <= placeholderCount; i++) {
                    var placeholderLabel = getPlaceholderLabel(i);
                    var key = 'placeholder_' + i;
                    var displayValue = formatValue(entity[key]);
                    var $field = $('<p/>', {'class': 'cpb-entity__field'});
                    $field.append($('<strong/>').text(placeholderLabel + ':'));
                    var hasImage = (i === 27 && entity[key + '_url']);

                    if (hasImage) {
                        $field.append(' ');
                        $field.append($('<img/>', {
                            src: entity[key + '_url'],
                            alt: placeholderLabel,
                            style: 'max-width:100px;height:auto;'
                        }));

                        if (displayValue !== emptyValue) {
                            $field.append(' ');
                            $field.append($('<span/>').text(displayValue));
                        }
                    } else {
                        $field.append(' ');
                        $field.append($('<span/>').text(displayValue));
                    }

                    $panel.append($field);
                }

                var $actions = $('<p/>', {'class': 'cpb-entity__actions'});
                var $deleteButton = $('<button/>', {
                    type: 'button',
                    'class': 'button button-secondary cpb-delete',
                    'data-id': entityId
                }).text(cpbAdmin.delete);
                $actions.append($deleteButton);
                $panel.append($actions);

                $panelCell.append($panel);
                $panelRow.append($panelCell);
                $entityTableBody.append($panelRow);
            });

            updatePagination(total, totalPages, currentPage);
        }

        function fetchEntities(page){
            var targetPage = page || 1;
            clearFeedback();

            $.post(cpbAjax.ajaxurl, {
                action: 'cpb_read_main_entity',
                _ajax_nonce: cpbAjax.nonce,
                page: targetPage,
                per_page: perPage
            })
                .done(function(response){
                    if (response && response.success && response.data){
                        renderEntities(response.data);
                    } else {
                        showFeedback(cpbAdmin.loadError || cpbAdmin.error);
                    }
                })
                .fail(function(){
                    showFeedback(cpbAdmin.loadError || cpbAdmin.error);
                });
        }

        fetchEntities(1);

        if ($pagination.length){
            $pagination.on('click', '.cpb-entity-page', function(e){
                e.preventDefault();
                var targetPage = parseInt($(this).data('page'), 10);

                if (!targetPage || targetPage === currentPage){
                    return;
                }

                fetchEntities(targetPage);
            });
        }

        $entityTableBody.on('click', '.cpb-delete', function(){
            var id = $(this).data('id');

            if (!id){
                return;
            }

            clearFeedback();

            $.post(cpbAjax.ajaxurl, {
                action: 'cpb_delete_main_entity',
                id: id,
                _ajax_nonce: cpbAjax.nonce
            })
                .done(function(resp){
                    if (resp && resp.success){
                        fetchEntities(currentPage);
                    } else {
                        showFeedback(cpbAdmin.error);
                    }
                })
                .fail(function(){
                    showFeedback(cpbAdmin.error);
                });
        });
    }

    $('.cpb-accordion').on('click','.item-header',function(){
        $(this).next('.item-content').slideToggle();
        $(this).parent().toggleClass('open');
    });

    function initAccordionGroups(){
        $('[data-cpb-accordion-group]').each(function(){
            var $group = $(this);

            if ($group.data('cpbAccordionInitialized')) {
                return;
            }

            $group.data('cpbAccordionInitialized', true);

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

    initAccordionGroups();

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
