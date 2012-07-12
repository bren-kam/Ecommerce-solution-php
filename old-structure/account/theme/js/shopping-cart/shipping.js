/**
 * Shipping - List
 */

// var editorHTML = CKEDITOR.instances.taDescription.getData();

// When the page has loaded
jQuery( postLoad );

/**
 * postLoad
 *
 * Initial load of the page
 *
 * @param $ (jQuery shortcut)
 */
function postLoad( $ ) {
	// Enable free shipping
	$('#cbEnableFreeShipping').live( 'click', setFreeShipping );
	
	// Chaning shipping quantity
	$('#tFreeShippingQuantity').live( 'change', setFreeShipping );
}

function setFreeShipping() {
	$.post( '/ajax/shopping-cart/shipping/set-free/', { _nonce: $('#free_shipping_nonce').val(), c : $("#cbEnableFreeShipping").attr('checked'), q : $("#tFreeShippingQuantity").val() }, ajaxResponse, 'json' );
}