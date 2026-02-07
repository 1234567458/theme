/**
 * ES6 Class for Elementor Agent/Agency Posts Widget
 *
 * @since 2.3.2
 * */

class RHEAAgentAgencyPostsWidgetClass extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors : {
                postsFilterForm  : '.rhea-agent-filters-form',
                postsListingWrap : '.rhea-agent-agency-posts-wrap',
                postsAjaxLoader  : '.rhea-ajax-loader'
            }
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings( 'selectors' );
        return {
            $postsFilterForm  : this.$element.find( selectors.postsFilterForm ),
            $postsListingWrap : this.$element.find( selectors.postsListingWrap ),
            $postsAjaxLoader : this.$element.find( selectors.postsAjaxLoader )
        };
    }

    bindEvents() {
        this.loadAgentAgencyPostsWidget();
    }

    loadAgentAgencyPostsWidget( event ) {

        let postsFilterForm  = this.elements.$postsFilterForm,
            postsListingWrap = this.elements.$postsListingWrap,
            postsAjaxLoader  = this.elements.$postsAjaxLoader,
            moreFilters      = postsFilterForm.find( '.more-filters' );

        postsAjaxLoader.hide();

        postsFilterForm.on( 'click', '.more-filters', function(){
            if ( jQuery(this).hasClass('opened') ) {
                jQuery(this).removeClass('opened');
                postsFilterForm.find('.dropdown-fields-wrap').hide(200);
                jQuery(this).siblings('.filter-button').show(200);
            } else {
                jQuery(this).addClass('opened');
                postsFilterForm.find('.dropdown-fields-wrap').show(200);
                jQuery(this).siblings('.filter-button').hide(200);
            }
        } );

        // Object containing the values of the search fields on first page load
        let searchFieldValues = {
            name         : '',
            properties   : '',
            location     : '',
            verification : '',
            rating       : ''
        }

        let triggerAjaxCall = () => {

            if ( moreFilters.hasClass('opened') ) {
                moreFilters.removeClass('opened');
                postsFilterForm.find('.dropdown-fields-wrap').hide(400);
                moreFilters.siblings('.filter-button').show(400);
            }

            searchFieldValues.post_type    = postsFilterForm.find( '.rhea-post-type' ).val();
            searchFieldValues.settings     = postsListingWrap.data( 'card-options' );
            searchFieldValues.name         = postsFilterForm.find( '.posts-filter-name' ).val();
            searchFieldValues.properties   = postsFilterForm.find( '.posts-filter-property-count' ).val();
            searchFieldValues.location     = postsFilterForm.find( '.post-filter-location' ).find( ":selected" ).val();
            searchFieldValues.verification = postsFilterForm.find( '.post-verification:checked' ).val();
            searchFieldValues.rating       = postsFilterForm.find( '.rating-options-wrap label input:checked' ).val();
            searchFieldValues.sort_by      = postsFilterForm.find( '.posts-sort-by' ).find( ":selected" ).val();

            postsListingWrap.hide(100);
            postsListingWrap.html('');

            jQuery.ajax( {
                url     : ajaxurl,
                type    : 'post',
                data    : {
                    action : 'rhea_agent_agency_posts_ajax_filter',
                    ...searchFieldValues
                },
                success : ( response ) => {
                    postsListingWrap.html( response.data.search_results );
                    postsAjaxLoader.hide(100);
                    postsListingWrap.show(100);
                }
            } );
        }

        postsFilterForm.on( 'click', '.filter-posts-button', function(e){
            e.preventDefault();
            triggerAjaxCall();
        } );

        // Resetting selectpicker options upon form reset
        postsFilterForm.on( 'reset', function() {
            setTimeout(function() {
                // Reset the bootstrap-select elements
                postsFilterForm.find('.inspiry_select_picker_trigger').each(function() {
                    jQuery(this).selectpicker('val', jQuery(this).find('option[selected]').val());
                    jQuery(this).selectpicker('refresh');
                });

                triggerAjaxCall();
            }, 0);
        } );
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const RHEAAgentAgencyPostsWidgetHandler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHEAAgentAgencyPostsWidgetClass, { $element } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rhea-ultra-agent-card-widget.default', RHEAAgentAgencyPostsWidgetHandler );
} );