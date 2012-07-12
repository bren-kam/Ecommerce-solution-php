/*
 * jQuery PHP Plugin
 * version: 0.8.3 (16/03/2009)
 * author:  Anton Shevchuk (http://anton.shevchuk.name)
 * @requires jQuery v1.2.1 or later
 *
 * Examples and documentation at: http://jquery.hohli.com/
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 */
function php (response, textStatus) {
	// call jQuery methods
	for (var i=0;i<response['q'].length; i++) {
	   
		var selector  = $(response['q'][i]['s']);
		var methods   = response['q'][i]['m'];
		var arguments = response['q'][i]['a'];
		
		loopMethods( selector, methods, arguments );
	}

	// predefined actions named as 
	// Methods of ObjResponse in PHP side 
	$.each(response['a'], function (func, params) {
		for (var i=0;i<params.length; i++) {
			try {
				php[func](params[i]);
			} catch (error) {
				// if is error
				alert('onAction: ' + func + '('+ params[i] +')\n'
								   +' in file: ' + error.fileName + '\n'
								   +' on line: ' + error.lineNumber +'\n'
								   +' error:   ' + error.message);
			}
		}
	});
}

// Loop methods/arguments
function loopMethods( selector, methods, params ) {
	var originalLength = methods.length;
	
	for (var j=0;j<originalLength; j++) { 
		try {
			var method   = methods[j];
			var argument = params[j];
			
			// We want to reduce the arrays as we go through them
			delete methods[j];
			delete params[j];
			
			if (method && method!= '' && method!= 'undefined') {
				switch (true) {
					// exception for 'ready', 'map', 'queue'
					case (method == 'ready' || method == 'map' || method == 'queue'):
					   selector = selector[method](window[argument[0]]);
					   break;
					// exception for 'bind' and 'one'
					case ((method == 'bind' || method == 'one') && argument.length == 3):
					   selector = selector[method](argument[0],argument[1],window[argument[2]]);
					   break;
					// exception for 'toggle' and 'hover'
					case ((method == 'toggle' || method == 'hover') && argument.length == 2):
					   selector = selector[method](window[argument[0]],window[argument[1]]);
					   break;
					// exception for 'filter'
					case (method == 'filter' && argument.length == 1):
					   // try run method
					   if (window[argument[0]] && window[argument[0]] != '' && window[argument[0]] != 'undefined') {
						   selector = selector[method](window[argument[0]]);
					   } else {
						   // try filter by specified expression
						   selector = selector[method](argument[0]);
					   }
					   break;
					// exception for effects with callback
					case ((   method == 'show'      || method == 'hide'
						   || method == 'slideDown' || method == 'slideUp' || method == 'slideToggle'
						   || method == 'fadeIn'    || method == 'fadeOut'
						   
						 ) && argument.length == 2):
					   selector = selector[method](argument[0],window[argument[1]]);
					   break;
					// exception for events with callback
					case ((   method == 'blur'      || method == 'change'
						   || method == 'click'     || method == 'dblclick'
						   || method == 'error'     || method == 'focus'
						   || method == 'keydown'   || method == 'keypress'  || method == 'keyup'
						   || method == 'load'      || method == 'unload'
						   || method == 'mousedown' || method == 'mousemove' || method == 'mouseout'
						   || method == 'mouseover' || method == 'mouseup'
						   || method == 'resize'    || method == 'scroll'
						   || method == 'select'    || method == 'submit'
						 ) && argument.length == 1):
					   selector = selector[method](window[argument[0]]);
					   break;
					// exception for 'fadeTo' with callback
					case (method == 'fadeTo' && argument.length == 3):
					   selector = selector[method](argument[0],argument[1],window[argument[2]]);
					   break;
					// exception for 'animate' with callback
					case (method == 'animate' && argument.length == 4):
					   selector = selector[method](argument[0],argument[1],argument[2],window[argument[3]]);
					   break;
					
					// Handle delays
					case ( 'delay' == method ):
						// Continue after a set amount of time
						setTimeout( function() {
							loopMethods( selector, methods, params );
						}, argument[0] );
						
						// End the loop
						j = originalLength;
					break;
					
					// universal
					case (argument.length == 0):
					   selector = selector[method]();
					   break;
					case (argument.length == 1):
					   selector = selector[method](argument[0]);
					   break;
					case (argument.length == 2):
					   selector = selector[method](argument[0],argument[1]);
					   break;
					case (argument.length == 3):
					   selector = selector[method](argument[0],argument[1],argument[2]);
					   break;
					case (argument.length == 4):
					   selector = selector[method](argument[0],argument[1],argument[2],argument[3]);
					   break;
					default:
					   selector = selector[method](argument);
					   break;
				}
			}
		} catch (error) {
			// if is error
			alert('onAction: $("'+ selector +'").'+ method +'("'+ argument +'")\n'
							+' in file: ' + error.fileName + '\n'
							+' on line: ' + error.lineNumber +'\n'
							+' error:   ' + error.message);
		}
	}
}
	