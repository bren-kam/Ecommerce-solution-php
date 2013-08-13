/*
  -------------------------------------------------------------------------
	                    Javascript Form Validator 
                                Version 1.1
	Based off the "JavaScript Form Validator Version 2.0.2" which can be 
	found at JavaScript-coder.com.
	
	Recreation by Phoenix Development February 27, 2008.
	
	Works in collaboration with PHP class "class.validator.php".
    -------------------------------------------------------------------------  
*/

/* Luhn algorithm number checker - (c) 2005-2009 - planzero.org            *
 * This code has been released into the public domain, however please      *
 * give credit to the original author where possible.                      */
function luhn_check( number ) {
	// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
	var ccnumber = number.replace( /\D/g, '' );

	// Set the string length and parity
	var number_length = ccnumber.length;
	var parity = number_length % 2;

	// Loop through each digit and do the maths
	var total = 0;
	for ( var i = 0; i < number_length; i++ ) {
		var digit = number.charAt(i);

		// Multiply alternate digits by two
		if (i % 2 == parity) {
			digit = digit * 2;

			// If the sum is two digits, add them together (in effect)
			if ( digit > 9 )
			digit = digit - 9;
		}

		// Total up the digits
		total = total + parseInt( digit, 10 );
	}

	// If the total mod 10 equals 0, the number is valid
    return (total % 10 == 0) ? true : false;
}

// From PHP.JS
function in_array(needle,haystack,argStrict){var found=false,key,strict=!!argStrict;for (key in haystack){if ((strict&&haystack[key]===needle)||(!strict&&haystack[key]==needle)){found=true;break;}}
return found;}

function Validator(fName) {
	this.fObj = document.forms[fName];
	
	// Makes the the form able to be trigger by the .trigger funciton
	var trigger = (arguments.length > 0) ? arguments[1] : false;
	
	// Make sure the form is real, if not, show an error
	if (!this.fObj) {
	  	console.error("Error: could not get form object " + fName);
		return false;
	}

	if (this.fObj.onsubmit) {
		this.fObj.old_onsubmit = this.fObj.onsubmit;
		this.fObj.onsubmit = null;
	} else {
		this.fObj.old_onsubmit = null;
	}
	
	this.vElements = new Array();
	
	if (trigger) {
		this.fObj.trigger = form_submit_handler;
	} else {
		this.fObj.onsubmit = form_submit_handler;
	}
	
	// Assign differention functions
	this.addValidation = add_validation;
	this.removeValidation = remove_validation;
	this.clearAllValidations = clear_all_validations;
	this.clearValidations = clear_validations;
	this.restoreValidations = restore_validations;

	return true;
}

function clear_all_validations() {
	for (var i = 0; i < this.fObj.elements.length; i++) {
		this.fObj.elements[i].vSet = null;
	}
}

function clear_validations(elementArray) {
	start = (arguments[1]) ? arguments[1] : 0;
	for (var i = start; i < this.fObj.elements.length; i++) {
		if (this.fObj.elements[i].val) {
			if (in_array(this.fObj.elements[i].val.vSet[0][0], elementArray)) {
				this.fObj.elements[i].val.oldSet = this.fObj.elements[i].val.vSet;
				this.fObj.elements[i].val.vSet = null;
			}
		}
	}
}

function restore_validations(elementArray) {
	start = (arguments[1]) ? arguments[1] : 0;
	for (var i = start; i < this.fObj.elements.length; i++) {
		if (this.fObj.elements[i].val) {
			if (this.fObj.elements[i].val.oldSet) {
				this.fObj.elements[i].val.vSet = this.fObj.elements[i].val.oldSet;
			}
		}
	}
}

function form_submit_handler() {
	for (var i = 0; i < this.elements.length; i++) {
		if (this.elements[i].val && this.elements[i].val.vSet && !this.elements[i].val.validate(this)) {
			return false;
		}
	}
	return true;
}

function remove_validation( elementName ) {
	var elementObj = this.fObj[elementName];
	
	if ( elementObj ) {
		if ( elementObj.val ) {
			elementObj.val.oldSet = elementObj.val.vSet;
			elementObj.val.vSet = null;
		}
	}
}

