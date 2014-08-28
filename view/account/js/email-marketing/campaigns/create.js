var EmailEditor = {

    contentTypeDraggables: null
    , layoutContainer: null
    , layoutSelectors: null
    , layouts: null
    , placeholderSelector: null

    , contentTypes: {

        _base:  {
            init: function() {
                $('body').on('click', '[data-action=clear]', EmailEditor.contentTypes._base.clear );
            }
            , setup: function(my_content) {
                // Set an ID
                if ( !$(my_content).attr('id') )
                    $(my_content).attr('id', 'ct' + Date.now());
                my_content.find('.hidden').removeClass('hidden').hide();
            }
            , clear: function() {
                if (confirm('Are you sure do you want to remove this element?')) {
                    $(this).parents('.droppable')
                        .html('<p class="placeholder">Drag Content Here</p>')
                        .removeClass('has-content')
                        .removeClass('empty-content-type');
                }
            }
        }

        , product: {
            content: $('#email-builder-types div.content-type-template[data-content-type=product]')
            , init: function() {
                $('#email-editor').on('click', 'div[data-content-type=product] [data-action=edit]', EmailEditor.contentTypes.product.showEdit );
                $('#email-editor').on('click', '[data-action=edit-price]', EmailEditor.contentTypes.product.showEditPrice );
                $('#email-editor').on('click', '[data-action=save-price]', EmailEditor.contentTypes.product.savePrice );
            }
            , setup: function(my_content) {
                EmailEditor.contentTypes._base.setup(my_content);

                // Show autocomplete if it's a brand new product box
                if ( my_content.find('.placeholder-content').is(':empty')  ) {
                    my_content.find('.product-autocomplete').show();
                    my_content.find('[data-action=edit-price]').hide();
                } else {
                    my_content.find('[data-action=edit]').show();
                }

                var _nonce = $('#_autocomplete_owned').val();
                var autocomplete = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
                    , queryTokenizer: Bloodhound.tokenizers.whitespace
                    , remote: {
                        url: '/products/autocomplete-owned/?_nonce=' + _nonce + '&type[]=name&type[]=sku&term=%QUERY'
                        , filter: function( list ) {
                            return list.suggestions
                        }
                    }
                });
                autocomplete.initialize();
                my_content.find('.product-autocomplete')
                    .typeahead(null, {
                        displayKey: 'name'
                        , source: autocomplete.ttAdapter()
                    })
                    .unbind('typeahead:selected')
                    .on('typeahead:selected', function(event, selected) {
                        EmailEditor.contentTypes.product.selectProduct( my_content, event, selected );
                    });
            }
            , showEdit: function() {
                $(this).parents('div[data-content-type]')
                    .find('.product-autocomplete').show();
                $(this).parents('div[data-content-type]')
                    .find('[data-action=edit], [data-action=edit-price]').hide();
            }
            , showEditPrice: function() {
                var placeholder = $(this).parents('div[data-content-type]');

                var price = placeholder.find('.product-price-container .price').text().parseProductPrice();
                var sale_price = placeholder.find('.product-price-container .sale-price').text().parseProductPrice();

                // if it's not a number, show "as is"
                price = price > 0 ? price : placeholder.find('.product-price-container .price').text();
                sale_price = sale_price > 0 ? sale_price : placeholder.find('.product-price-container .sale-price').text();

                placeholder.find('.product-price').val(price);
                placeholder.find('.product-sale-price').val(sale_price);

                placeholder.find('[data-action=edit], [data-action=edit-price]').hide();
                placeholder.find('.edit-price-actions').show();
            }
            , selectProduct: function( my_content, event, item ) {
                $.get(
                    '/products/get/'
                    , { '_nonce': $('#_get').val(), 'pid': item.value }
                    , function(r) {
                        var box_width = my_content.attr('width');
                        var img_width = box_width * ( ( box_width < 200 ) ? 0.9  : 0.6 );  // 90% for colspan 1,2,3. 60% for the rest
                        var tpl = '<div class="product-img" width="' + img_width + '"><a href="' + r.product.link + '"><img src="' + r.product.image + '" width="' + img_width + '" /></a></div>';
                        tpl += '<div class="product-content">';
                        tpl += '<a href="' + r.product.link + '"><h2>' + r.product.name + '</h2></a>';

                        tpl += '<div class="product-price-container">';
                        if ( r.product.sale_price > 0 )
                            tpl += '<span class="sale-price">$' + r.product.sale_price + '</span> <span class="price strikethrough">$' + r.product.price + '</span>';
                        else if (r.product.price > 0 )
                            tpl += '<span class="price">$' + r.product.price + '</span>';
                        tpl += '</div>';

                        tpl += '</div>';
                        my_content.find('.placeholder-content').html(tpl);

                        // Hide autocomplete
                        my_content.find('.product-autocomplete').hide();

                        // Show edit price
                        my_content.find('[data-action=edit-price]').show();

                        // Show select product icon
                        my_content.find('[data-action=edit]').show();

                        // mark is as has-content
                        my_content.parents('.droppable')
                            .removeClass('empty-content-type').addClass('has-content');
                    }
                    , 'json'
                );
            }
            , savePrice: function(e) {
                var placeholder = $(this).parents('[data-content-type]');
                var price = placeholder.find('.product-price').val();
                var sale_price = placeholder.find('.product-sale-price').val();

                var price_str = price > 0 ? ( '$' + price.parseProductPrice().priceFormat() ) : price;
                var sale_price_str = sale_price > 0 ? ( '$' + sale_price.parseProductPrice().priceFormat() ) : sale_price;

                if ( sale_price > 0 && price == 0 )
                    return alert("Sale Price needs a Price");

                var tpl = '';
                if ( sale_price_str )
                    tpl = '<span class="sale-price">' + sale_price_str + '</span> <span class="price strikethrough">' + price_str + '</span>';
                else if ( price_str  )
                    tpl = '<span class="price">' + price_str + '</span>';

                placeholder.find('.product-price-container').html(tpl);
                placeholder.find('.edit-price-actions').hide();
                placeholder.find('[data-action=edit-price]').show();
                placeholder.find('[data-action=edit]').show();
            }
        }

        , text: {
            content: $('#email-builder-types div.content-type-template[data-content-type=text]')
            , init: function() {
                $('body').on( 'click', '.open-text-editor', EmailEditor.contentTypes.text.showEditor );
                $('body').on( 'click', '#save-text', EmailEditor.contentTypes.text.saveText );
            }
            , setup: function(my_content) {
                EmailEditor.contentTypes._base.setup(my_content);
            }
            , showEditor: function() {
                var instance = $(this).parents('[data-content-type]');
                var placeholder_id = instance.attr('id');
                var content = instance.find('.placeholder-content').html();
                var textarea = $('<textarea id="text-editor" name="text-editor"></textarea>');

                textarea.val(content);

                // cleanup previous RTE Editors if any
                if (CKEDITOR.instances['text-editor']) {
                    delete CKEDITOR.instances['text-editor'];
                };

                $('#save-text').data('placeholder-id', placeholder_id);
                $('#editor-container').html( textarea );

                CKEDITOR.replace('text-editor', {
                    allowedContent: !0,
                    autoGrow_minHeight: 100,
                    resize_minHeight: 100,
                    height: 100,
                    toolbar: [
                        ["Bold", "Italic", "Underline"],
                        ["JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"],
                        ["NumberedList", "BulletedList", "Table"],
                        ["Format"],
                        ["Link", "Unlink"],
                        ["Source"]
                    ]
                });
            }
            , saveText: function() {
                var placeholder_id = $(this).data('placeholder-id');
                var text = CKEDITOR.instances['text-editor'].getData();
                $('#' + placeholder_id + ' .placeholder-content').html(text);
                // mark is as has-content
                $('#' + placeholder_id).parents('.droppable')
                    .removeClass('empty-content-type').addClass('has-content');
            }
        }

        , image: {
            'content': $('#email-builder-types div.content-type-template[data-content-type=image]')
            , init: function() {
                // Open Media Manager
                $('#email-editor').on('click', '[data-media-manager]', function() {
                    var placeholder_id = $(this).parents('[data-content-type]').attr('id');
                    MediaManager.placeholder_id = placeholder_id;
                });
                // Overwrite Media Manager "Submit"
                MediaManager.submit = EmailEditor.contentTypes.image.selectImage;
                // Edit Link
                $('#email-editor').on('click', '[data-action=edit-link]', function() {
                    $(this).siblings('[data-action=save-link], .image-link-url').show();
                    $(this).hide();
                });
                // Edit Link Submit
                $('#email-editor').on('click', '[data-action=save-link]', EmailEditor.contentTypes.image.saveImageLink);
            }
            , setup: function(my_content) {
                EmailEditor.contentTypes._base.setup(my_content);
                //sparrow();
            }
            , selectImage: function() {
                var file = MediaManager.view.find('.mm-file.selected:first').parents( 'li:first').data();
                var placeholder_id = MediaManager.placeholder_id;
                var box_width = $('#' + placeholder_id).attr('width');

                if ( file ) {
                    var image = $('<img src="' + file.url + '" alt="' + file.url + '" width="' + box_width + '" />');
                    $('#' + placeholder_id + ' .placeholder-content').html(image);
                    $('#' + placeholder_id + ' [data-action=edit-link]').show();
                    // mark is as has-content
                    $('#' + placeholder_id).parents('.droppable')
                        .removeClass('empty-content-type').addClass('has-content');
                }

                delete MediaManager.placeholder_id;
            }
            , saveImageLink: function(e) {
                var url = $(this).siblings('.image-link-url').val();
                var placeholder = $(this).parents('[data-content-type]');
                var img = placeholder.find('.placeholder-content img');
                var img_html = img.size() > 0 ? img[0].outerHTML : '';

                // if url is set, set an anchor wrapping the image
                if ( url ) {
                    // check for valid url
                    if ( !EmailEditor.contentTypes.image.is_url( url ) ) {
                        alert('Please enter a valid URL');
                        return;
                    }
                    var anchor = '<a href="'+ url +'">' + img_html + '</a>';
                    placeholder.find('.placeholder-content').html(anchor);
                } else {
                    // no url - no link
                    placeholder.find('.placeholder-content').html(img_html);
                }

                // hide controls
                placeholder.find('[data-action=save-link], .image-link-url').hide();
                placeholder.find('[data-action=edit-link]').show();
            }
            , is_url: function( url ) {
                var regex = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                var re = new RegExp(regex);
                return re.test(url);
            }
        }

    }

    , init: function() {
        EmailEditor.contentTypeDraggables = $('li[data-content-type]');
        EmailEditor.layoutContainer = $('#email-editor');
        EmailEditor.layoutSelectors = $('li[data-layout]');
        EmailEditor.layouts = $('div[data-layout]');
        EmailEditor.placeholderSelector = '.droppable';

        // Make Content Types Draggable
        EmailEditor.contentTypeDraggables.draggable({
            opacity: '0.7'
            , helper: 'clone'
        });

        // Make Placeholders Droppable, allows Content Type to be added on them
        // This gets called every time the layout changes
        EmailEditor.layoutContainer.bindPlaceholders = EmailEditor.bindPlaceholders;

        // Change Layout
        EmailEditor.layoutSelectors.click( EmailEditor.changeLayout );

        // -- Initialize Events For Email Message --
        // if we have content (email message), apply events to them
        var current_content = EmailEditor.layoutContainer.find('div[data-content-type]');
        if ( current_content.size() > 0 ) {
            // bind events for empty placeholder
            EmailEditor.layoutContainer.bindPlaceholders();
            // bind events for placeholders with content in it
            $.each( current_content, function (k, v) {
                var ct = $(v).data('content-type');
                // we need to acc the action toolbar
                var ct_actions = EmailEditor.contentTypes[ct].content.find('.placeholder-actions').clone();
                $(v).prepend(ct_actions);
                // setup events
                EmailEditor.contentTypes[ct].setup($(v));
            });
        } else {
            // if it's a new Campaign, just pick the first Layout
            EmailEditor.layoutSelectors.first().click();
        }

        // Call init() for all content types
        for(i in EmailEditor.contentTypes) {
            EmailEditor.contentTypes[i].init();
        }
    }

    , bindPlaceholders: function() {
        this.find(EmailEditor.placeholderSelector).droppable({
            accept: '[data-content-type]'
            , hoverClass: 'droppable-hover'
            , drop: function(event, ui) {
                // Its a new content added to a placeholder
                var placeholder = $(this);
                var content_type_key = ui.draggable.data('content-type');
                var content_type = EmailEditor.contentTypes[content_type_key];
                var my_content = content_type.content.clone();
                var box_width = placeholder.attr('width');
                my_content.removeClass('content-type-template');
                my_content.attr('width', box_width);
                my_content.find('.placeholder-content').attr('width', box_width);
                placeholder.find('*').remove();
                placeholder.html(my_content).addClass('empty-content-type');
                content_type.setup(my_content);
            }
        });
    }

    , changeLayout: function() {
        var has_content = EmailEditor.layoutContainer.find('.placeholder-content').size() > 0;
        if ( has_content && !confirm("Do you want to change the email Layout? You will lose all you previous content.") )
            return;

        var layout_key = $(this).data('layout');
        var layout = EmailEditor.layouts.siblings('[data-layout=' + layout_key + ']');
        var my_layout = layout.html();
        EmailEditor.layoutContainer.find('*').remove();
        EmailEditor.layoutContainer.html(my_layout);
        // Rebind elements on Droppable, jQueryUI has no live binding for droppable =(
        EmailEditor.layoutContainer.bindPlaceholders();

        EmailEditor.layoutSelectors.removeClass('active');
        $(this).addClass('active');
    }

    , getEmailContent: function() {
        var editor_content = EmailEditor.layoutContainer.clone();
        editor_content.find('.placeholder-actions').remove().end()
            .find('*').removeClass('ui-droppable').end()
            .find('.placeholder').remove().end();

        return editor_content;
    }

    , getSelectedLayout: function() {
        return EmailEditor.layoutSelectors.closest('.active');
    }

}

