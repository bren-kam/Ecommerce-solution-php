/**
 * Object to Product Image Uploader
 */
var ProductImageUploader = {

    /**
     * The File Uploader
     */
    uploader: null

    /**
     * New Image Template
     */
    , template: null

    /**
     * Setup events
     */
    , init: function() {
        // Setup File Uploader
        ProductImageUploader.uploader = new qq.FileUploader({
            action: '/products/product-builder/upload-image/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#upload-image')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: ProductImageUploader.submit
            , onComplete: ProductImageUploader.complete
        });

        // Upload file trigger
        $('#aUpload').click( ProductImageUploader.open );
        $('body').on('click', '.remove-image', ProductImageUploader.remove);

        // Get the image template
        ProductImageUploader.template = $('#image-template').clone();
        ProductImageUploader.template.removeAttr('id');
    }

    /**
     * Submit - triggered when a file is selected to upload
     *
     * @param id
     * @param fileName
     */
    , submit: function( id, fileName ) {
        ProductImageUploader.uploader.setParams({
            _nonce : $('#_upload_image').val()
            , iid : $('#sIndustry').val()
            , pid : ProductForm.getProductId()
        });

        $('#aUpload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    /**
     * Complete - handles upload response
     * @param id
     * @param fileName
     * @param responseJSON
     */
    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#aUpload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            // Clone image template
            ProductImageUploader.template.clone()
                .find('a:first')
                .attr('href', response.image_url.replace('/small/', '/large/'))
                .find('img:first')
                .attr('src', response.image_url)
                .parents('.image:first')
                .find('input:first')
                .val(response.image_name)
                .parent()
                .appendTo('#images-list');
        }
    }

    /**
     * Opens uploader
     * @param e
     */
    , open: function(e) {
        if ( e )
            e.preventDefault();

        if( $('#sIndustry').val() == '' ) {
            alert( 'You must select an Industry before uploading an image' );
            return;
        }

        // Ensure we have a product id
        if ( ProductForm.getProductId() == '' )
            ProductForm.createProduct();

        if ( $.support.cors ) {
            $('#upload-image input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

    /**
     * Removes an image
     * @param e
     */
    , remove: function(e) {
        if ( e ) e.preventDefault();

        if ( confirm( 'Are you sure you want to delete this image? It cannot be undone.' ) ) {
            $(this).parents('p.image').remove();
        }
    }

}

var ProductSpecEditor = {

    template: null

    /**
     * Setup events
     */
    , init: function() {

        // Get Template
        ProductSpecEditor.template = $('#product-spec-template').clone();
        ProductSpecEditor.template.removeAttr('id');

        // Bind Events
        $('#add-product-spec').click( ProductSpecEditor.add );
        $('body').on('click', '.remove-spec', ProductSpecEditor.remove );

    }

    /**
     * Adds a Product Specification
     * @param e
     */
    , add: function(e) {
        e.preventDefault();

        var tAddSpecName = $('#tAddSpecName');
        var specName = $.trim(tAddSpecName.val()).replace( /[|`]/g, '');

        var taAddSpecValue = $('#taAddSpecValue');
        var specValue = $.trim(taAddSpecValue.val()).replace( /[|`]/g, '');

        var productSpecsList = $('#product-specs-list');

        //if ( specName == '' )
        //    return;
        //
        var values = specValue.split( /\n/ );

        for ( var i in values ) {
            specValue = values[i].trim();

            var newProductSpec = ProductSpecEditor.template.clone();

            newProductSpec
                .find('span.specification-name').text( specName ).end()
                .find('span.specification-value').text( specValue ).end()
                .find('input:first').val( specName + '|' + specValue );

            productSpecsList.append( newProductSpec );
        }

        // Reset values
        $('#tAddSpecName, #taAddSpecValue').val('').trigger('blur');
    }

    /**
     * Removes a Product Specification
     * @param e
     */
    , remove: function(e) {
        e.preventDefault();

        $(this).parents('.product-spec').remove();
    }

}

var ProductTagEditor = {

    template: null

    /**
     * Setup Events
     */
    , init: function() {

        // Get Template
        ProductTagEditor.template = $('#product-tag-template').clone();
        ProductTagEditor.template.removeAttr('id');

        // Bind Events
        $('#add-product-tag').click( ProductTagEditor.add );
        $('body').on('click', '.remove-tag', ProductTagEditor.remove );

    }

    /**
     * Add a Product Tag
     * @param e
     */
    , add: function(e) {
        e.preventDefault();

        var tTag = $('#tTag');
        var tag = $.trim(tTag.val()).replace( /[|`]/g, '');

        var productTagsList = $('#product-tags-list');

        if ( tag == '' )
            return;

        var newProductTag = ProductTagEditor.template.clone();

        newProductTag
            .prepend( tag )
            .find('input:first').val( tag );

        productTagsList.append( newProductTag );

        // Reset values
        $('#tTag').val('').trigger('blur');
    }

    /**
     * Removes a Product Tag
     * @param e
     */
    , remove: function(e) {
        e.preventDefault();

        $(this).parents('.product-tag').remove();
    }

}

var ProductAttributeEditor = {

    template: null

    /**
     * Setup events
     */
    , init: function() {

        // Get Template
        ProductAttributeEditor.template = $('#attribute-template').clone();
        ProductAttributeEditor.template.removeAttr('id');

        // Bind Events
        $('#add-attribute').click( ProductAttributeEditor.add );
        $('body').on('click', '.remove-attribute', ProductAttributeEditor.remove );

        // Get attributes based on it's category
        $('#sCategory').change( ProductAttributeEditor.getAttributes );

        // Get Attributes for the first time
        ProductAttributeEditor.getAttributes();
    }

    /**
     * Adds a Product Attribute
     * @param e
     */
    , add: function(e) {
        e.preventDefault();

        var attributeItemsList = $('#attribute-items-list')
        var sAttributes = $('#sAttributes');

        sAttributes.find('option:selected').each( function() {
            var option = $(this)
            var attributeItemId = option.val();

            // Make sure they actually put something in
            if ( attributeItemId == '' )
                return;

            var newAttributeItem = ProductAttributeEditor.template.clone();

            newAttributeItem
                .find('strong:first')
                .prepend( option.parents('optgroup:first').attr('label') )
                .after( option.text() )
                .end()
                .find('input:first')
                .val( attributeItemId );

            attributeItemsList.append( newAttributeItem );

            // Deselect the option
            option
                .attr('disabled', true)
                .prop('selected', false);
        });

    }

    /**
     * Removes a Product Attribute
     * @param e
     */
    , remove: function(e) {
        e.preventDefault();

        var attributeItemId = $(this).siblings('input').val();

        // Remove parent
        $(this).parent().remove();

        // Enable item in drop down
        $('#sAttributes option[value=' + attributeItemId + ']').removeAttr('disabled');
    }

    /**
     * Loads Product Attributes based of Selected Category
     * @param e
     */
    , getAttributes: function(e) {
        if ( e )
            e.preventDefault();

        var categoryId = parseInt( $('#sCategory').val() );

        if ( categoryId <= 0 )
            return;

        // Load attribute items
        $.post(
            '/products/product-builder/get-attribute-items/'
            ,{ _nonce: $('#_get_attribute_items').val(), cid : categoryId }
            , ProductAttributeEditor._loadAttributes
        );
    }

    /**
     * Handles /products/product-builder/get-attribute-items/ callback
     * @param response
     */
    , _loadAttributes: function(response) {
        if ( response.success ) {

            $('#sAttributes').empty();

            for ( attribute_name in response.attributes ) {

                var attribute_items = response.attributes[attribute_name];

                var optgroup = $('<optgroup />', { label: attribute_name} );

                for ( i in attribute_items ) {
                    var attribute_item = attribute_items[i];
                    var option = $( '<option />', { value: attribute_item.id } ).text( attribute_item.name );
                    optgroup.append( option );
                }

                $('#sAttributes').append( optgroup );
            }

        }
    }

}


var ProductForm = {

    /**
     * Setup Events
     */
    init: function() {

        // Setup datepicker
        $('#tPublishDate').datepicker().on('changeDate', function(e) {
            $('#hPublishDate').val( e.date.toISOString().slice(0, 10) );
        });

        // If it's a new product form, create product id after setting name
        $('#tName').change( ProductForm.setProductSlug );

        // Make Images sortable
        $('#images-list').sortable({
            forcePlaceholderSize : true
            , placeholder: 'image-placeholder'
        });

        ProductForm.setupValidation();

    }

    /**
     * Get Product Id
     * @returns {string}
     */
    , getProductId: function() {
        return $('#hProductId').val();
    }

    /**
     * Set product slug from it's name
     * @param e
     */
    , setProductSlug: function(e) {
        if ( e ) e.preventDefault();

        // Get product name
        var productName = $('#tName').val();

        // Get current slug
        var tProductSlug = $('#tProductSlug');

        // Set slug if empty
        if ( tProductSlug.val() == '' )
            tProductSlug.val( productName.slug() );
    }

    /**
     * Create products
     * @param e
     */
    , createProduct: function(e) {
        if ( e ) e.preventDefault();

        // Prevent if we already have a Product ID
        if ( ProductForm.getProductId() != '' )
            return;

        // Create the product ID
        $.post(
            '/products/product-builder/create/'
            , { _nonce : $('#_create_product').val() }
            , ProductForm._setProductId
        );
    }

    /**
     * Sets product id, /products/product-builder/create/ callback
     * @param response
     */
    , _setProductId: function(response) {
        GSR.defaultAjaxResponse( response );
        if ( response.product_id ) {
            $('#hProductId').val( response.product_id );
            $('#fAddEditProduct').attr( 'action', '?pid=' + response.product_id );
        }
    }

    /**
     * Generates a Slug from a String
     * @param str
     * @returns {string}
     */
    , getSlug: function(str) {
        return str
                .replace(/^\s+|\s+$/g,"")
                .replace( /[^-a-zA-Z0-9\s]/g, '' )
                .replace( /[\s]/g, '-' )
                .toLowerCase();
    }

    , setupValidation: function() {
        $('#fAddEditProduct').bootstrapValidator({
            fields: {
                tName: {
                    validators: {
                        notEmpty: {
                            message: 'A "Name" is required'
                        }
                    }
                }
                , sProductStatus: {
                    validators: {
                        notEmpty: {
                            message: 'A "Status" is required'
                        }
                    }
                }
                , tSKU: {
                    validators: {
                        notEmpty: {
                            message: 'A "SKU" is required'
                        }
                    }
                }
                , sBrand: {
                    validators: {
                        notEmpty: {
                            message: 'A "Brand" is required'
                        }
                    }
                }
                , sIndustry: {
                    validators: {
                        notEmpty: {
                            message: 'A "Industry" is required'
                        }
                    }
                }
                , sCategory: {
                    validators: {
                        notEmpty: {
                            message: 'A "Category" is required'
                        }
                    }
                }
            }
        });
    }
}

// Show warning for ashley feeds
var show_ashley_warning = function(){
    var warningModal = $('#warningModal');

    if (warningModal.length){
        warningModal.modal('show');
    }
};

/**
 * Initialize
 */
jQuery(function(){
    ProductImageUploader.init();
    ProductSpecEditor.init();
    ProductTagEditor.init();
    ProductAttributeEditor.init();
    ProductForm.init();

});

