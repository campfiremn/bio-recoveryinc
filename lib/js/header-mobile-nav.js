jQuery( function($) {
        'use strict';

	// Insert mobile menu before the Genesis Header Right widget navigation menu
	$( '<div id="header-mobile-menu">&#x2261;</div>' ).insertBefore( 'nav.nav-header ul.genesis-nav-menu' );
	
	// Add .hide class to .nav-header .genesis-nav-menu to hide it for small screen sizes
	$( 'nav.nav-header ul.genesis-nav-menu' ).addClass( 'hide' );

	// Toggle Header Right widget navigation menu for mobile menu
	$('#header-mobile-menu').on( 'click', function() {
		$('nav.nav-header ul.genesis-nav-menu').slideToggle();
		$(this).toggleClass('active');
	});

});