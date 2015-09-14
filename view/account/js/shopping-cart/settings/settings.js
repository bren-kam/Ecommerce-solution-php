jQuery(document).ready(function(){
    var paypal_username = jQuery('#tPaypalExpressUsername').val();
    var paypal_password = jQuery('#tPaypalExpressPassword').val();
    var paypal_signature = jQuery('#tPaypalExpressSignature').val();
    var nonce = jQuery('#modal-paypal input[name=_nonce]').val();

    console.log(nonce);
    $.ajax({
	url: '/shopping-cart/settings/test-paypal/?_nonce=' + nonce,
	success: function(response){
	    if(!response.success) {
		$("#paypal-status img").attr("src", "/images/payment-logos/error.png");
		$("#paypal-status strong").text("Problem in connection");
	    }
	}
    });
});
