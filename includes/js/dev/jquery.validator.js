/**
 * jQuery Form of the Javascript Validator
 */

/* Luhn algorithm number checker - (c) 2005-2009 - planzero.org            *
 * This code has been released into the public domain, however please      *
 * give credit to the original author where possible.                      */
function luhnCheck( number ) {
	/**
	 * Variables:
	 *		1) [number] - Strip any non-digits (useful for credit card numbers with spaces and hyphens)
	 *		2) Set the parity
	 *		3) Set teh total
	 */
	var number = number.replace( /\D/g, '' ), parity = number.length % 2, total = 0;

	for ( var i = 0; i < numberLength; i++ ) {
		var digit = number.charAt(i);
		
		// Multiply alternate digits by two
		if (i % 2 == parity) {
			digit = digit * 2;

			// If the sum is two digits, add them together (in effect)
			if ( digit > 9 )
			digit = digit - 9;
		}

		// Total up the digits
		total = total + parseInt( digit );
	}

	// If the total mod 10 equals 0, the number is valid
    return total % 10 == 0;
}

// From PHP.JS
function inArray(needle,haystack,argStrict){var found=false,key,strict=!!argStrict;for(key in haystack){if((strict&&haystack[key]===needle)||(!strict&&haystack[key]==needle)){found=true;break;}}
return found;}

function Validator(fName) {
	this.fObj = document.forms[fName];
	
	// Makes the the form able to be trigger by the .trigger funciton
	var trigger = ( arguments.length > 0 ) ? arguments[1] : false;
	
	// Make sure the form is real, if not, show an error
	if( !this.fObj ) {
		// Send error to firebug
	  	console.error( "Could not get form object: " + fName );
		return false;
	}

	// Change the onsubmit element to null
	this.fObj.onsubmit = null;
	
	// If we set the trigger or the onsubmit
	if( trigger ) {
		this.fObj.trigger = formSubmitHandler;
	} else {
		this.fObj.onsubmit = formSubmitHandler;
	}
	
	// Assign differention functions
	this.addValidation = addValidation;
	
	/**
	 * Not commonly used -- uncomment if necessary
	 *
	this.removeValidation = removeValidation;
	this.clearAllValidations = clearAllValidations;
	this.clearValidations = clearValidations;
	this.restoreValidations = retoreValidations;
	*/
	
	return true;
}

/**
function clearAllValidations() {
	for(var i = 0; i < this.fObj.elements.length; i++) {
		this.fObj.elements[i].vSet = null;
	}
}

function clearValidations(elementArray) {
	start = (arguments[1]) ? arguments[1] : 0;
	for(var i = start; i < this.fObj.elements.length; i++) {
		if(this.fObj.elements[i].val) {
			if(inArray(this.fObj.elements[i].val.vSet[0][0], elementArray)) {
				this.fObj.elements[i].val.oldSet = this.fObj.elements[i].val.vSet;
				this.fObj.elements[i].val.vSet = null;
			}
		}
	}
}

function retoreValidations(elementArray) {
	start = (arguments[1]) ? arguments[1] : 0;
	for(var i = start; i < this.fObj.elements.length; i++) {
		if(this.fObj.elements[i].val) {
			if(this.fObj.elements[i].val.oldSet) {
				this.fObj.elements[i].val.vSet = this.fObj.elements[i].val.oldSet;
			}
		}
	}
}

function removeValidation( elementName ) {
	var elementObj = this.fObj[elementName];
	
	if( elementObj ) {
		if( elementObj.val ) {
			elementObj.val.oldSet = elementObj.val.vSet;
			elementObj.val.vSet = null;
		}
	}
}
*/

function formSubmitHandler() {
	// Loop through the form elements, if something doesn't validate return false
	for( var i = 0; i < this.elements.length; i++ ) {
		if( this.elements[i].val && this.elements[i].val.vSet && !this.elements[i].val.validate( this ) )
			return false;
	}
	
	// It was successful
	return true;
}

