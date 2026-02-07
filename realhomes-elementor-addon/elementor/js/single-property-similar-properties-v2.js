/**
 * RH Single Property Similar Properties Widget V2 Class.
 *
 * @since 2.4.0
 */
class RHSinglePropertySimilarPropertiesV2Class extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        const widgetId       = this.getID(),
              widgetPrefix   = `rh-single-property-similar-properties-v2`,
              widgetSelector = `#${widgetPrefix}-${widgetId}`;
        return {
            selectors : {
                widget             : widgetSelector,
                slider             : `#${widgetPrefix}-slider-${widgetId}`,
                carouselPagination : `#swiper-pagination-${widgetId}`,
                slideContent       : `.swiper-slide`
            }
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );
        const elements  = {
            $widget             : this.$element.find( selectors.widget ),
            $slider             : this.$element.find( selectors.slider ),
            $carouselPagination : this.$element.find( selectors.carouselPagination )
        };

        elements.$slides = elements.$slider.find( selectors.slideContent );
        return elements;
    }

    getSwiperSettings() {
        const elementSettings = this.getElementSettings();
        const swiperOptions   = {
            slidesPerView : 3,
            spaceBetween  : 1,
            speed         : elementSettings.speed,
            loop          : 'yes' === elementSettings.infinite,
            navigation    : {
                nextEl : `#swiper-button-next-${this.getID()}`,
                prevEl : `#swiper-button-prev-${this.getID()}`
            },
            pagination    : {
                el           : `#swiper-pagination-${this.getID()} > .swiper-pagination-inner`,
                clickable    : true,
                renderBullet : ( index, classname ) => `<span class="${classname}" data-bullet-index="${index}"></span>`
            },
            breakpoints   : {
                0    : {
                    slidesPerView : 1
                },
                767  : {
                    slidesPerView : 2
                },
                1200 : {
                    slidesPerView : 3
                },
                1366 : {
                    slidesPerView : 3
                }
            }
        };

        if ( 'yes' === elementSettings.autoplay ) {
            swiperOptions.autoplay = {
                delay                : elementSettings.autoplay_speed,
                disableOnInteraction : 'yes' === elementSettings.pause_on_interaction
            };
        }

        return swiperOptions;
    }

    async onInit() {
        super.onInit( ...arguments );

        const elementSettings = this.getElementSettings();

        if ( 'carousel' !== elementSettings.layout ) {
            return;
        }

        if ( ! this.elements.$slider.length ) {
            return;
        }

        await this.initSwiper();

        if ( 'yes' === elementSettings.pause_on_hover ) {
            this.togglePauseOnHover( true );
        }
    }

    async initSwiper() {
        const Swiper         = elementorFrontend.utils.swiper;
        const elements       = this.getDefaultElements();
        const swiperSettings = this.getSwiperSettings();

        // Initialize the properties slider
        this.similarPropertiesSlider = await new Swiper( elements.$slider, swiperSettings );

        // Initialize custom transition
        this.paginationTransition();
    }

    togglePauseOnHover( toggleOn ) {
        if ( toggleOn ) {
            this.elements.$widget.on( {
                mouseenter : () => this.similarPropertiesSlider.autoplay.stop(),
                mouseleave : () => this.similarPropertiesSlider.autoplay.start()
            } );
        } else {
            this.elements.$widget.off( 'mouseenter mouseleave' );
        }
    }

    paginationTransition() {
        const $pagination = this.elements.$carouselPagination.find( '.swiper-pagination-inner' );

        // Add the custom active bullet indicator
        $pagination.append( '<span class="select-active-bullet"></span>' );

        const $selectActiveBullet = $pagination.find( '.select-active-bullet' );
        const $allBullets         = $pagination.find( '.swiper-pagination-bullet' );

        // Set the initial position and opacity of the active bullet indicator
        $selectActiveBullet.css( {
            left    : $allBullets.first().position().left,
            opacity : 1
        } );

        // Handle bullet clicks
        $pagination.on( 'click', '.swiper-pagination-bullet', ( event ) => {
            let target = jQuery( event.target ).position();
            $selectActiveBullet.animate( {
                top  : target.top,
                left : target.left
            } );
        } );

        // Update the active bullet indicator on slide change
        this.similarPropertiesSlider.on( 'slideChange', () => {
            $pagination.find( '.swiper-pagination-bullet-active' ).trigger( 'click' );
        } );

        jQuery( window ).on( 'resize', () => {
            $pagination.find( '.swiper-pagination-bullet-active' ).trigger( 'click' );
        } );
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const rhSinglePropertySimilarPropertiesV2Handler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHSinglePropertySimilarPropertiesV2Class, { $element } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rh-single-property-similar-properties-v2.default', rhSinglePropertySimilarPropertiesV2Handler );
} );
