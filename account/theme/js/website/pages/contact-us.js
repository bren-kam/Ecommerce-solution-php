jQuery(function(){
	storedAddresses = new Array();
	
	// Make it serialize addresses properly
	$("#fEditPage").submit( updateAddresses() );
	
	// Add Address functionality
	$('#aAddLocation').click( function() {
		if ( !validateAddress( $('#tPhone'), $('#tFax'), $('#tEmail'), $('#tWebsite'), $('#tZip') ) )
			return false;
		
		// Add on to the list
		$('#dContactUsList').append( '<div class="contact" id="dContact' + generateID() + '"><h2><span class="location">' + $('#tLocation').val() + '</span></h2><div class="contact-left"><span class="address">' + $('#tAddress').val() + '</span><br /><span class="city">' + $('#tCity').val() + '</span>, <span class="state">' + $('#sState option:selected').val() + '</span> <span class="zip">' + $('#tZip').val() + '</span></div><div class="contact-right"><span class="phone">' + $('#tPhone').val() + '</span><br /><span class="fax">' + $('#tFax').val() + '</span><br /></div><div style="float:right"><span class="email">' + $('#tEmail').val() + '</span><br /><span class="website">' + $('#tWebsite').val() + '</span></div><br /><br clear="all" /><br /><strong>Store Hours:</strong><br /><span class="store-hours">' + nl2br( $('#taStoreHours').val() ) + '</span><div class="actions"><a href="javascript:;" class="delete-address" title="Delete Address"><img src="/images/icons/x.png" width="15" height="17" alt="Delete Address" /></a><a href="javascript:;" class="edit-address" title="Edit Address"><img src="/images/icons/edit.png" width="15" height="17" alt="Edit Address" /></a></div></div>' );
		
		// Reset the form
		$('#tLocation, #tAddress, #tCity, #tZip, #tPhone, #tFax, #tEmail, #tWebsite, #taStoreHours').val('');
		$('#sState option:first').attr( 'selected', true );
		
		// Update the addresses
		updateAddresses();
	});
	
	// Edit Address funtionality
	$('.edit-address').live( 'click', function() {
		var a = $(this), parent = a.parents('.contact:first');
		$('#tEditLocation').val( parent.find('.location').text() );
		$('#tEditPhone').val( parent.find('.phone').text() );
		$('#tEditFax').val( parent.find('.fax').text() );
		$('#tEditEmail').val( parent.find('.email').text() );
		$('#tEditWebsite').val( parent.find('.website').text() );
		$('#tEditAddress').val( parent.find('.address').text() );
		$('#tEditCity').val( parent.find('.city').text() );
		$('#sEditState option[value=' + parent.find('.state').text() + ']').attr( 'selected', true );
		$('#tEditZip').val( parent.find('.zip').text() );
		$('#taEditStoreHours').val( parent.find('.store-hours').text().replace( '<br />', '\n' ) );
		$('#hContactID').val( parent.attr('id').replace( 'dContact', '' ) );
		
		head.js( '/js2/?f=jquery.boxy', function() {
			new Boxy( $('#dEditAddress'), {
				title : a.attr('title')
			});
		});
	});
	
	// Save Address functionality
	$('#aSaveAddress').live( 'click', function() {
		if ( !validateAddress( $('#tEditPhone'), $('#tEditFax'), $('#tEditEmail'), $('#tEditWebsite'), $('#tEditZip') ) )
				return false;
		
		var contact = $('#dContact' + $('#hContactID').val() );
		
		contact.find('.location').text( $('#tEditLocation').val() );
		contact.find('.phone').text( $('#tEditPhone').val() );
		contact.find('.fax').text( $('#tEditFax').val() );
		contact.find('.email').text( $('#tEditEmail').val() );
		contact.find('.website').text( $('#tEditWebsite').val() );
		contact.find('.address').text( $('#tEditAddress').val() );
		contact.find('.city').text( $('#tEditCity').val() );
		contact.find('.state').text( $('#sEditState option:selected').val() );
		contact.find('.zip').text( $('#tEditZip').val() );
		contact.find('.store-hours').html( nl2br( $('#taEditStoreHours').val() ) );
		
		// Hide the box
		Boxy.get(this).hide();
		
		updateAddresses();
		
		// Reset the form
		$('#tEditLocation, #tEditAddress, #tEditCity, #tEditZip, #tEditPhone, #tEditFax, #tEditEmail, #tEditWebsite, #taEditStoreHours').val('');
		$('#sEditState option:first').attr( 'selected', true );
	});
	
	// Delete Address functionality
	$('.delete-address').live( 'click', function() {
		if ( confirm( 'Are you sure you want to delete this location?' ) ) {
			// Remove self
			$(this).parents('.contact:first').remove();
			
			updateAddresses();
		}
	});
	
	// Multiple Location Map toggle
	$('#cbMultipleLocationMap').click( function() {
		$.post( '/ajax/website/page/set-pagemeta/', { _nonce: $('#_ajax_set_pagemeta').val(), k : 'mlm', v : $(this).is(':checked') ? true : false, wpid : $('#hWebsitePageID').val() }, ajaxResponse, 'json' );
	});
	
	// Hide All Maps toggle
	$('#cbHideAllMaps').click( function() {
		$.post( '/ajax/website/page/set-pagemeta/', { _nonce: $('#_ajax_set_pagemeta').val(), k : 'ham', v : $(this).is(':checked') ? true : false, wpid : $('#hWebsitePageID').val() }, ajaxResponse, 'json' );
	});
});