// Add a validation
function addValidation( elementName, desc, err ) {
	// Get the element
	var elementObj = this.fObj[elementName];
	
	// If the element doesn't exist, check to see if there are multiple
	if( !elementObj ) {
		// Get array
		elementArray = elementName.split('|');
		
		// If more than one element exists
		if( elementArray.length > 1 ) {
			// If we can't get either of the objects
			if( !this.fObj[elementArray[0]] || !this.fObj[elementArray[1]] ) {
				console.error("Could not get the input objects");
				return;
			}
			
			// If they don't have validation for this element, add it
			if(!this.fObj[elementArray[0]].val)
				// Add the validation
				this.fObj[elementArray[0]].val = new Val();
				
			// Add the validation
			this.fObj[elementArray[0]].val.add( elementName, desc, err );
		} else {
			console.error( "Could not get the input object named: " + elementName );
			return;
		}
	} else {
		// One element
		/**
		 * NOTE: Note sure what this does -- probably with restoration and clearing of values
		 *
		if( elementObj.val ) {
			// Validation exists
			
			if(elementObj.val.oldSet) {
				elementObj.val.vSet = elementObj.val.oldSet;
			} else {
				elementObj.val.add(elementName, desc, err);
			}
			
		} else {
			
			elementObj.val.add(elementName, desc, err);
		}*/
		
		// If it doesn't exist, add it
		if( !elementObj.val )
			elementObj.val = new Val();
		
		// Add validation
		elementObj.val.add( elementName, desc, err );
	}
}

// Val Class
function Val( elementName, desc, err ) {
	// Set the validation set
	this.vSet = new Array();
	
	// Set the function to add a validation
	this.add = addVal;
	
	// Set the validation function
	this.validate = validate;
}

// Add a validation item to the elmeent
function addVal(elementName, desc, error) {
	// Add it on to the end
	this.vSet.push([elementName, desc, error]);
}

// Validate the form object
function validate( fObj ) {
	// Loop through the elements
	for( i = 0; i < this.vSet.length; i++ ) {
		// If it's valid, continue
		if( checkValidation( this.vSet[i][0], this.vSet[i][1], fObj ) )
			continue;
		
		// Focus on the element (first one) and return false
		alert( this.vSet[i][2] );
		elementArray = this.vSet[i][0].split('|');
		fObj[elementArray[0]].focus();
		return false;
	}
	
	return true;
}

// Show an error message and return false
function valError( errorMessage, charpos ) {
	
	return false;
}