function add_validation(elementName, desc, err) {
	if (!this.fObj) {
		console.error("Error: the form object is not set properly");
		return;
	}
	
	var elementObj = this.fObj[elementName];

	if (!elementObj) {
		elementArray = elementName.split("|");
		if (elementArray.length > 1) {
			if (!this.fObj[elementArray[0]] || !this.fObj[elementArray[1]]) {
				console.error("Error: Could not get the input objects");
				return;
			} else {
				if (!this.fObj[elementArray[0]].val) {
					this.fObj[elementArray[0]].val = new Val();
					this.fObj[elementArray[0]].val.add(elementName, desc, err);
				} else {
					this.fObj[elementArray[0]].val.add(elementName, desc, err);
				}
			}
		} else {
			console.error("Error: Could not get the input object named: " + elementName);
			return;
		}
	} else {
		if (elementObj.val) {
			if (elementObj.val.oldSet) {
				elementObj.val.vSet = elementObj.val.oldSet;
			} else {
				elementObj.val.add(elementName, desc, err);
			}
		} else {
			elementObj.val = new Val();
			elementObj.val.add(elementName, desc, err);
		}
	}
}

function Val(elementName, desc, err) {
	this.vSet = new Array();
	this.add = addVal;
	this.validate = validate;
}

function addVal(elementName, desc, error) {
	this.vSet[this.vSet.length] = new Array(elementName, desc, error);
}

function validate(fObj) {
	for (i = 0; i < this.vSet.length; i++)
	{
		if (!checkValidation(this.vSet[i][0], this.vSet[i][1], this.vSet[i][2], fObj)) {
			elementArray = this.vSet[i][0].split("|");
			fObj[elementArray[0]].focus();
			return false;
		}
	}
	return true;
}