// Validation
function validateAddress( phone, fax, email, website, zip ) {
	var phoneVal = phone.val();
	
	// if ( phoneVal.search(/[^0-9\- ()]/) >= 0 || ( phoneVal.length < 10 && phoneVal.length > 0 ) ) {
	// Redid the regex to accept Honduras( and, possibly, other international phone#s )
	if ( phoneVal.search(/[^0-9\- ()]/) >= 0 || ( phoneVal.length < 9 && phoneVal.length > 0 ) || ( phoneVal.length > 21 ) ) {
		alert( 'The "Phone" field must contain a valid phone number' );
		phone.focus();
		return false;
	}
	
	var faxVal = fax.val();
	// if ( faxVal.search(/[^0-9\- ()]/) >= 0 || ( faxVal.length < 10 && faxVal.length > 0 ) ) {
	if ( faxVal.search(/[^0-9\- ()]/) >= 0 || ( faxVal.length < 9 && faxVal.length > 0 ) || ( faxVal.length > 21 ) ) {
		alert( 'The "Fax" field must contain a valid fax number' );
		fax.focus();
		return false;
	}
	
	var emailVal = email.val();
	if ( emailVal.length > 0 && null == emailVal.match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/) ) {
		alert( 'The "Email" field must contain a valid email address' );
		email.focus();
		return false;
	}
	
	var websiteVal = website.val();
	if ( websiteVal.length > 0 && null == websiteVal.match(/(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/) ) {
		alert( 'The "Website" field must contain a valid link' );
		website.focus();
		return false;
	}
	
	var zipVal = zip.val();
	if ( zipVal.search(/[^-0-9]/) >= 0 || ( zipVal.length < 5 && zipVal.length > 0 ) ) {
		//alert( 'The "Zip" field must contain a valid zip code' );
		a = confirm( 'The ZIP you entered is not a standard US Zip code.  Is this intentional?' );
		if (a == false){
			zip.focus();
			return false;
		}
	}
	
	return true;
}

/**
 * Generates a random number
 */
function generateID() {
	var newID = 10 + Math.floor(Math.random()*1001);
	
	if ( '' == $('#dContact' + newID).text() )
		return newID;
}

/**
 * Updates a hidden field with a serialized array of address
 */
function updateAddresses() {
	addresses = new Array();
	$('.contact').each( function() {
		addresses.push({
			'location' : $(this).find('.location').text(),
			'phone' : $.trim( $(this).find('.phone').text() ),
			'fax' : $(this).find('.fax').text(),
			'email' : $(this).find('.email').text(),
			'website' : $(this).find('.website').text(),
			'store-hours' : $(this).find('.store-hours').html().replace( /[\n\r\t]/g, '' ),
			'address' : $(this).find('.address').text(),
			'city' : $(this).find('.city').text(),
			'state' : $(this).find('.state').text(),
			'zip' : $(this).find('.zip').text()
		});
	});
	
	$('#hAddresses').val( serialize( addresses ) );
}

function serialize (mixed_value) {
    // http://kevin.vanzonneveld.net
    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: utf8_encode
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'

    var _getType = function (inp) {
        var type = typeof inp, match;
        var key;
        if (type == 'object' && !inp) {
            return 'null';
        }
        if (type == "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);
            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';
    
    switch (type) {
        case "function": 
            val = ""; 
            break;
        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
            break;
        case "string":
            mixed_value = this.utf8_encode(mixed_value);
            val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
            break;
        case "array":
        case "object":
            val = "a";
            /*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                if (objname == undefined) {
                    return;
                }
                objname[1] = this.serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);
            }
            */
            var count = 0;
            var vals = "";
            var okey;
            var key;
            for (key in mixed_value) {
                ktype = _getType(mixed_value[key]);
                if (ktype == "function") { 
                    continue; 
                }
                
                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                vals += this.serialize(okey) +
                        this.serialize(mixed_value[key]);
                count++;
            }
            val += ":" + count + ":{" + vals + "}";
            break;
        case "undefined": // Fall-through
        default: // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
            val = "N";
            break;
    }
    if (type != "object" && type != "array") {
        val += ";";
    }
    return val;
}

function utf8_encode ( argString ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'

    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    var utftext = "";
    var start, end;
    var stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }

    if (end > start) {
        utftext += string.substring(start, string.length);
    }

    return utftext;
}

function nl2br (str, is_xhtml) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Philip Peterson
    // +   improved by: Onno Marsman
    // +   improved by: Atli Þór
    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Maximusya
    // *     example 1: nl2br('Kevin\nvan\nZonneveld');
    // *     returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
    // *     example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
    // *     returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
    // *     example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
    // *     returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'

    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}