// Check the validation to make sure it's valid
function checkValidation( elementName, descriptor, fObj ) {
	/**
	 * Variables:
	 *		1) [tmpArray] - Split the command if it has more than one value
	 *		2) [command] - The validation action ('req', 'email', etc)
	 *		3) [cmdValue] - The other half, such as minlen=30
	 */
	var tmpArray = descriptor.split('='), command = tmpArray[0], cmdValue = ( tmpArray.length > 1 ) ? tmpArray[1] : '', value = $(fObj[elementName]).val(); 
	
	// Define regex patterns
	var patterns = {
		'alnum': /[^A-Za-z0-9\ ]/,
		'alnumhyphen': /[^A-Za-z0-9\-_]/,
		'alpha': /[^A-Za-z]/,
		'cc': /^(3[47]|4|5[1-5]|6011)/,
		'csv': /[^-a-zA-Z0-9,\s]/,
		'date': /^[\d]{4}-[\d]{2}-[\d]{2}$/,
		'email': /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/,
		'float': /[^0-9\.]/,
		'img': /^[0-9A-Za-z_ \-]+(.[jJ][pP][gG]|.[jJ][pP][eE][gG]|.[gG][iI][fF]|.[pP][nN][gG])$/,
		'num': /[^0-9]/,
		'phone': /[^0-9\- ()]/,
		'URL': /(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/,
		'zip': /[^-0-9]/
	}
	
	// Loop through the commands
	switch( command ) {
		case "alnum":
		case "alnumhyphen":
		case "alpha":
		case "csv":
		case "float":
		case "num":
		case "phone":
		case "zip":
			// Get the character position of any illegal patterns
			var charpos = value.search( patterns[command] );
			
			// Validate it
			if( value.length > 0 && charpos >= 0 )
				return false
		break; 
		
		/**
		 * Credit Card Types:
		 *		1) Visa
		 *		2) MasterCard
		 *		3) Discover
		 *		4) American Express
		 */
		case "cc":
			// Get object value
			var objValue = value.replace( /\D/g, '' );

			// Make sure it's  valid credit card type
			if( null == patterns['cc'].exec( objValue ) )
				return false;
				
			// Add the specific validation for the other types
			switch( objValue[0] ) {
				// Visa
				case '4':
					if( 13 != objValue.length && 16 != objValue.length )
						return false;
					
					// Set the type to select it in the credit card drop down
					var cardType = 1;
				break;

				// MasterCard
				case '5':
					if( 16 != objValue.length )
						return false;
					
					// Set the type to select it in the credit card drop down
					var cardType = 2;
				break;

				// Discover
				case '6':
					if( 16 != objValue.length )
						return false;
					
					// Set the type to select it in the credit card drop down
					var cardType = 3;
				break;

				// AmEx
				case '3':
					if( 15 != objValue.length )
						return false;
					
					// Set the type to select it in the credit card drop down
					var cardType = 4;
				break;
			}

			// Do the Mod 10 (Luhn Check), if valid, select the right credit card type
			if( !luhnCheck( objValue ) )
				return false;
			
			// Make sure there is a card type
			if( 'undefined' != typeof cardType )
				$('#' + cmdValue + ' options:eq(' + cardType - 1 + ')').attr( 'selected', true ); // Select the option
		break;

		// Match
		case "date":
		case "email": 
		case "URL":
			if( value.length > 0 && null == value.match( patterns[command] ) )
				return false;
		break;

		// Certain extensions
		case "ext":
			// Set variables
			var accept = cmdValue.split('|'), extArray = value.split('.'), ext = extArray[extArray.length-1].toLowerCase();
			
			if( -1 == accept.indexOf(ext) )
				return false;
		break; 
		
		// Greater than
		case "gt":
			if( parseInt( value ) <= parseInt( cmdValue ) )
				return false;
		break;
		
		// Make sure it's a valid image
		case "image":
		case "img":
			var imgFileArray = value.split("\\"), imgFile = imgFileArray[imgFileArray.length-1], charpos = imgFile.match( patterns['img'] );
			
			if( value.length > 0 && charpos >= 0 )
				return false;
		break;
		
		// Lesser than
		case "lt":
			if( parseInt( value ) >= parseInt( cmdValue ) )
				return false;
		break;
		
		// Two elements must match
		case "match":
			// Get the two elements
			var tmpElements = elementName.split('|');
			
			if( $(fObj[tmpElements[0]]).val() != $(fObj[tmpElements[1]]).val() )
				return false;
		break;
		
		// Maximum length of a field
		case "maxlen":
			if( value.length > parseInt( cmdValue ) )
				return false;
		break;              
		
		// Minimum length of a field
		case "minlen": 
			if( value.length < parseInt( cmdValue ) )
				return false;                 
		break;
		
		// An item must exist be filled in
		case "req": 
			if( 0 == value.length || "checkbox" == fObj[elementName].type && !fObj[elementName].checked )
				return false;
		break;
		
		// Must have a specific value
		case "val":
			if( value != cmdValue & value.length > 0 )
				return false;
		break;
		
		// Must not have a specific value
		case "!val":
			if( value == cmdValue && value.length > 0 )
				return false;
		break;

		default:break;
	}
	
	return true;
}