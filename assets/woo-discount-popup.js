$( document ).ready( function() {
	// Display shop popup after 2 seconds of page load
	if ( getCookieValue( 'woo-discount-popup' ) !== 'hide' ) {
		setTimeout( function() {
			$( '#woo-discount-popup' ).css( 'display', 'flex' ).hide().fadeIn( 'slow' );
		}, 2000 );
	}

	// Close the shop popup form 
	$( '.section-shop-popup .close-icon' ).click( function() {
		setCookie( 'woo-discount-popup', 'hide', 600 ); 

		$( '#woo-discount-popup' ).fadeOut( 'slow' );
		$( '.modal-success, .modal-backdrop' ).removeClass( 'show' );
		$( '#modal-success-popup, .modal-backdrop' ).hide();
		$( 'body' ).removeClass( 'hide-popup' );
		$( 'body' ).removeClass( 'modal-open' );
	} );	

	$( '.woo-discount-popup__overlay' ).on( 'click', function() {
		$( 'body' ).removeClass( 'modal-open' );
		setCookie( 'woo-discount-popup', 'hide', 600 );
		$( '#woo-discount-popup' ).fadeOut( 'slow' );
	} );	


	/**
	 * Shop-Popup Form Submit Event Handler
	 */
	var emailForm = $( '#woo-discount-popup__form' );
	if ( emailForm ) {
		emailForm.on( 'submit', function( event ) {
			event.preventDefault();

			let button = $( this ).find( 'button[type="submit"]' );
			button.prop( 'disabled', true );
			button.text( 'Processing...' );

			let email = $( '.email' ).val();
			let formData = new FormData();

			formData.append( 'action', 'email_form_handler' );
			formData.append( 'email', email );

			removeErrorMessage();

			fetch ( handler.email_form_handler, {
				method: 'POST',
				body: formData,
			} )

				.then( response => response.json() )
			
				.then( data => {
					handleResponse( data, button );
				} )
			
				.catch( error => {
					console.error( 'Error:', error );
				} );
		} );
	}
} );


/**
 * Processes server response, updates email errors, and manages button state and notifications.
 */
function handleResponse( data, submitButton ) {
	let email = $( '.email' );
	let emailError = $( '#emailError' );

	if ( emailError.length === 0 ) {
		emailError = $( '<span>', {
			id: 'emailError',
			class: 'error-message'
		} );
		emailError.insertAfter( email );
	}

	if ( data === 'email_required' ) {
		email.addClass( 'error' );
		emailError.text( 'This is a required field.' ).css( 'display', 'block' );

		submitButton.prop( 'disabled', false );
		submitButton.text( 'Submit' );
	}

	else if ( data === 'email_invalid' ) {
		email.addClass( 'error' );
		emailError.text( 'Please enter a valid email address!' ).css( 'display', 'block' );

		submitButton.prop( 'disabled', false );
		submitButton.text( 'Submit' );
	}

	else if ( data === 'not_allowed' ) {
		userNotifcation( handler.woo_discount_popup_error_message );
		setCookie( 'woo-discount-popup', 'hide', 600 );

	else if ( data === 'success' ) {
		userNotifcation( handler.woo_discount_popup_error_message );
		setCookie( 'woo-discount-popup', 'hide', 600 );
	}
}


/**
 * Removes the error state and hides the email error message.
 */ 
function removeErrorMessage() {
	let email = $( '.email' );
	email.removeClass( 'error' );
	
	let emailError = $( '#emailError' );
	if ( emailError.length > 0 ) {
		emailError.text( '' ).css( 'display', 'none' );
	}
}


/**
 * Displays a user notification message.
 */
function userNotifcation( userMessage ) {
	const wrapper = $( '.woo-discount-popup .popup-content' );
	const contentWrapper = $( '.woo-discount-popup .popup-content__wrapper' );
	const userMessageContainer = $( '<div>', {
		html: '<h2>' + userMessage + '</h2>'
	} );

	contentWrapper.fadeOut( 300, function() {
		userMessageContainer.prependTo( wrapper ).hide().fadeIn( 400 );
	} );	
}


/** 
 * Function for Setting Cookie
 * */
function setCookie( name, value, monthsToExpire ) {
	let expirationDate = new Date();
	expirationDate.setMonth( expirationDate.getMonth() + monthsToExpire );

	let cookieValue = encodeURIComponent( name ) + '=' + encodeURIComponent( value ) + '; expires=' + expirationDate.toUTCString() + '; path=/';
	document.cookie = cookieValue;
}


/** 
 * Function for Getting the Cookie Value
 * */
function getCookieValue( cookieName ) {
	let cookies = document.cookie.split( ';' );

	for ( let i = 0; i < cookies.length; i++ ) {
		let cookie = cookies[i].trim();
		let separatorIndex = cookie.indexOf( '=' );
	
		if ( separatorIndex === -1 ) {
			continue;
		}
	
		let name = cookie.substring( 0, separatorIndex ).trim();
		let value = cookie.substring( separatorIndex + 1 ).trim();
	
		value = decodeURIComponent( value );
	
		if ( name === cookieName ) {
			return value;
		}
	}

	return null;
}