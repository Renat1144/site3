( function () {
	'use strict';

	const header = document.querySelector( '.site-header' );
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( header ) {
		const updateHeader = () => header.classList.toggle( 'is-scrolled', window.scrollY > 24 );
		updateHeader();
		window.addEventListener( 'scroll', updateHeader, { passive: true } );
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

