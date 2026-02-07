/**
 * ES6 Class for Elementor Ultra Single Similar Properties Widget
 *
 * @since 2.3.2
 * */

class RHEAUltraSingleSimilarProperties extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {

        return {
            selectors : {
                similarPropertiesWrapper : '#similar-properties-wrapper',
                similarProperties        : '#similar-properties',
                filterWrapper            : '#rhea-similar-filters-wrapper'
            }
        };
    }

    getDefaultElements() {

        const selectors = this.getSettings( 'selectors' );
        return {
            $similarPropertiesWrapper : this.$element.find( selectors.similarPropertiesWrapper ),
            $similarProperties        : this.$element.find( selectors.similarProperties ),
            $filterWrapper            : this.$element.find( selectors.filterWrapper )

        };
    }

    bindEvents() {

        this.UltraSingleSimilarProperties();
    }

    UltraSingleSimilarProperties( event ) {

        let similarPropertiesWrapper = this.elements.$similarPropertiesWrapper,
            similarProperties     = this.elements.$similarProperties,
            similarFilterWrapper     = this.elements.$filterWrapper,
            similarPropertiesNonce            = similarFilterWrapper.data('similar-properties-request-nonce');

        if ( similarFilterWrapper.length ) {
            const similarPropertiesFilters = similarFilterWrapper.find( 'a' ),
                  similarPropertiesHtml    = similarProperties.html();

            // Check for localized similar properties data
            if ( typeof similarPropertiesData !== "undefined" ) {

                const design           = similarPropertiesData.design;
                let buttonClass        = 'rh-btn rh-btn-primary';
                let buttonClassCurrent = 'rh-btn rh-btn-secondary';

                if ( 'classic' === design ) {
                    buttonClass        = '';
                    buttonClassCurrent = 'current';
                }

                similarFilterWrapper.on( 'click', 'a', function ( event ) {
                    let self           = jQuery( this );
                    let propertyFilter = self.data( 'similar-properties-filter' );

                    similarPropertiesFilters.removeClass( buttonClassCurrent ).addClass( buttonClass );
                    self.removeClass( buttonClass ).addClass( buttonClassCurrent );

                    if ( 'recommended' === propertyFilter ) {
                        similarProperties.html( similarPropertiesHtml );
                    } else {
                        jQuery.ajax( {
                            url        : ajaxurl,
                            type       : 'POST',
                            dataType   : 'json', // Expect a JSON response from the server
                            data       : {
                                action              : 'realhomes_filter_similar_properties',
                                property_id         : similarPropertiesData.propertyId,
                                properties_per_page : similarPropertiesData.propertiesPerPage,
                                property_filter     : propertyFilter,
                                design              : design,
                                nonce               : similarPropertiesNonce
                            },
                            beforeSend : function () {
                                similarPropertiesWrapper.addClass( 'loading' );
                            },
                            success    : function ( response ) {
                                similarPropertiesWrapper.removeClass( 'loading' );

                                // Check the success flag in the response
                                if ( response.success ) {
                                    // Insert the HTML content if the response is successful
                                    similarProperties.html( response.data );
                                } else {
                                    // Handle the error and show the message
                                    similarProperties.html( '<div class="error-message">' + response.data.message + '</div>' );
                                }
                            },
                            error      : function ( jqXHR, textStatus, errorThrown ) {
                                similarPropertiesWrapper.removeClass( 'loading' );
                                console.error( 'AJAX Error:', textStatus, errorThrown );
                            }
                        } );
                    }

                    event.preventDefault();
                } );
            } else {
                // Hide filters when no data available
                similarFilterWrapper.hide();
            }
        }
    }

}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const singleSimilarPropertyWidgetHandler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHEAUltraSingleSimilarProperties, {
            $element
        } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rhea-ultra-single-property-similar-properties.default', singleSimilarPropertyWidgetHandler );
} );