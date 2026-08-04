( function () {
	'use strict';

	const roots = Array.from( document.querySelectorAll( '[data-ts-contact-root]' ) );

	if ( ! roots.length || ! ( 'HTMLDialogElement' in window ) ) {
		return;
	}

	const instances = roots.map( ( root ) => {
		const dialog = root.querySelector( '[data-ts-contact-dialog]' );
		const form = dialog?.querySelector( '[data-ts-contact-form]' );
		const panel = dialog?.querySelector( '.ts-contact-modal__panel' );
		const closeButton = dialog?.querySelector( '[data-ts-contact-close]' );
		const status = dialog?.querySelector( '[data-ts-contact-status]' );
		const submitButton = form?.querySelector( 'button[type="submit"]' );
		const success = dialog?.querySelector( '[data-ts-contact-success]' );
		const dialogLabelledBy = dialog?.getAttribute( 'aria-labelledby' ) || '';
		let lastTrigger = null;

		if ( ! dialog || ! form || ! submitButton ) {
			return null;
		}

		const setStatus = ( message, type = '' ) => {
			if ( status ) {
				status.textContent = message;
				status.dataset.state = type;
			}
		};

		const closeDialog = () => {
			if ( dialog.open ) {
				dialog.close();
			}
		};

		const openDialog = ( trigger ) => {
			lastTrigger = trigger || null;
			dialog.classList.remove( 'is-success' );
			if ( dialogLabelledBy ) {
				dialog.setAttribute( 'aria-labelledby', dialogLabelledBy );
			} else {
				dialog.removeAttribute( 'aria-labelledby' );
			}
			form.hidden = false;
			if ( success ) {
				success.hidden = true;
			}
			setStatus( '' );

			const tourTitle = trigger?.closest( '.tour-modal__content' )?.querySelector( 'h2, h3' )?.textContent?.trim()
				|| document.querySelector( '.tour-page-title' )?.textContent?.trim()
				|| '';
			const contextField = form.querySelector( '[name="tour_context"]' );
			if ( contextField ) {
				contextField.value = tourTitle;
			}

			const tourDialog = trigger?.closest( 'dialog.tour-modal' );
			if ( tourDialog?.open ) {
				tourDialog.close();
			}

			document.documentElement.classList.add( 'ts-contact-open' );
			dialog.showModal();
			window.requestAnimationFrame( () => form.querySelector( 'input[name="name"]' )?.focus( { preventScroll: true } ) );
		};

		closeButton?.addEventListener( 'click', closeDialog );
		dialog.addEventListener( 'click', ( event ) => {
			if ( event.target !== dialog || ! panel ) {
				return;
			}
			const bounds = panel.getBoundingClientRect();
			if ( event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom ) {
				closeDialog();
			}
		} );
		dialog.addEventListener( 'close', () => {
			dialog.classList.remove( 'is-success' );
			if ( ! document.querySelector( '[data-ts-contact-dialog][open]' ) ) {
				document.documentElement.classList.remove( 'ts-contact-open' );
			}
			lastTrigger?.focus?.( { preventScroll: true } );
		} );

		form.addEventListener( 'submit', async ( event ) => {
			event.preventDefault();
			setStatus( '' );

			if ( ! form.reportValidity() ) {
				return;
			}

			submitButton.disabled = true;
			submitButton.dataset.originalLabel = submitButton.textContent;
			submitButton.textContent = 'Отправляем…';
			form.setAttribute( 'aria-busy', 'true' );

			try {
				const response = await fetch( form.getAttribute( 'action' ), {
					method: 'POST',
					body: new FormData( form ),
					credentials: 'same-origin',
					headers: { 'X-Requested-With': 'XMLHttpRequest' },
				} );
				const payload = await response.json();

				if ( ! response.ok || ! payload.success ) {
					throw new Error( payload?.data?.message || 'Не удалось отправить заявку. Попробуйте ещё раз.' );
				}

				form.reset();
				form.hidden = true;
				if ( success ) {
					dialog.classList.add( 'is-success' );
					success.hidden = false;
					const successTitle = success.querySelector( 'h3' );
					if ( successTitle?.id ) {
						dialog.setAttribute( 'aria-labelledby', successTitle.id );
					}
					closeButton?.focus( { preventScroll: true } );
				}
			} catch ( error ) {
				setStatus( error.message || 'Не удалось отправить заявку. Попробуйте ещё раз.', 'error' );
			} finally {
				submitButton.disabled = false;
				submitButton.textContent = submitButton.dataset.originalLabel || 'Отправить заявку';
				form.removeAttribute( 'aria-busy' );
			}
		} );

		return { root, openDialog };
	} ).filter( Boolean );

	if ( ! instances.length ) {
		return;
	}

	document.addEventListener( 'click', ( event ) => {
		const trigger = event.target.closest( '[data-ts-contact-open], a[href="#contact"], a[href="#contact-form"]' );
		if ( ! trigger ) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();
		const ownRoot = trigger.closest( '[data-ts-contact-root]' );
		const instance = instances.find( ( candidate ) => candidate.root === ownRoot ) || instances[ 0 ];
		instance.openDialog( trigger );
	}, true );
}() );