var CampaignForm = {

    init: function() {

        // Multiple Step Form
        $('a[data-step]').click( CampaignForm.changeStep );
        $('div[data-step].hidden').removeClass('hidden').hide();

        CampaignForm.initStep1();
        CampaignForm.initStep2();
        CampaignForm.initStep3();
    }

    , initStep1: function() {

        // Date Picker - No Conflict with jQueryUI
        var datepicker = $.fn.datepicker.noConflict();
        $.fn.bootstrapDatepicker = datepicker;

        // Inline DIV
        $('#schedule-datepicker').bootstrapDatepicker({
             todayHighlight: true
            , dateFormat: 'yyyy-mm-dd'
        }).on( 'changeDate', function(e) {
            $("#date").val(e.format('yyyy-mm-dd'));
        }).bootstrapDatepicker( 'setDate', $('#date').val() );

        // Time Picker
        var time = $('#tTime');
        time.timepicker({
            step: 60
            , show24Hours: false
            , timeFormat: 'g:i a'
        });

        // Schedule options are only displayed if #schedule is checked
        $('#schedule').change(function() {
            if ($(this).is(':checked')) {
                $('.schedule').removeClass('hidden').show();
            } else {
                $('.schedule').hide();
            }
        });

        // Subscribers List
        $('#select-all-subscribers').change(function(e){
            if ($(this).prop('checked') === true) {
                $('.subscribers :checkbox').prop('checked', true);
                $('.subscribers :checkbox').attr('checked', 'checked');  // IE
            } else {
                $('.subscribers :checkbox').prop('checked', false);
                $('.subscribers :checkbox').removeAttr('checked');  // IE
            }
        });
    }

    , initStep2: function() {
        EmailEditor.init();
    }

    , initStep3: function() {
        $('body').on( 'click', '.send-test', CampaignForm.sendTest );
        $('.save-draft').click( CampaignForm.saveDraft );
        $('.save-campaign').click( CampaignForm.save );
    }

    , changeStep: function() {
        var step = $(this).data('step');

        if ( !CampaignForm.validate() )
            return;

        // Show Step
        $('div[data-step]').hide();
        $('div[data-step=' + step + ']').show();

        // Toggle active marker in progress bar
        $('.form-steps a[data-step=' + step + ']')
            .parent().addClass('active')
            .siblings().removeClass('active');

        if ( step == 3 ) {
            CampaignForm.loadPreview();
        }
    }

    , validate: function() {
        var current_step = $('li.active > a').data('step');

        if ( current_step == 1 ) {
            var validation = '';
            var subject = $('#subject').val();
            var subscribers_selected = $('.subscribers :checked').size();
            if ( $.trim(subject) == 0 ) {
                validation += "An Email Subject is required. ";
            }
            if (subscribers_selected == 0 ) {
                validation += "Please select at least one Subscribers list. ";
            }
            if ( validation ) {
                GSR.defaultAjaxResponse( { error: validation } );
                return false;
            }
        }
        return true;
    }

    , loadPreview: function() {
        // Get form data
        var data = CampaignForm.getFormData();
        data._nonce = $('#_save_draft').val();

        // Post, then load preview as Iframe
        $.post( '/email-marketing/campaigns/save-draft/', data, function(r) {

            if ( r.notification && r.notification.success )
                delete r.notification;

            GSR.defaultAjaxResponse(r);

            if ( r.campaign_id ) {
                $('#campaign-id').remove();
                $('<input />', {id: 'campaign-id', name: 'id', type: 'hidden'})
                    .val( r.campaign_id )
                    .appendTo('div[data-step=1]');
            }

            var campaign_id = r.campaign_id ? r.campaign_id : $('#campaign-id').val();

            var iframe = $('<iframe/>', {
                id: 'preview-iframe'
                , frameborder: 0
                , scrolling: 'no'
                , width: '100%'
                , seamless: 'seamless'
                , src: '/email-marketing/campaigns/preview/?_nonce='+ $('#_preview').val() +'&id='+ campaign_id
            });
            $('#email-preview').removeAttr('style');
            iframe.load(function(){
                var doc = this.contentDocument || this.contentWindow.document;

                $('#email-preview').width(doc.body.scrollWidth);
                iframe.width(doc.body.scrollWidth);

                $('#email-preview').height(doc.body.scrollHeight);
                iframe.height(doc.body.scrollHeight);
            });
            $('#email-preview').html(iframe);
        } );
    }

    , getFormData: function() {
        var data = {};

        // Set Step 1 data (campaign settings)
        $.each(
            $('div[data-step=1]').find('input, textarea, select').serializeArray()
            , function(k, v) {
                if ( v.name.indexOf("[]") > 0 ) {
                    var entry_name = v.name.replace("[]", "");
                    if ( typeof(data[entry_name]) == "undefined" ) {
                        data[entry_name] = [];
                    }
                    data[entry_name].push(v.value);
                } else {
                    data[v.name] = v.value;
                }
            }
        );

        // Message HTML
        data.message = EmailEditor.getEmailContent().html();
        data.layout = EmailEditor.getSelectedLayout().data('layout');
        if ( $('#no-template').prop('checked') )
            data.no_template = "yes";
        return data;
    }

    , sendTest: function() {
        // Get form data
        var data = CampaignForm.getFormData();
        data._nonce = $('#_send_test').val();
        data.email = $('#test-destination').val();

        // Post!
        $.post( '/email-marketing/campaigns/send-test/', data, GSR.defaultAjaxResponse );
    }

    , saveDraft: function() {
        if ($(this).hasClass('disabled'))
            return;

        $(this).addClass('disabled').text('Saving...');

        // Get form data
        var data = CampaignForm.getFormData();
        data._nonce = $('#_save_draft').val();

        // Post!
        $.post( '/email-marketing/campaigns/save-draft/', data, CampaignForm.saveDraftResponse );
    }

    , saveDraftResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            if ( response.campaign_id ) {
                $('#campaign-id').remove();
                $('<input />', {id: 'campaign-id', name: 'id', type: 'hidden'})
                    .val( response.campaign_id )
                    .appendTo('div[data-step=1]');
            }
            $('.save-draft').removeClass('disabled').text('Draft Saved!');
        } else {
            $('.save-draft').removeClass('disabled').text('Save Draft');
        }
    }

    , save: function() {
        if ($(this).hasClass('disabled'))
            return;

        $(this).addClass('disabled').text('Saving...');

        // Get form data
        var data = CampaignForm.getFormData();
        data._nonce = $('#_save_campaign').val();

        // Post!
        $.post( '/email-marketing/campaigns/save-campaign/', data, CampaignForm.saveResponse );
    }

    , saveResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            window.location = '/email-marketing/campaigns/';
        } else {
            if ( response.campaign_id ) {
                $('#campaign-id').remove();
                $('<input />', {id: 'campaign-id', name: 'id', type: 'hidden'})
                    .val( response.campaign_id )
                    .appendTo('div[data-step=1]');
            }

            $('.save-draft').removeClass('disabled').text('Looks Good! Send it Out.');
        }
    }

}

String.prototype.parseProductPrice = function() {
    return parseFloat( this.replace(/\$| |,/g, '') )
}
Number.prototype.priceFormat = function() {
    return this.toFixed(2).toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
};



jQuery( CampaignForm.init );