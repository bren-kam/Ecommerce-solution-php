var BrandEdit = {

    /**
     * Product Option Template
     */
    template: null

    , init: function() {

        // Get Product Option Template
        BrandEdit.template = $('#product-option-template').clone()
            .removeClass('hidden');
        $('#product-option-template').remove();

        // Product Option events
        $('#sProductOptions').change( BrandEdit.addProductOption );
        $('body').on( 'click', '.delete-product-option', BrandEdit.deleteProductOption );

        // Set slug based on name
        $('#tName').change( function() {
            $('#tSlug').val( $(this).val().slug() );
        });

    }

    , addProductOption: function() {
        var productOption = $(this).find('option:selected')
        var productOptionId = $(this).val();

        if ( productOptionId == '' )
            return;

        var item = BrandEdit.template.clone();
        item.find('span:first').text( productOption.text() );
        item.find('input:first').val( productOptionId );
        item.appendTo('#product-option-list');

        $('#sProductOptions option[value=' + productOptionId + ']').prop( 'disabled', true );
    }

    , deleteProductOption: function() {
        var productOption = $(this).parents('p:first');
        var productOptionId = productOption.find('input:first').val();

        $('#sProductOptions option[value=' + productOptionId + ']').prop( 'disabled', false );

        productOption.remove();
    }

}

jQuery( BrandEdit.init );
