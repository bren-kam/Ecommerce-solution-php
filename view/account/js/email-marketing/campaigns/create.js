head.load( 'http://code.jquery.com/ui/1.10.4/jquery-ui.min.js', '/ckeditor/ckeditor.js', function() {

    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // STEP 1

    // Date Picker
    $('#dDate').datepicker({
        minDate: 0
        , dateFormat: 'yy-mm-dd'
        , defaultDate: $('#date').val()
        , onSelect: function(date) {
            $("#date").val(date);
        }
    });

    // Time Picker
    var tTime = $('#tTime');
    tTime.timepicker({
        step: 60
        , show24Hours: false
        , timeFormat: 'g:i a'
    }).timepicker('show');

    // Fix for offset
    tTime.timepicker('hide');

    // Schedule options are only displayed if #schedule is checked
    $('#schedule').change(function() {
        if ($(this).is(':checked')) {
            $('.schedule').removeClass('hidden');
        } else {
            $('.schedule').addClass('hidden');
        }
    }).change();

    $('#select-all-subscribers').change(function(){
        if ($(this).is(':checked')) {
            $('.subscribers :checkbox').prop('checked', true);
        } else {
            $('.subscribers :checkbox').prop('checked', false);
        }
    });

    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // STEP 2

    var content_type_draggables = $('li[data-content-type]');
    var layout_container = $('#email-editor');
    var layout_selectors = $('li[data-layout]');
    var layouts = $('div[data-layout]');
    var placeholder_selector = '.droppable';

    // Here we define out Content Types (text, product, image)
    // and place the code that handles it
    // - init() bind events
    // - setup() called when a Content Type is dropped into a Placeholder (created editor, uploader, etc)
    // - save() will save Content Type Data
    var content_types = {

        _base:  {
            init: function() {
                $('body').on('click', '[data-action=clear]', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure do you want to remove this element?')) {
                        $(this).parents('.droppable')
                            .html('<p class="placeholder">Drag Content Here</p>')
                            .removeClass('has-content')
                            .removeClass('empty-content-type');
                    }
                });
            }
            , setup: function(my_content) {
                // Set an ID
                if ( !$(my_content).attr('id') )
                    $(my_content).attr('id', 'ct' + Date.now());
            }
        }

        , product: {
            content: $('#email-builder-types div.content-type-template[data-content-type=product]')
            , init: function() {
                $('body').on('click', 'div[data-content-type=product] [data-action=edit]', function(e) {
                    e.preventDefault();
                    $(this).parents('div[data-content-type]').find('.products-autocomplete').show();
                });
            }
            , setup: function(my_content) {
                content_types._base.setup(my_content);
                my_content.find('.products-autocomplete').autocomplete({
                    minLength: 1
                    , source: function(request, response){
                        var type = ['name' , 'sku'];
                        $.post(
                            '/products/autocomplete-owned/'
                            , { '_nonce' : $('#_autocomplete_owned').val(), 'type' : type, 'term' : request['term'] }
                            , function( autocompleteResponse ) {
                                response( autocompleteResponse['suggestions'] );
                            }
                            , 'json'
                        );
                    }
                    , select: function( event, ui ) {
                        event.preventDefault();
                        $.post(
                            '/products/get-product-dialog-info/'
                            , { '_nonce': $('#_get_product_dialog_info').val(), 'pid': ui.item.value }
                            , function(r) {
                                var tpl = '<div class="product-img"><a href="' + r.product.link + '"><img src="' + r.product.image + '" /></a></div>';
                                tpl += '<div class="product-content">';
                                tpl += '<a href="' + r.product.link + '"><h2>' + r.product.name.substring(0, 30) + '</h2></a>';

                                if ( r.product.sale_price > 0 )
                                    tpl += '<span class="sale-price">$' + r.product.sale_price + '</span> <span class="price strikethrough">$' + r.product.price + '</span>';
                                else if (r.product.price > 0 )
                                    tpl += '<span class="price">$' + r.product.price + '</span>';

                                tpl += '</div>';
                                my_content.find('.placeholder-content').html(tpl);

                                // Hide autocomplete
                                my_content.find('.products-autocomplete').hide();

                                // mark is as has-content
                                my_content.parents('.droppable')
                                    .removeClass('empty-content-type').addClass('has-content');
                            }
                            , 'json'
                        );
                    }
                }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                        .append( "<a>" + item.name + "<br><small>SKU: " + item.sku + "</small></a>" )
                        .appendTo( ul );
                };
            }
        }

        , text: {
            content: $('#email-builder-types div.content-type-template[data-content-type=text]')
            , init: function() {
                $('#save-text').click(content_types['text'].save_text);
                $('body').on('click', '.open-text-editor', function(e) {
                    var instance = $(this).parents('[data-content-type]');
                    var placeholder_id = instance.attr('id');
                    var content = instance.find('.placeholder-content').html();
                    var textarea = $('<textarea id="text-editor" name="text-editor"></textarea>');
                    $('#save-text').data('placeholder-id', placeholder_id);
                    textarea.val(content);
                    $('#editor-container').html(textarea).sparrow();
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
                });
            }
            , setup: function(my_content) {
                content_types._base.setup(my_content);
                my_content.sparrow();
            }
            , save_text: function(e) {
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
                // Media Manager "Select" button
                $('#select-image').click(content_types['image'].select_image);
                $('body').on('click', '.open-media-manager', function(e) {
                    var placeholder_id = $(this).parents('[data-content-type]').attr('id');
                    $('#select-image').data('placeholder-id', placeholder_id);
                });
                $('#email-editor').on('click', '[data-action=edit-link]', function(e) {
                    e.preventDefault();
                    $(this).siblings('[data-action=save-link], .image-link-url').removeClass('hidden');
                    $(this).addClass('hidden');
                });
                $('#email-editor').on('click', '[data-action=save-link]', content_types['image'].save_image_link);
            }
            , setup: function(my_content) {
                content_types._base.setup(my_content);
                sparrow();
            }
            , select_image: function(e) {
                e.preventDefault();
                var placeholder_id = $(this).data('placeholder-id');
                var image = $('a.file.selected img').clone();
                $('#' + placeholder_id + ' .placeholder-content').html(image);
                $('#' + placeholder_id + ' [data-action=edit-link]').removeClass('hidden');
                // mark is as has-content
                $('#' + placeholder_id).parents('.droppable')
                    .removeClass('empty-content-type').addClass('has-content');
            }
            , save_image_link: function(e) {
                e.preventDefault();
                var url = $(this).siblings('.image-link-url').val();
                var placeholder = $(this).parents('[data-content-type]');
                var img = placeholder.find('.placeholder-content img');
                var img_html = img.size() > 0 ? img[0].outerHTML : '';

                // if url is set, set an anchor wrapping the image
                if ( url ) {
                    // check for valid url
                    if ( !content_types['image'].is_url( url ) ) {
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
                placeholder.find('[data-action=save-link], .image-link-url').addClass('hidden');
                placeholder.find('[data-action=edit-link]').removeClass('hidden');
            }
            , is_url: function( url ) {
                var regex = /^(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                var re = new RegExp(regex);
                return re.test(url);
            }
        }

    };

    // Make Content Types Draggable
    content_type_draggables.draggable({
        opacity: '0.7'
        , helper: 'clone'
    });

    // Make Placeholders Droppable, allows Content Type to be added on them
    // This gets called every time the layout changes
    layout_container.bind_placeholders = function() {
        this.find(placeholder_selector).droppable({
            accept: '[data-content-type]'
            , hoverClass: 'droppable-hover'
            , drop: function(event, ui) {
                // Its a new content added to a placeholder
                var placeholder = $(this);
                var content_type_key = ui.draggable.data('content-type');
                var content_type = content_types[content_type_key]
                var my_content = content_type.content.clone();
                my_content.removeClass('content-type-template');
                placeholder.find('*').remove();
                placeholder.html(my_content).addClass('empty-content-type');
                content_type.setup(my_content);
            }
        });
    };

    // Change Layout
    layout_selectors.click(function() {
        var has_content = layout_container.find('.placeholder-content').size() > 0;
        if ( has_content && !confirm("Do you want to change the email Layout? You will lose all you previous content.") )
            return;

        var layout_key = $(this).data('layout');
        var layout = layouts.siblings('[data-layout=' + layout_key + ']');
        var my_layout = layout.html();
        layout_container.find('*').remove();
        layout_container.html(my_layout);
        // Rebind elements on Droppable, jQueryUI has no live binding for droppable =(
        layout_container.bind_placeholders();

        layout_selectors.removeClass('active');
        $(this).addClass('active');
    })

    // -- Initialize Events For Email Message --
    // if we have content (email message), apply events to them
    var current_content = layout_container.find('div[data-content-type]');
    if ( current_content.size() > 0 ) {
        // bind events for empty placeholder
        layout_container.bind_placeholders();
        // bind events for placeholders with content in it
        $.each( current_content, function (k, v) {
            var ct = $(v).data('content-type');
            // we need to acc the action toolbar
            var ct_actions = content_types[ct].content.find('.placeholder-actions').clone();
            $(v).prepend(ct_actions);
            // setup events
            content_types[ct].setup($(v));
        });
    } else {
        // if it's a new Campaign, just pick the first Layout
        layout_selectors.first().click();
    }

    // Call init() for all content types
    for(i in content_types) {
        content_types[i].init();
    }

    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // STEP 3

    // This will generate the email content that will be Sent
    function get_email_content() {
        var editor_content = layout_container.clone();
        editor_content.find('.placeholder-actions').remove().end()
            .find('*').removeClass('ui-droppable').end()
            .find('.placeholder').remove().end();

        return editor_content;
    }

    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // FORM GLOBALS

    // Multiple Step Form
    $('a[data-step]').click(function(e) {
        e.preventDefault();

        // Show form
        $('div[data-step]').addClass('hidden');
        $('div[data-step=' + $(this).data('step') + ']').removeClass('hidden');

        // Toggle active marker in progress bar
        $('.progress-bar a[data-step=' + $(this).data('step') + ']')
            .parent().addClass('active')
            .siblings().removeClass('active');

        if ( $(this).data('step') == 3 ) {
            // Do Save Draft and load Preview as Iframe

            // Get form data
            var data = get_form_data();
            data._nonce = $('#_save_draft').val();
            // Post, then load preview as Iframe
            $.post( '/email-marketing/campaigns/save-draft/', data, function(r) {

                if ( r.notification && r.notification.success )
                    delete r.notification;

                ajaxResponse(r);

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
    });

    // Serialize Form in a Key/Value Object
    function get_form_data() {
        var data = {};

        // Set Step 1 data (campaign settings)
        $.each(
            $('div[data-step=1]').find('input, textarea, select').serializeArray()
            , function(k, v) {
                data[v.name] = v.value;
            }
        );

        // Message HTML
        data.message = get_email_content().html();
        data.layout = layout_selectors.closest('.active').data('layout');
        if ( $('#no-template').prop('checked') )
            data.no_template = "yes";
        return data;
    }

    // Saves Campaign as Draft
    $('.save-draft').click(function(e) {
        e.preventDefault();

        if ($(this).hasClass('disabled'))
            return;

        $(this).addClass('disabled').text('Saving...');

        // Get form data
        var data = get_form_data();
        data._nonce = $('#_save_draft').val();

        // Post!
        $.post( '/email-marketing/campaigns/save-draft/', data, ajaxResponse );
    });

    // Send Test Email
    $('.send-test').click(function(e) {
        e.preventDefault();

        // Get form data
        var data = get_form_data();
        data._nonce = $('#_send_test').val();
        data.email = $('#test-destination').val();

        // Post!
        $.post( '/email-marketing/campaigns/send-test/', data, ajaxResponse );
    });

    // Save Campaign
    $('.save-campaign').click(function(e) {
        e.preventDefault();

        if ($(this).hasClass('disabled'))
            return;

        $(this).addClass('disabled').text('Saving...');

        // Get form data
        var data = get_form_data();
        data._nonce = $('#_save_campaign').val();

        // Post!
        $.post( '/email-marketing/campaigns/save-campaign/', data, function(r) {
            if ( r.notification )
                ajaxResponse(r);
            else
                window.location = '/email-marketing/campaigns/';

        } );
    });

});
