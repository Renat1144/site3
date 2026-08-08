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
	const galleryCards = Array.from( gallery?.querySelectorAll( '.destination-track > .destination-card' ) || [] );

	const tourDetails = Array.from( gallery?.querySelectorAll( '.tour-details-library > .tour-details' ) || [] );

	if ( gallery && galleryCards.length && tourDetails.length === galleryCards.length && 'HTMLDialogElement' in window ) {
		const tourModal = document.createElement( 'dialog' );
		tourModal.className = 'tour-modal';
		tourModal.setAttribute( 'aria-modal', 'true' );
		tourModal.innerHTML = `
			<div class="tour-modal__panel">
				<div class="tour-modal__media"><img alt=""></div>
				<div class="tour-modal__body"><div class="tour-modal__content"></div></div>
				<button class="tour-modal__close" type="button" aria-label="Закрыть описание тура">×</button>
			</div>
		`;
		document.body.append( tourModal );

		const tourModalPanel = tourModal.querySelector( '.tour-modal__panel' );
		const tourModalImage = tourModal.querySelector( '.tour-modal__media img' );
		const tourModalContent = tourModal.querySelector( '.tour-modal__content' );
		const tourModalClose = tourModal.querySelector( '.tour-modal__close' );
		let tourModalTrigger = null;
		let tourModalScrollTarget = null;

		const closeTourModal = () => {
			if ( tourModal.open ) {
				tourModal.close();
			}
		};

		const openTourModal = ( card, trigger ) => {
			const cardIndex = galleryCards.indexOf( card );
			const details = tourDetails[ cardIndex ];
			const cardImage = card.querySelector( 'img' );

			if ( cardIndex < 0 || ! details || ! cardImage || ! tourModalContent || ! tourModalImage ) {
				return;
			}

			tourModalContent.innerHTML = details.innerHTML;
			tourModalImage.src = cardImage.currentSrc || cardImage.src;
			tourModalImage.alt = cardImage.alt || '';
			tourModalImage.removeAttribute( 'srcset' );
			tourModalTrigger = trigger;
			tourModalScrollTarget = null;

			const modalTitle = tourModalContent.querySelector( 'h2, h3' );
			if ( modalTitle ) {
				modalTitle.id = 'tour-modal-title';
				tourModal.setAttribute( 'aria-labelledby', modalTitle.id );
			} else {
				tourModal.removeAttribute( 'aria-labelledby' );
			}

			const scrollbarCompensation = Math.max( window.innerWidth - document.documentElement.clientWidth, 0 );
			document.documentElement.style.setProperty( '--tour-scrollbar-compensation', `${ scrollbarCompensation }px` );
			document.documentElement.classList.add( 'tour-modal-open' );
			tourModal.showModal();
			tourModalClose?.focus( { preventScroll: true } );
		};

		galleryCards.forEach( ( card, index ) => {
			const trigger = card.querySelector( '.tour-details-trigger .wp-block-button__link' );
			const cardTitle = card.querySelector( '.destination-card-copy h3' )?.textContent?.trim() || `№ ${ index + 1 }`;

			if ( trigger ) {
				trigger.setAttribute( 'aria-haspopup', 'dialog' );
				trigger.setAttribute( 'aria-label', `Подробнее о туре «${ cardTitle }»` );
			}
		} );

		gallery.addEventListener( 'click', ( event ) => {
			const trigger = event.target.closest( '.tour-details-trigger .wp-block-button__link' );
			if ( ! trigger ) {
				return;
			}

			const card = trigger.closest( '.destination-card' );
			if ( galleryCards.includes( card ) ) {
				event.preventDefault();
				openTourModal( card, trigger );
			}
		} );

		tourModalClose?.addEventListener( 'click', closeTourModal );
		tourModal.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				closeTourModal();
			}
		} );
		tourModal.addEventListener( 'click', ( event ) => {
			const modalLink = event.target.closest( '.tour-detail-cta a[href^="#"]' );
			if ( modalLink ) {
				event.preventDefault();
				tourModalScrollTarget = document.querySelector( modalLink.hash );
				closeTourModal();
				return;
			}

			if ( event.target === tourModal && tourModalPanel ) {
				const panelRect = tourModalPanel.getBoundingClientRect();
				const outsidePanel = event.clientX < panelRect.left || event.clientX > panelRect.right || event.clientY < panelRect.top || event.clientY > panelRect.bottom;
				if ( outsidePanel ) {
					closeTourModal();
				}
			}
		} );

		tourModal.addEventListener( 'close', () => {
			document.documentElement.classList.remove( 'tour-modal-open' );
			document.documentElement.style.removeProperty( '--tour-scrollbar-compensation' );

			if ( tourModalScrollTarget ) {
				const target = tourModalScrollTarget;
				tourModalScrollTarget = null;
				window.requestAnimationFrame( () => target.scrollIntoView( { behavior: reducedMotion ? 'auto' : 'smooth' } ) );
			} else {
				tourModalTrigger?.focus( { preventScroll: true } );
			}
		} );
	}

	const updateOnScroll = () => {
		updateHeader();
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
		const brandLink = brandLockup.querySelector( '.brand-name a' );
		const isHomePage = document.body.classList.contains( 'home' );
		const activateBrand = ( event ) => {
			if ( isHomePage ) {
				event?.preventDefault();
				window.scrollTo( { top: 0, behavior: reducedMotion ? 'auto' : 'smooth' } );
				return;
			}

			if ( event?.target?.closest?.( 'a' ) ) {
				return;
			}

			event?.preventDefault();
			if ( brandLink?.href ) {
				window.location.assign( brandLink.href );
			}
		};

		brandLockup.setAttribute( 'role', 'link' );
		brandLockup.setAttribute( 'tabindex', '0' );
		brandLockup.setAttribute( 'aria-label', isHomePage ? 'Перейти к началу страницы' : 'Перейти на главную страницу' );
		brandLockup.addEventListener( 'click', activateBrand );
		brandLockup.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				activateBrand( event );
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

	const cookieConsent = document.querySelector( '.cookie-consent' );
	if ( cookieConsent ) {
		const cookieName = 'ts_cookie_consent';
		const maxAge = 365 * 24 * 60 * 60;
		const readPreference = () => {
			const match = document.cookie.split( '; ' ).find( ( item ) => item.startsWith( `${ cookieName }=` ) );
			return match ? decodeURIComponent( match.split( '=' ).slice( 1 ).join( '=' ) ) : '';
		};
		const showConsent = () => {
			cookieConsent.classList.add( 'is-visible' );
		};
		const hideConsent = () => {
			cookieConsent.classList.remove( 'is-visible' );
		};
		const storePreference = ( value ) => {
			const secure = window.location.protocol === 'https:' ? '; Secure' : '';
			document.cookie = `${ cookieName }=${ encodeURIComponent( value ) }; Path=/; Max-Age=${ maxAge }; SameSite=Lax${ secure }`;
			document.documentElement.dataset.cookieConsent = value;
			hideConsent();
			window.dispatchEvent( new CustomEvent( 'ts:cookie-consent', { detail: { value } } ) );
		};

		cookieConsent.setAttribute( 'role', 'dialog' );
		cookieConsent.setAttribute( 'aria-label', 'Настройки cookie' );
		cookieConsent.setAttribute( 'aria-live', 'polite' );

		const currentPreference = readPreference();
		if ( currentPreference === 'all' || currentPreference === 'essential' ) {
			document.documentElement.dataset.cookieConsent = currentPreference;
		} else {
			window.requestAnimationFrame( showConsent );
		}

		cookieConsent.querySelector( '.cookie-consent__accept a' )?.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			storePreference( 'all' );
		} );
		cookieConsent.querySelector( '.cookie-consent__essential a' )?.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			storePreference( 'essential' );
		} );

		document.addEventListener( 'click', ( event ) => {
			const settingsLink = event.target.closest( '.footer-legal-links a[href="#cookie-settings"]' );
			if ( ! settingsLink ) {
				return;
			}
			event.preventDefault();
			showConsent();
			window.requestAnimationFrame( () => cookieConsent.querySelector( '.cookie-consent__accept a' )?.focus( { preventScroll: true } ) );
		} );
	}

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
