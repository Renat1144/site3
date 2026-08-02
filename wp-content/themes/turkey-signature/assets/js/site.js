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
	const galleryCards = Array.from( galleryTrack?.querySelectorAll( '.destination-card' ) || [] );
	const gallerySlider = galleryProgress?.querySelector( '.destination-slider' );
	const galleryPrevious = galleryProgress?.querySelector( '.destination-arrow-prev' );
	const galleryNext = galleryProgress?.querySelector( '.destination-arrow-next' );
	const galleryCurrent = galleryProgress?.querySelector( '.destination-current' );
	const galleryTotal = galleryProgress?.querySelector( '.destination-total' );
	const carouselAutoplaySpeed = 14;
	const carouselUserPauseDuration = 6000;
	let carouselTicking = false;
	let activeCardIndex = 0;
	let carouselAutoplayFrame = 0;
	let carouselLastFrame = 0;
	let carouselVirtualPosition = 0;
	let carouselResumeTimer = 0;
	let carouselPauseUntil = 0;
	let carouselInView = ! ( 'IntersectionObserver' in window );
	let carouselWrapping = false;
	let carouselWrapTimer = 0;
	let carouselModalOpen = false;
	let galleryClones = [];

	const clamp = ( value, minimum, maximum ) => Math.min( Math.max( value, minimum ), maximum );
	const formatCardIndex = ( index ) => String( index + 1 ).padStart( 2, '0' );
	const createGalleryClones = () => {
		if ( ! galleryTrack || galleryClones.length ) {
			return;
		}

		galleryClones = galleryCards.map( ( card ) => {
			const clone = card.cloneNode( true );
			clone.classList.add( 'destination-card-clone' );
			clone.setAttribute( 'aria-hidden', 'true' );
			clone.setAttribute( 'inert', '' );
			clone.querySelectorAll( '[id]' ).forEach( ( element ) => element.removeAttribute( 'id' ) );
			clone.querySelectorAll( 'a, button, input, select, textarea, [tabindex]' ).forEach( ( element ) => element.setAttribute( 'tabindex', '-1' ) );
			galleryTrack.append( clone );
			return clone;
		} );
	};
	const getCarouselCycleLength = () => Math.max( ( galleryClones[ 0 ]?.offsetLeft || 0 ) - ( galleryCards[ 0 ]?.offsetLeft || 0 ), 0 );
	const getCardOffsets = () => {
		const firstCardOffset = galleryCards[ 0 ]?.offsetLeft || 0;
		return galleryCards.map( ( card ) => card.offsetLeft - firstCardOffset );
	};
	const getNormalizedGalleryPosition = () => {
		const cycleLength = getCarouselCycleLength();
		const currentPosition = gallery?.classList.contains( 'is-continuous-autoplay' ) ? carouselVirtualPosition : ( galleryViewport?.scrollLeft || 0 );

		if ( cycleLength <= 0 ) {
			return currentPosition;
		}

		return ( ( currentPosition % cycleLength ) + cycleLength ) % cycleLength;
	};
	const normalizeGalleryPosition = () => {
		if ( ! galleryViewport ) {
			return 0;
		}

		const normalizedPosition = getNormalizedGalleryPosition();

		if ( ! carouselWrapping && ! gallery?.classList.contains( 'is-continuous-autoplay' ) && Math.abs( galleryViewport.scrollLeft - normalizedPosition ) > 0.5 ) {
			galleryViewport.scrollLeft = normalizedPosition;
		}

		return normalizedPosition;
	};
	const getClosestCardIndex = () => {
		const offsets = getCardOffsets();
		const cycleLength = getCarouselCycleLength();
		const currentPosition = getNormalizedGalleryPosition();
		let closestIndex = 0;
		let closestDistance = Number.POSITIVE_INFINITY;

		offsets.forEach( ( offset, index ) => {
			const directDistance = Math.abs( offset - currentPosition );
			const distance = cycleLength > 0 ? Math.min( directDistance, cycleLength - directDistance ) : directDistance;
			if ( distance < closestDistance ) {
				closestDistance = distance;
				closestIndex = index;
			}
		} );

		return closestIndex;
	};
	const setArrowState = ( arrow, isDisabled ) => {
		if ( ! arrow ) {
			return;
		}
		arrow.setAttribute( 'aria-disabled', String( isDisabled ) );
		arrow.setAttribute( 'tabindex', isDisabled ? '-1' : '0' );
	};
	const updateCarousel = () => {
		if ( ! gallery || ! galleryViewport || ! galleryProgress || ! gallerySlider || ! galleryCards.length ) {
			carouselTicking = false;
			return;
		}

		const cycleLength = getCarouselCycleLength();
		const currentPosition = normalizeGalleryPosition();
		const progress = cycleLength > 0 ? clamp( currentPosition / cycleLength, 0, 1 ) : 0;
		activeCardIndex = getClosestCardIndex();
		galleryProgress.style.setProperty( '--gallery-progress', progress.toFixed( 4 ) );
		if ( galleryCurrent ) {
			galleryCurrent.textContent = formatCardIndex( activeCardIndex );
		}
		gallerySlider.setAttribute( 'aria-valuenow', String( activeCardIndex ) );
		gallerySlider.setAttribute( 'aria-valuetext', galleryCards[ activeCardIndex ]?.querySelector( 'h3' )?.textContent?.trim() || formatCardIndex( activeCardIndex ) );
		setArrowState( galleryPrevious, galleryCards.length < 2 );
		setArrowState( galleryNext, galleryCards.length < 2 );
		carouselTicking = false;
	};
	const requestCarouselUpdate = () => {
		if ( ! carouselTicking ) {
			window.requestAnimationFrame( updateCarousel );
			carouselTicking = true;
		}
	};
	const scrollToCard = ( index ) => {
		if ( ! galleryViewport || ! galleryCards.length ) {
			return;
		}

		const cycleLength = getCarouselCycleLength();
		const normalizedIndex = ( ( index % galleryCards.length ) + galleryCards.length ) % galleryCards.length;
		let targetPosition = getCardOffsets()[ normalizedIndex ];

		if ( index < 0 && cycleLength > 0 ) {
			carouselWrapping = true;
			gallery.classList.add( 'is-carousel-wrapping' );
			galleryViewport.scrollLeft = getNormalizedGalleryPosition() + cycleLength;
			window.requestAnimationFrame( () => {
				galleryViewport.scrollTo( {
					left: targetPosition,
					behavior: reducedMotion ? 'auto' : 'smooth',
				} );
			} );

			if ( carouselWrapTimer ) {
				window.clearTimeout( carouselWrapTimer );
			}

			carouselWrapTimer = window.setTimeout( () => {
				carouselWrapping = false;
				carouselWrapTimer = 0;
				gallery.classList.remove( 'is-carousel-wrapping' );
				requestCarouselUpdate();
			}, reducedMotion ? 0 : 800 );
			activeCardIndex = normalizedIndex;
			requestCarouselUpdate();
			return;
		} else if ( index >= galleryCards.length && cycleLength > 0 ) {
			targetPosition = cycleLength;
		} else if ( 0 === normalizedIndex && getNormalizedGalleryPosition() > cycleLength / 2 ) {
			targetPosition = cycleLength;
		}

		activeCardIndex = normalizedIndex;
		galleryViewport.scrollTo( {
			left: targetPosition,
			behavior: reducedMotion ? 'auto' : 'smooth',
		} );
		requestCarouselUpdate();
	};
	const stopCarouselAutoplay = () => {
		const wasRunning = gallery?.classList.contains( 'is-continuous-autoplay' );

		if ( carouselAutoplayFrame ) {
			window.cancelAnimationFrame( carouselAutoplayFrame );
			carouselAutoplayFrame = 0;
		}

		if ( wasRunning && galleryViewport && galleryTrack ) {
			galleryViewport.scrollLeft = carouselVirtualPosition;
			galleryTrack.style.removeProperty( 'transform' );
		}

		carouselLastFrame = 0;
		gallery?.classList.remove( 'is-continuous-autoplay' );
	};
	const canCarouselAutoplay = () => (
		! reducedMotion &&
		! document.hidden &&
		carouselInView &&
		! carouselModalOpen &&
		! gallerySlider?.classList.contains( 'is-dragging' ) &&
		Date.now() >= carouselPauseUntil
	);
	const runCarouselAutoplay = ( timestamp ) => {
		if ( ! canCarouselAutoplay() || ! galleryViewport || galleryCards.length < 2 ) {
			stopCarouselAutoplay();
			return;
		}

		const cycleLength = getCarouselCycleLength();

		if ( carouselLastFrame && cycleLength > 0 ) {
			const elapsed = Math.min( timestamp - carouselLastFrame, 80 );
			carouselVirtualPosition += carouselAutoplaySpeed * elapsed / 1000;

			if ( carouselVirtualPosition >= cycleLength ) {
				carouselVirtualPosition -= cycleLength;
			}

			galleryTrack.style.transform = `translate3d(${ -carouselVirtualPosition }px, 0, 0)`;
			requestCarouselUpdate();
		}

		carouselLastFrame = timestamp;
		carouselAutoplayFrame = window.requestAnimationFrame( runCarouselAutoplay );
	};
	const startCarouselAutoplay = () => {
		if ( carouselAutoplayFrame || ! canCarouselAutoplay() || galleryCards.length < 2 ) {
			return;
		}

		carouselVirtualPosition = getNormalizedGalleryPosition();
		galleryViewport.scrollLeft = 0;
		gallery?.classList.add( 'is-continuous-autoplay' );
		galleryTrack.style.transform = `translate3d(${ -carouselVirtualPosition }px, 0, 0)`;
		carouselLastFrame = 0;
		carouselAutoplayFrame = window.requestAnimationFrame( runCarouselAutoplay );
	};
	const pauseCarouselAutoplay = ( duration = carouselUserPauseDuration ) => {
		carouselPauseUntil = Math.max( carouselPauseUntil, Date.now() + duration );
		stopCarouselAutoplay();

		if ( carouselResumeTimer ) {
			window.clearTimeout( carouselResumeTimer );
		}

		carouselResumeTimer = window.setTimeout( () => {
			carouselResumeTimer = 0;
			startCarouselAutoplay();
		}, duration );
	};
	const syncCarouselAutoplay = () => {
		if ( canCarouselAutoplay() ) {
			startCarouselAutoplay();
		} else {
			stopCarouselAutoplay();
		}
	};
	const enhanceArrow = ( arrow, label, step ) => {
		if ( ! arrow ) {
			return;
		}

		arrow.setAttribute( 'role', 'button' );
		arrow.setAttribute( 'aria-label', label );
		arrow.addEventListener( 'click', () => {
			if ( arrow.getAttribute( 'aria-disabled' ) !== 'true' ) {
				pauseCarouselAutoplay();
				scrollToCard( getClosestCardIndex() + step );
			}
		} );
		arrow.addEventListener( 'keydown', ( event ) => {
			if ( ( event.key === 'Enter' || event.key === ' ' ) && arrow.getAttribute( 'aria-disabled' ) !== 'true' ) {
				event.preventDefault();
				pauseCarouselAutoplay();
				scrollToCard( getClosestCardIndex() + step );
			}
		} );
	};

	if ( gallery && galleryViewport && galleryTrack && galleryProgress && gallerySlider && galleryCards.length ) {
		createGalleryClones();
		galleryViewport.id = galleryViewport.id || 'destination-carousel';
		if ( galleryTotal ) {
			galleryTotal.textContent = formatCardIndex( galleryCards.length - 1 );
		}
		enhanceArrow( galleryPrevious, 'Предыдущий тур', -1 );
		enhanceArrow( galleryNext, 'Следующий тур', 1 );
		gallerySlider.setAttribute( 'role', 'slider' );
		gallerySlider.setAttribute( 'tabindex', '0' );
		gallerySlider.setAttribute( 'aria-label', 'Положение в списке туров' );
		gallerySlider.setAttribute( 'aria-controls', galleryViewport.id );
		gallerySlider.setAttribute( 'aria-valuemin', '0' );
		gallerySlider.setAttribute( 'aria-valuemax', String( galleryCards.length - 1 ) );

		const updateFromPointer = ( clientX ) => {
			const sliderRect = gallerySlider.getBoundingClientRect();
			const ratio = clamp( ( clientX - sliderRect.left ) / Math.max( sliderRect.width, 1 ), 0, 0.9999 );
			galleryViewport.scrollLeft = getCarouselCycleLength() * ratio;
			updateCarousel();
		};
		const stopDragging = ( event ) => {
			if ( gallerySlider.classList.contains( 'is-dragging' ) ) {
				gallerySlider.classList.remove( 'is-dragging' );
				gallery.classList.remove( 'is-slider-dragging' );
				if ( event?.pointerId !== undefined && gallerySlider.hasPointerCapture( event.pointerId ) ) {
					gallerySlider.releasePointerCapture( event.pointerId );
				}
				scrollToCard( getClosestCardIndex() );
			}
		};

		gallerySlider.addEventListener( 'pointerdown', ( event ) => {
			event.preventDefault();
			pauseCarouselAutoplay();
			gallerySlider.classList.add( 'is-dragging' );
			gallery.classList.add( 'is-slider-dragging' );
			gallerySlider.setPointerCapture( event.pointerId );
			updateFromPointer( event.clientX );
		} );
		gallerySlider.addEventListener( 'pointermove', ( event ) => {
			if ( gallerySlider.classList.contains( 'is-dragging' ) ) {
				updateFromPointer( event.clientX );
			}
		} );
		gallerySlider.addEventListener( 'pointerup', stopDragging );
		gallerySlider.addEventListener( 'pointercancel', stopDragging );
		gallerySlider.addEventListener( 'click', ( event ) => {
			pauseCarouselAutoplay();
			updateFromPointer( event.clientX );
			scrollToCard( getClosestCardIndex() );
		} );
		gallerySlider.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'ArrowLeft' || event.key === 'ArrowDown' ) {
				event.preventDefault();
				pauseCarouselAutoplay();
				scrollToCard( getClosestCardIndex() - 1 );
			} else if ( event.key === 'ArrowRight' || event.key === 'ArrowUp' ) {
				event.preventDefault();
				pauseCarouselAutoplay();
				scrollToCard( getClosestCardIndex() + 1 );
			} else if ( event.key === 'Home' ) {
				event.preventDefault();
				pauseCarouselAutoplay();
				scrollToCard( 0 );
			} else if ( event.key === 'End' ) {
				event.preventDefault();
				pauseCarouselAutoplay();
				scrollToCard( galleryCards.length - 1 );
			}
		} );

		galleryViewport.addEventListener( 'scroll', requestCarouselUpdate, { passive: true } );
		galleryViewport.addEventListener( 'pointerdown', () => pauseCarouselAutoplay(), { passive: true } );
		galleryViewport.addEventListener( 'wheel', () => pauseCarouselAutoplay(), { passive: true } );
		document.addEventListener( 'visibilitychange', syncCarouselAutoplay );
		if ( 'IntersectionObserver' in window ) {
			const galleryObserver = new IntersectionObserver( ( entries ) => {
				carouselInView = Boolean( entries[ 0 ]?.isIntersecting );
				syncCarouselAutoplay();
			}, { threshold: 0.25 } );
			galleryObserver.observe( gallery );
		}
		window.addEventListener( 'resize', requestCarouselUpdate, { passive: true } );
		[ ...galleryCards, ...galleryClones ].forEach( ( card ) => {
			card.querySelectorAll( 'img' ).forEach( ( image ) => {
				if ( ! image.complete ) {
					image.addEventListener( 'load', requestCarouselUpdate, { once: true } );
				}
			} );
		} );
		requestCarouselUpdate();
		syncCarouselAutoplay();
	}

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
			carouselModalOpen = true;
			stopCarouselAutoplay();
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
			carouselModalOpen = false;
			pauseCarouselAutoplay();

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