function checkValidation(elementName, descriptor, err, fObj) {
	var tmpArray = descriptor.split("=");
	command = tmpArray[0]; 
	cmdvalue = (tmpArray.length > 1) ? tmpArray[1] : '';
	
	var patterns = {
		'alnum': /[^A-Za-z0-9\ ]/,
		'alnumhyphen': /[^A-Za-z0-9\-_]/,
		'alpha': /[^A-Za-z]/,
		'cc': /^(3[47]|4|5[1-5]|6011)/,
		'csv': /[^-a-zA-Z0-9,\s]/,
		'custom': /[^A-Za-z0-9\042\047\055\057\ _$.,!?()]/,
		'date': /^[\d]{4}-[\d]{2}-[\d]{2}$/,
		'email': /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
		'float': /[^0-9\.]/,
		'img': /^[0-9A-Za-z_ \-]+(.[jJ][pP][gG]|.[jJ][pP][eE][gG]|.[gG][iI][fF]|.[pP][nN][gG])$/,
		'ip': /^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/,
		'num': /[^0-9]/,
		'phone': /[^0-9\- ()]/,
		'URL': /(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/,
		'zip': /[^-0-9]/
	}
	
	switch (command) 
	{
		case "alnum": 
		case "alphanumeric": 
			var charpos = fObj[elementName].value.search(patterns['alnum']);
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) {
				if (!err || err.length == 0) err = elementName + " must be alpha-numeric";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]");
				return false;
			}
		break;

		case "alnumhyphen":
			var charpos = fObj[elementName].value.search(patterns['alnumhyphen']); 
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) { 
				if (!err || err.length == 0) err = elementName + " may only contain alpha-numeric, hyphen and underscore characters";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]"); 
				return false;
			}
		break;

		case "alphabetic": 
		case "alpha": 
			var charpos = fObj[elementName].value.search(patterns['alpha']); 
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) {
				if (!err || err.length == 0) err = elementName + " may only contain letters (no symbols or numbers)";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]");
				return false;
			}
			break;

		case "cc":
		case "credit":
		case "creditcard":
			// Error message
			var ccError = 'The credit card number you entered is not valid. Please try again.';

			// Get object value
			var objValue = fObj[elementName].value.replace( /\D/g, '' ) ;

			// Get the length of the object
			var objLength = objValue.length;

			// Get the first number
			var firstNum = objValue[0];

			// Make sure it's  valid credit card type
			if ( null != patterns['cc'].exec( objValue ) ) {
				// Add the specific validation for the other types
				switch ( firstNum ) {
					// Visa
					case '4':
						if ( 13 != objLength  && 16 != objLength ) {
							// Return false and show them an error
							alert( ccError );
							return false;
						} else {
							// Set the type to select it in the credit card drop down
							var cardType = 1;
						}
						break;

					// MasterCard
					case '5':
						if ( 16 != objLength ) {
							// Return false and show them an error
							alert( ccError );
							return false;
						} else {
							// Set the type to select it in the credit card drop down
							var cardType = 2;
						}
						break;

					// Discover
					case '6':
						if ( 16 != objLength ) {
							// Return false and show them an error
							alert( ccError );
							return false;
						} else {
							// Set the type to select it in the credit card drop down
							var cardType = 3;
						}
						break;

					// AmEx
					case '3':
						if ( 15 != objLength ) {
							// Return false and show them an error
							alert( ccError );
							return false;
						} else {
							// Set the type to select it in the credit card drop down
							var cardType = 4;
						}
						break;
				}

				// Do the Mod 10 (Luhn Check), if valid, select the right credit card type
				if ( !luhn_check( objValue ) ) {
					alert( ccError );
					return false;
				} else {
					// Make sure there is a card type
					if ( typeof cardType != 'undefined' )
						document.getElementById('sCCType').options[cardType].selected = true;
				}
			} else {
				alert( ccError );
				return false;
			}
		break;

		case "csv":
			var charpos = fObj[elementName].value.search( patterns['csv'] );
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) {
				if (!err || err.length == 0) err = elementName + " may only contain alpha-numeric characters, '-' and '_', separated by commas";
				alert( err );
				return false;
			}
		break;

		case "custom":
			var charpos = fObj[elementName].value.search(patterns['custom']);
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) {
				if (!err || err.length == 0) err = elementName + " may only contain alpha-numeric, {'}, {,}, {.}, {&}, {/}, {-}, {\"}, {?}, {(}, {)}, {_} and {!} characters";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]");
				return false;
			}
		break;

		case "date": 
			if (fObj[elementName].value.length > 0 && fObj[elementName].value != fObj[elementName].getAttribute('tmpval')) {
				var charpos = fObj[elementName].value.match(patterns['date']);
				if (charpos == null) {
					if (!err || err.length == 0) err = elementName + " must contain a valid date"; 
					alert(err); 
					return false;
				}
			}
		break;
		
		case "email": 
			if (fObj[elementName].value.length > 0) {
				var charpos = fObj[elementName].value.match(patterns['email']);
				if (charpos == null) {
					if (!err || err.length == 0) err = elementName + " must contain a valid email address"; 
					alert(err); 
					return false;
				}
			}
		break;

		case "extension":
		case "ext":
			var accept = cmdvalue.split("|");
			var ext = fObj[elementName].value.split(".")[fObj[elementName].value.split(".").length-1].toLowerCase();
			if (accept.indexOf(ext) == -1) {
				if (!err || err.length == 0) err = elementName + " may only contain the following file types: " + accept;
				alert(err + "\n[Current file type: \"" + ext + "\" ]");
				return false;
			}
			break; 

		case "float": 
			var charpos = fObj[elementName].value.search(patterns['float']); 
			if (fObj[elementName].value.length > 0 &&  charpos >= 0 && fObj[elementName].value != fObj[elementName].getAttribute('tmpval')) { 
				if (!err || err.length == 0) err = elementName + " may only contain numbers and a period"; 
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]"); 
				return false; 
			}
		break;               

		case "gt":
		case "greaterthan":
			if (isNaN(fObj[elementName].value)) { 
				alert(elementName + " must be numeric"); 
				return false; 
			}
			if (eval(fObj[elementName].value) <= eval(cmdvalue)) {
				if (!err || err.length == 0) err = elementName + " must contain a number greater than " + cmdvalue;
				alert(err);
				return false;
			}
		break;

		case "image":
		case "img":
			var imgfile = fObj[elementName].value.split("\\")[fObj[elementName].value.split("\\").length-1];
			var charpos = imgfile.match(patterns['img']);
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) { 
				if (!err || err.length == 0) err = elementName + " may only hold an image with extensions jpg, jpeg, gif or png.";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]"); 
				return false;
			}
		break;

        case "ip":
			var charpos = fObj[elementName].value.search(patterns['ip']);
			if (fObj[elementName].value.length > 0 && charpos >= 0 && fObj[elementName].value != fObj[elementName].getAttribute('tmpval')) {
				if (!err || err.length == 0) err = elementName + " must be a number";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]");
				return false;
			}
		break;

		case "lt":
		case "lessthan":
			if (isNaN(fObj[elementName].value)) {
				alert(elementName + " must be numeric");
				return false;
			}
			if (eval(fObj[elementName].value) >= eval(cmdvalue)) {
				if (!err || err.length == 0) err = elementName + " must contain a number less than "+ cmdvalue;
				alert(err);
				return false;
			}
		break;

		case "match":
			var tmpElements = elementName.split("|");
			if (fObj[tmpElements[0]].value != fObj[tmpElements[1]].value) {
				if (!err || err.length == 0) err = tmpElements[0] + " and " + tmpElements[1] + " must match";
				alert(err);
				return false;
			}
		break;

		case "maxlength": 
		case "maxlen":
			if (eval(fObj[elementName].value.length) > eval(cmdvalue)) {
				if (!err || err.length == 0) err = elementName + " may not be longer than " + cmdvalue + " characters";
				alert(err + "\n[Current length = " + fObj[elementName].value.length + " ]");
				return false;
			}
		break;

		case "minlength": 
		case "minlen": 
			if (eval(fObj[elementName].value.length) < eval(cmdvalue)) {
				if (!err || err.length == 0) err = elementName + " may not be shorter than " + cmdvalue + " characters";
				alert(err + "\n[Current length = " + fObj[elementName].value.length + " ]");
				return false;                 
			} 
		break;

		case "num": 
		case "numeric": 
			var charpos = fObj[elementName].value.search(patterns['num']); 
			if (fObj[elementName].value.length > 0 && charpos >= 0 && fObj[elementName].value != fObj[elementName].getAttribute('tmpval')) { 
				if (!err || err.length == 0) err = elementName + " must be a number"; 
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]"); 
				return false; 
			} 
		break;

		case "phone":
			var charpos = fObj[elementName].value.search(patterns['phone']); 
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) { 
				if (!err || err.length == 0) err = elementName + " must contain a valid phone number";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]"); 
				return false;
			} 			
			break;

		case "regexp": 
			if (fObj[elementName].value.length > 0) {
				if (!fObj[elementName].value.match(cmdvalue)) { 
					if (!err || err.length == 0) err = elementName + " contains invalid characters";                                                
					alert(err); 
					return false;                   
				} 
			}
		break;

		case "req": 
		case "required":
			if (eval(fObj[elementName].value.length) == 0 || fObj[elementName].type == "checkbox" && fObj[elementName].checked == false) {
				if (!err || err.length == 0) err = elementName + " is a required field";
				alert(err); 
				return false;
			} 
		break;

		case "URL":
			if (fObj[elementName].value.length > 0) {
				var charpos = fObj[elementName].value.match(patterns['URL']);
				if (charpos == null) {
					if (!err || err.length == 0) err = elementName + " must contain a valid URL";
					alert(err);
					return false;
				}
			}
		break;

		case "val":
			if (fObj[elementName].value != cmdvalue && fObj[elementName].value.length > 0) {
				if (!err || err.length == 0) err = elementName + '" must contain the following value "' + cmdvalue + '"';
				alert(err);
				return false;
			}
		break;
		
		case "!val":
			if (fObj[elementName].value == cmdvalue && fObj[elementName].value.length > 0) {
				if (!err || err.length == 0) err = elementName + '" must not contain the following value "' + cmdvalue + '"';
				alert(err);
				return false;
			}
			break;

		case "zip":
			var charpos = fObj[elementName].value.search(patterns['zip']);
			if (fObj[elementName].value.length > 0 &&  charpos >= 0) {
				if (!err || err.length == 0) err = elementName + " must contain a valid zip code";
				alert(err + "\n [Error character position " + eval(charpos + 1) + "]");
				return false; 
			} 
		break;

		default:break;
	}
	return true;
}