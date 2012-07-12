/**
 * User
 */
jQuery(function($) {
	// Password Strength Meter
	$('#tPassword').keyup( function() {
		var passwordValue = $(this).val(), emailValue = $('#tEmail').val(), percent = passwordStrengthPercent( passwordValue, emailValue );
		
		$('#sPSResult').html( passwordStrength( passwordValue, emailValue ) );
		$('#dPSColorBar').css( { backgroundPosition: '0px -' + percent + 'px', width: percent * 2.67 + 'px' } );
	    $('#sPSPercent').html( ' ' + percent  + '% ' );
	})
});