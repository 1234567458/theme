/**
 * RH Properties Widget V13 Class
 *
 * @since 2.4.0
 */
class RHSinglePropertyGalleryV4Class extends elementorModules.frontend.handlers.Base {
    // Default settings for the widget
    getDefaultSettings() {
        const widgetId = this.getID();
        return {
            selectors : {
                widget       : `#rh-single-property-gallery-v4-${widgetId}`,
                slider       : `#rh-single-property-gallery-v4-slider-${widgetId}`,
                pagination   : `#swiper-pagination-${widgetId}`,
                slideContent : `.swiper-slide`
            }
        };
    }

    // Default element selectors for the widget
    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );
        const $elements = {
            $widget     : this.$element.find( selectors.widget ),
            $slider     : this.$element.find( selectors.slider ),
            $pagination : this.$element.find( selectors.pagination )
        };

        $elements.$slides = $elements.$slider.find( selectors.slideContent );
        return $elements;
    }

    // Swiper slider settings
    getSwiperSettings() {
        const elementSettings = this.getElementSettings();
        return {
            slidesPerView            : 3,
            spaceBetween             : 0,
            centerInsufficientSlides : true,
            speed                    : elementSettings.speed,
            loop                     : 'yes' === elementSettings.infinite,
            pagination               : {
                el           : `#swiper-pagination-${this.getID()} > .swiper-pagination-inner`,
                clickable    : true,
                renderBullet : ( index, classname ) => `<span class="${classname}" data-bullet-index="${index}"></span>`
            },
            breakpoints              : {
                0   : {
                    slidesPerView : 1
                },
                767 : {
                    slidesPerView : 2
                },
                881 : {
                    slidesPerView : 3
                }
            }
        };
    }

    // Initialize the widget
    async onInit() {
        super.onInit( ...arguments );

        if ( ! this.elements.$slider.length ) {
            return;
        }

        await this.initSwiper();

        // Enable pause on hover if configured
        if ( this.getElementSettings().pause_on_hover === 'yes' ) {
            this.togglePauseOnHover( true );
        }
    }

    // Initialize the Swiper instance
    async initSwiper() {
        const Swiper          = elementorFrontend.utils.swiper;
        const sliderSettings  = this.getSwiperSettings();
        const elementSettings = this.getElementSettings();

        // Configure autoplay settings if enabled
        if ( elementSettings.autoplay === 'yes' ) {
            sliderSettings.autoplay = {
                delay                : elementSettings.autoplay_speed,
                disableOnInteraction : elementSettings.pause_on_interaction === 'yes'
            };
        }

        // Create the Swiper instance
        this.slider = await new Swiper( this.elements.$slider, sliderSettings );

        // Initialize custom transition
        this.paginationTransition();
    }

    paginationTransition() {
        const $pagination = this.elements.$pagination.find('.swiper-pagination-inner');

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
            $selectActiveBullet.stop().animate( {
                top  : target.top,
                left : target.left
            } );
        } );

        let moveIndicatorToActive  = function(){
            let target = jQuery( $pagination.find( '.swiper-pagination-bullet-active' ) ).position();
            $selectActiveBullet.stop().animate( {
                top  : target.top,
                left : target.left
            } );
        };

        // Update the active bullet indicator on slide change
        this.slider.on( 'slideChange', moveIndicatorToActive  );

        // Update the active bullet indicator on window resize
        jQuery( window ).on( 'resize', moveIndicatorToActive  );
    }

    // Enable or disable pause on hover functionality
    togglePauseOnHover( enable ) {
        if ( enable ) {
            this.elements.$widget.on( {
                mouseenter : () => this.slider.autoplay.stop(),
                mouseleave : () => this.slider.autoplay.start()
            } );
        } else {
            this.elements.$widget.off( 'mouseenter mouseleave' );
        }
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const RHSinglePropertyGalleryV4Handler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHSinglePropertyGalleryV4Class, { $element } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rh-single-property-gallery-v4.default', RHSinglePropertyGalleryV4Handler );
} );