/**
 * Single Property Schedule A Tour Widget V2 Class
 *
 * @since 2.4.0
 */

class RHSinglePropertyScheduleTourV2Class extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        const widgetId = this.getID();
        return {
            selectors : {
                widget           : `#rh-single-property-schedule-tour-v2-${widgetId}`,
                date             : `#schedule-date-${widgetId}`,
                form             : '.rh-single-property-schedule-tour-v2-form',
                submitButton     : '.submit-button',
                ajaxLoader       : '#sat-loader',
                messageContainer : '.message-container',
                errorContainer   : '.error-container'
            }
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );
        return {
            $widget           : this.$element.find( selectors.widget ),
            $date             : this.$element.find( selectors.date ),
            $form             : this.$element.find( selectors.form ),
            $submitButton     : this.$element.find( selectors.submitButton ),
            $ajaxLoader       : this.$element.find( selectors.ajaxLoader ),
            $messageContainer : this.$element.find( selectors.messageContainer ),
            $errorContainer   : this.$element.find( selectors.errorContainer )
        };
    }

    bindEvents() {
        this.loadScheduleSectionRender();
        this.scheduleATourFromProcess();
    }

    loadScheduleSectionRender( event ) {
        let date = this.elements.$date;

        if ( jQuery().datepicker ) {
            date.datepicker( {
                minDate    : 0,
                showAnim   : 'slideDown',
                beforeShow : function ( input, inst ) {
                    inst.dpDiv[0].classList.add( 'rh-single-property-schedule-tour-v2-date-datepicker' );
                    inst.dpDiv[0].classList.add( 'rhea-schedule-section-wrapper' );
                }
            } );
        }
    }

    scheduleATourFromProcess( event ) {
        if ( jQuery().validate && jQuery().ajaxSubmit ) {
            const form             = this.elements.$form,
                  submitButton     = this.elements.$submitButton,
                  ajaxLoader       = this.elements.$ajaxLoader,
                  messageContainer = this.elements.$messageContainer,
                  errorContainer   = this.elements.$errorContainer,
                  formOptions      = {
                      beforeSubmit : function () {
                          submitButton.attr( 'disabled', 'disabled' );
                          ajaxLoader.fadeIn( 'fast' );
                          messageContainer.fadeOut( 'fast' );
                          errorContainer.fadeOut( 'fast' );
                      },
                      success      : function ( ajax_response, statusText, xhr, $form ) {
                          let response = jQuery.parseJSON( ajax_response );
                          ajaxLoader.fadeOut( 'fast' );
                          submitButton.removeAttr( 'disabled' );
                          if ( response.success ) {
                              $form.resetForm();
                              messageContainer.html( response.message ).fadeIn( 'fast' );

                              setTimeout( function () {
                                  messageContainer.fadeOut( 'slow' )
                              }, 5000 );

                              // call reset function if it exists
                              if ( typeof inspiryResetReCAPTCHA == 'function' ) {
                                  inspiryResetReCAPTCHA();
                              }
                          } else {
                              errorContainer.html( response.message ).fadeIn( 'fast' );
                          }
                      }
                  };

            form.validate( {
                errorLabelContainer : errorContainer,
                submitHandler       : function ( form ) {
                    jQuery( form ).ajaxSubmit( formOptions );
                }
            } );
        }
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const RHSinglePropertyScheduleTourV2Handler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHSinglePropertyScheduleTourV2Class, { $element } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rh-single-property-schedule-tour-v2.default', RHSinglePropertyScheduleTourV2Handler );
} );