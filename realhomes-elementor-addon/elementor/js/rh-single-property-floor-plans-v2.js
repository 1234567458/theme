/**
 * Single Property Floor Plans Widget V2 Class
 *
 * @since 2.4.0
 */
class RHSinglePropertyFloorPlansV2Class extends elementorModules.frontend.handlers.Base {
    // Default settings for the widget
    getDefaultSettings() {
        const widgetId = this.getID();
        return {
            selectors : {
                widget : `#rh-single-property-floor-plans-v2-${widgetId}`
            }
        };
    }

    // Default element selectors for the widget
    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );
        const $elements = {
            $widget : this.$element.find( selectors.widget )
        };

        return $elements;
    }

    // Initialize the widget
    async onInit() {
        super.onInit( ...arguments );

        const $tabsContainer = this.elements.$widget,
              $tabs          = $tabsContainer.find( '.rh-single-property-floor-plans-v2-tab' ),
              $panels        = $tabsContainer.find( '.rh-single-property-floor-plans-v2-tab-panel' );

        // Initialize - Set the first tab and panel as active
        $tabs.first().addClass( 'active' );
        $panels.hide().first().show();

        // Handle click events on tabs
        $tabs.on( 'click', function () {
            const $clickedTab = jQuery( this ),
                  tabIndex    = $clickedTab.index();

            // Remove active class from all tabs and add to the clicked tab
            $tabs.removeClass( 'active' );
            $clickedTab.addClass( 'active' );

            // Hide all panels and show the one corresponding to the clicked tab
            $panels.hide().eq( tabIndex ).fadeIn();
        } );
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const RHSinglePropertyFloorPlansV2Handler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHSinglePropertyFloorPlansV2Class, { $element } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rh-single-property-floor-plans-v2.default', RHSinglePropertyFloorPlansV2Handler );
} );