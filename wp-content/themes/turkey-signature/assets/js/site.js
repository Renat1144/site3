( function () {
	'use strict';

	const header = document.querySelector( '.site-header' );
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( header ) {
		const updateHeader = () => header.classList.toggle( 'is-scrolled', window.scrollY > 24 );
		updateHeader();
		window.addEventListener( 'scroll', updateHeader, { passive: true } );
	}

	if ( document.documentElement.dataset.staticExport === 'true' ) {
		const navigation = document.querySelector( '.main-navigation' );
		const openButton = navigation?.querySelector( '.wp-block-navigation__responsive-container-open' );
		const closeButton = navigation?.querySelector( '.wp-block-navigation__responsive-container-close' );
		const container = navigation?.querySelector( '.wp-block-navigation__responsive-container' );

		if ( openButton && closeButton && container ) {
			const setMenuState = ( isOpen ) => {
				container.classList.toggle( 'is-menu-open', isOpen );
				document.documentElement.classList.toggle( 'has-modal-open', isOpen );
				openButton.setAttribute( 'aria-expanded', String( isOpen ) );
				container.setAttribute( 'aria-hidden', String( ! isOpen ) );
			};

			openButton.addEventListener( 'click', () => {
				setMenuState( true );
				closeButton.focus();
			} );
			closeButton.addEventListener( 'click', () => {
				setMenuState( false );
				openButton.focus();
			} );
			container.querySelectorAll( 'a' ).forEach( ( link ) => {
				link.addEventListener( 'click', () => setMenuState( false ) );
			} );
			document.addEventListener( 'keydown', ( event ) => {
				if ( event.key === 'Escape' && container.classList.contains( 'is-menu-open' ) ) {
					setMenuState( false );
					openButton.focus();
				}
			} );
			setMenuState( false );
		}
	}

	if ( reducedMotion || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const targets = document.querySelectorAll( '.reveal-on-scroll' );
	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -12% 0px', threshold: 0.12 }
	);

	targets.forEach( ( target ) => observer.observe( target ) );
}() );
