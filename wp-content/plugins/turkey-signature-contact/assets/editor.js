( function ( blocks, blockEditor, components, element, i18n, ServerSideRender ) {
	'use strict';

	const el = element.createElement;
	const { InspectorControls, useBlockProps } = blockEditor;
	const { PanelBody, TextControl, TextareaControl } = components;
	const { __ } = i18n;

	const fields = [
		[ 'formTitle', 'Заголовок формы', TextControl ],
		[ 'formText', 'Пояснение к форме', TextareaControl ],
		[ 'nameLabel', 'Подпись поля имени', TextControl ],
		[ 'phoneLabel', 'Подпись поля телефона', TextControl ],
		[ 'emailLabel', 'Подпись поля E-mail', TextControl ],
		[ 'consentText', 'Текст согласия', TextareaControl ],
		[ 'submitLabel', 'Текст кнопки', TextControl ],
		[ 'successTitle', 'Заголовок после отправки', TextControl ],
		[ 'successText', 'Сообщение после отправки', TextareaControl ],
	];

	blocks.registerBlockType( 'turkey-signature/contact-form', {
		edit: ( props ) => {
			const blockProps = useBlockProps( { className: 'ts-contact-editor-block' } );
			const controls = fields.map( ( [ key, label, Control ] ) => el( Control, {
				key,
				label: __( label, 'turkey-signature-contact' ),
				value: props.attributes[ key ] || '',
				onChange: ( value ) => props.setAttributes( { [ key ]: value } ),
			} ) );

			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el( PanelBody, { title: __( 'Содержание формы', 'turkey-signature-contact' ), initialOpen: true }, controls )
				),
				el(
					'div',
					blockProps,
					el( ServerSideRender, {
						block: 'turkey-signature/contact-form',
						attributes: props.attributes,
						httpMethod: 'POST',
					} )
				)
			);
		},
		save: () => null,
	} );
} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.element,
	window.wp.i18n,
	window.wp.serverSideRender
);
