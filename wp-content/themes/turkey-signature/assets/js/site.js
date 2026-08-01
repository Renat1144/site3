( function () {
	'use strict';

	document.documentElement.classList.add( 'has-js' );

	const header = document.querySelector( '.site-header' );
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	let ticking = false;

	const updateHeader = () => {
		if ( header ) {
			header.classList.toggle( 'is-scrolled', window.scrollY > 48 );
		}
	};

	const gallery = document.querySelector( '.destination-section' );
	const galleryViewport = gallery?.querySelector( '.destination-viewport' );
	const galleryTrack = gallery?.querySelector( '.destination-track' );
	const galleryProgress = gallery?.querySelector( '.destination-progress' );

	const updateScrollGallery = () => {
		if ( ! gallery || ! galleryViewport || ! galleryTrack || reducedMotion || window.innerWidth < 782 ) {
			galleryTrack?.style.removeProperty( 'transform' );
			galleryProgress?.style.setProperty( '--gallery-progress', '0' );
			return;
		}

		const rect = gallery.getBoundingClientRect();
		const scrollable = Math.max( gallery.offsetHeight - window.innerHeight, 1 );
		const progress = Math.min( Math.max( -rect.top / scrollable, 0 ), 1 );
		const travel = Math.max( galleryTrack.scrollWidth - galleryViewport.clientWidth, 0 );
		galleryTrack.style.transform = `translate3d(${-travel * progress}px, 0, 0)`;
		galleryProgress?.style.setProperty( '--gallery-progress', progress.toFixed( 4 ) );
	};

	const updateOnScroll = () => {
		updateHeader();
		updateScrollGallery();
		ticking = false;
	};

	const requestScrollUpdate = () => {
		if ( ! ticking ) {
			window.requestAnimationFrame( updateOnScroll );
			ticking = true;
		}
	};

	updateOnScroll();
	window.addEventListener( 'scroll', requestScrollUpdate, { passive: true } );
	window.addEventListener( 'resize', requestScrollUpdate, { passive: true } );

	const brandLockup = document.querySelector( '.brand-lockup' );
	if ( brandLockup ) {
		const scrollHome = ( event ) => {
			event?.preventDefault();
			window.scrollTo( { top: 0, behavior: reducedMotion ? 'auto' : 'smooth' } );
		};

		brandLockup.setAttribute( 'role', 'link' );
		brandLockup.setAttribute( 'tabindex', '0' );
		brandLockup.setAttribute( 'aria-label', 'Перейти к началу страницы' );
		brandLockup.addEventListener( 'click', scrollHome );
		brandLockup.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				scrollHome( event );
			}
		} );
	}

	const navigation = document.querySelector( '.main-navigation' );
	const openButton = navigation?.querySelector( '.wp-block-navigation__responsive-container-open' );
	const closeButton = navigation?.querySelector( '.wp-block-navigation__responsive-container-close' );
	const container = navigation?.querySelector( '.wp-block-navigation__responsive-container' );
	const syncMenuClass = () => {
		document.documentElement.classList.toggle( 'ts-menu-open', Boolean( container?.classList.contains( 'is-menu-open' ) ) );
	};

	if ( container && 'MutationObserver' in window ) {
		new MutationObserver( syncMenuClass ).observe( container, { attributes: true, attributeFilter: [ 'class' ] } );
		syncMenuClass();
	}

	if ( document.documentElement.dataset.staticExport === 'true' ) {
		if ( openButton && closeButton && container ) {
			const setMenuState = ( isOpen ) => {
				container.classList.toggle( 'is-menu-open', isOpen );
				document.documentElement.classList.toggle( 'has-modal-open', isOpen );
				document.documentElement.classList.toggle( 'ts-menu-open', isOpen );
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

	const slides = Array.from( document.querySelectorAll( '.hero-slide' ) );
	const heroCounter = document.querySelector( '.hero-counter span' );
	let activeSlide = 0;
	let slideTimer;

	const showSlide = ( index ) => {
		activeSlide = ( index + slides.length ) % slides.length;
		slides.forEach( ( slide, slideIndex ) => {
			slide.classList.toggle( 'is-active', slideIndex === activeSlide );
		} );
		if ( heroCounter ) {
			heroCounter.textContent = String( activeSlide + 1 ).padStart( 2, '0' );
		}
	};

	if ( slides.length > 1 && ! reducedMotion ) {
		showSlide( 0 );
		slideTimer = window.setInterval( () => showSlide( activeSlide + 1 ), 5200 );
		document.addEventListener( 'visibilitychange', () => {
			window.clearInterval( slideTimer );
			if ( ! document.hidden ) {
				slideTimer = window.setInterval( () => showSlide( activeSlide + 1 ), 5200 );
			}
		} );
	}

	document.querySelectorAll( '.program-list, .faq-list' ).forEach( ( list ) => {
		list.querySelectorAll( 'details' ).forEach( ( item ) => {
			item.addEventListener( 'toggle', () => {
				if ( ! item.open ) {
					return;
				}
				list.querySelectorAll( 'details[open]' ).forEach( ( other ) => {
					if ( other !== item ) {
						other.open = false;
					}
				} );
			} );
		} );
	} );

	document.querySelectorAll( '.faq-list' ).forEach( ( list ) => {
		const hoverEvent = 'PointerEvent' in window ? 'pointerover' : 'mouseover';
		const leaveEvent = 'PointerEvent' in window ? 'pointerout' : 'mouseout';
		const getItem = ( event ) => event.target.closest( 'details' );
		const acceptsHover = ( event ) => ! event.pointerType || event.pointerType === 'mouse' || event.pointerType === 'pen';
		const hoverTimers = new WeakMap();

		list.addEventListener( hoverEvent, ( event ) => {
			const item = getItem( event );
			if ( ! item || ! list.contains( item ) || ! acceptsHover( event ) || ( event.relatedTarget && item.contains( event.relatedTarget ) ) ) {
				return;
			}
			window.clearTimeout( hoverTimers.get( item ) );
			hoverTimers.set( item, window.setTimeout( () => {
				item.open = true;
			}, 90 ) );
		} );

		list.addEventListener( leaveEvent, ( event ) => {
			const item = getItem( event );
			if ( ! item || ! acceptsHover( event ) || ( event.relatedTarget && item.contains( event.relatedTarget ) ) ) {
				return;
			}
			window.clearTimeout( hoverTimers.get( item ) );
			item.open = false;
		} );
	} );

	const revealTargets = document.querySelectorAll( '.reveal-on-scroll' );
	if ( reducedMotion || ! ( 'IntersectionObserver' in window ) ) {
		revealTargets.forEach( ( target ) => target.classList.add( 'is-visible' ) );
		return;
	}

	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px', threshold: 0.08 }
	);

	revealTargets.forEach( ( target ) => observer.observe( target ) );
}() );
