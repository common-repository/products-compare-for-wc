/* globals evdpl_plugin_fw_ui */

// Make sure the evdpl object exists.
window.evdpl = window.evdpl || {};

( function ( $, evdpl ) {
	evdpl.ui = evdpl.ui || {};

	var cssClasses = function ( classes ) {
		if ( typeof classes === 'string' ) {
			return classes;
		} else {
			var filteredClasses = [];

			for ( var value of classes ) {
				if ( value && filteredClasses.indexOf( value ) < 0 ) {
					filteredClasses.push( cssClasses( value ) );
				}
			}
			return filteredClasses.join( ' ' );
		}
	}

	var stopEventPropagation = function ( e ) {
		e.stopPropagation();
	};

	/**
	 * Confirm window.
	 */
	evdpl.ui.confirm = function ( options ) {
		var defaults = {
				title                     : false,
				message                   : false,
				onConfirm                 : false,
				onCancel                  : false,
				onClose                   : false,
				classes                   : {
					wrap   : '',
					content: '',
					title  : '',
					message: '',
					footer : '',
					cancel : '',
					confirm: ''
				},
				confirmButtonType         : 'confirm',
				cancelButton              : evdpl_plugin_fw_ui.i18n.cancel,
				confirmButton             : evdpl_plugin_fw_ui.i18n.confirm,
				width                     : 350,
				closeAfterConfirm         : true,
				allowWpMenu               : false,
				allowWpMenuInMobile       : false,
				showClose                 : true,
				closeWhenClickingOnOverlay: false
			},
			self     = {};

		options         = typeof options !== 'undefined' ? options : {};
		options         = $.extend( {}, defaults, options );
		options.classes = $.extend( {}, defaults.classes, options.classes );

		var classes       = {
				wrap   : cssClasses( ['evdpl-plugin-fw__confirm__wrap', options.classes.wrap] ),
				content: cssClasses( ['evdpl-plugin-fw__confirm__content', options.classes.content] ),
				title  : cssClasses( ['evdpl-plugin-fw__confirm__title', options.classes.title] ),
				message: cssClasses( ['evdpl-plugin-fw__confirm__message', options.classes.message] ),
				footer : cssClasses( ['evdpl-plugin-fw__confirm__footer', options.classes.footer] ),
				cancel : cssClasses( ['evdpl-plugin-fw__confirm__button', 'evdpl-plugin-fw__confirm__button--cancel', options.classes.cancel] ),
				confirm: cssClasses( ['evdpl-plugin-fw__confirm__button', 'evdpl-plugin-fw__confirm__button--' + options.confirmButtonType, options.classes.confirm] )
			},
			dom           = {
				message: false,
				footer : false,
				cancel : false,
				confirm: false
			},
			modal         = false,
			initialize    = function () {
				create();
				initEvents();
			},
			handleClose   = function () {
				modal && modal.close();
				modal = false;
			},
			create        = function () {
				dom.message = $( '<div class="' + classes.message + '">' );
				dom.footer  = $( '<div class="' + classes.footer + '">' );
				dom.cancel  = $( '<span class="' + classes.cancel + '">' + options.cancelButton + '</span>' );
				dom.confirm = $( '<span class="' + classes.confirm + '">' + options.confirmButton + '</span>' );


				if ( options.message ) {
					dom.message.html( options.message );
				}

				dom.footer.append( dom.cancel );
				dom.footer.append( dom.confirm );

				modal = evdpl.ui.modal(
					{
						classes                   : {
							wrap   : classes.wrap,
							title  : classes.title,
							content: classes.content
						},
						title                     : options.title,
						content                   : [dom.message, dom.footer],
						width                     : options.width,
						allowWpMenu               : options.allowWpMenu,
						allowWpMenuInMobile       : options.allowWpMenuInMobile,
						showClose                 : options.showClose,
						onClose                   : options.onClose,
						closeWhenClickingOnOverlay: options.closeWhenClickingOnOverlay
					}
				);
			},
			handleCancel  = function () {
				if ( typeof options.onCancel === 'function' ) {
					options.onCancel();
				}

				handleClose();
			},
			handleConfirm = function () {
				if ( typeof options.onConfirm === 'function' ) {
					options.onConfirm();
				}

				if ( options.closeAfterConfirm ) {
					handleClose();
				}
			},
			initEvents    = function () {
				dom.cancel.on( 'click', handleCancel );
				dom.confirm.on( 'click', handleConfirm );
			};

		initialize();

		self.elements = $.extend( {}, dom );
		self.modal    = $.extend( {}, modal );
		self.close    = handleClose;
		self.cancel   = handleCancel;
	};


	/**
	 * Modal window.
	 */
	evdpl.ui.modal = function ( options ) {
		var defaults = {
				allowWpMenu               : true,
				allowWpMenuInMobile       : false,
				title                     : false,
				content                   : false,
				footer                    : false,
				showClose                 : true,
				closeSelector             : false,
				classes                   : {
					wrap   : '',
					main   : '',
					close  : '',
					title  : '',
					content: '',
					footer : ''
				},
				width                     : 500,
				allowClosingWithEsc       : true,
				closeWhenClickingOnOverlay: false,
				scrollContent             : true,
				onClose                   : false
			},
			self     = {};

		options         = typeof options !== 'undefined' ? options : {};
		options         = $.extend( {}, defaults, options );
		options.classes = $.extend( {}, defaults.classes, options.classes );

		var container      = $( '#wpwrap' ),
			classes        = {
				wrap   : ['evdpl-plugin-ui', 'evdpl-plugin-fw__modal__wrap', options.classes.wrap],
				main   : ['evdpl-plugin-fw__modal__main', options.classes.main],
				close  : ['evdpl-plugin-fw__modal__close', 'evdpl-icon', 'evdpl-icon-close', options.classes.close],
				title  : ['evdpl-plugin-fw__modal__title', options.classes.title],
				content: ['evdpl-plugin-fw__modal__content', options.classes.content],
				footer : ['evdpl-plugin-fw__modal__footer', options.classes.footer]
			},
			dom            = {
				wrap   : false,
				main   : false,
				close  : false,
				title  : false,
				content: false,
				footer : false
			},
			initialize     = function () {
				handleClose();

				create();
				initEvents();
			},
			handleClose    = function () {
				$( '.evdpl-plugin-fw__modal__wrap' ).remove();
				container.removeClass( 'evdpl-plugin-fw__modal--opened' );
				container.removeClass( 'evdpl-plugin-fw__modal--allow-wp-menu' );
				container.removeClass( 'evdpl-plugin-fw__modal--allow-wp-menu-in-mobile' );

				if ( typeof options.onClose === 'function' ) {
					options.onClose();
				}
			},
			create         = function () {
				dom.wrap    = $( '<div class="' + cssClasses( classes.wrap ) + '">' );
				dom.main    = $( '<div class="' + cssClasses( classes.main ) + '">' );
				dom.close   = $( '<span class="' + cssClasses( classes.close ) + '">' );
				dom.title   = $( '<div class="' + cssClasses( classes.title ) + '">' );
				dom.content = $( '<div class="' + cssClasses( classes.content ) + '">' );
				dom.footer  = $( '<div class="' + cssClasses( classes.footer ) + '">' );

				dom.main.css( { width: options.width } );


				if ( options.title ) {
					if ( typeof options.title === 'string' ) {
						dom.title.html( options.title );
					} else {
						dom.title.append( options.title );
					}
				}

				if ( options.content ) {
					if ( typeof options.content === 'string' ) {
						dom.content.html( options.content );
					} else {
						dom.content.append( options.content );
					}
				}

				if ( options.showClose ) {
					dom.main.append( dom.close );
				}

				dom.main.append( dom.title );
				dom.main.append( dom.content );


				if ( options.footer ) {
					if ( typeof options.footer === 'string' ) {
						dom.footer.html( options.footer );
					} else {
						dom.footer.append( options.footer );
					}

					dom.main.append( dom.footer );
				}


				dom.wrap.append( dom.main );

				if ( options.scrollContent ) {
					dom.wrap.addClass( 'evdpl-plugin-fw__modal__wrap--scroll-content' );
				}

				container.append( dom.wrap );
				container.addClass( 'evdpl-plugin-fw__modal--opened' );
				if ( options.allowWpMenu ) {
					container.addClass( 'evdpl-plugin-fw__modal--allow-wp-menu' );
				}

				if ( options.allowWpMenuInMobile ) {
					container.addClass( 'evdpl-plugin-fw__modal--allow-wp-menu-in-mobile' );
				}


			},
			initEvents     = function () {
				dom.close.on( 'click', handleClose );
				if ( options.closeSelector ) {
					container.on( 'click', options.closeSelector, handleClose );
				}

				if ( options.closeWhenClickingOnOverlay ) {
					dom.wrap.on( 'click', handleClose );
					dom.main.on( 'click', stopEventPropagation );
				}

				$( document ).on( 'keydown', handleKeyboard );
			},
			handleKeyboard = function ( event ) {
				if ( options.allowClosingWithEsc && event.keyCode === 27 ) {
					handleClose();
				}
			}

		initialize();

		self.elements = $.extend( {}, dom );
		self.close    = handleClose;

		return self;

	}

} )( window.jQuery, window.evdpl );
