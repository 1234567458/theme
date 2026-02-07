/**
 * RH Properties Filter Widget Class
 *
 * @since 2.4.0
 * */
class RHEAPropertiesFilterWidget extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        return {
            selectors : {
                widgetMainWrapper : '.rhea-properties-filter-widget',
                filterWidgetWrap  : '.property-filters',
                filterWrapper     : '.filter-wrapper',
                statsContainer    : '.rh_pagination__stats',
                filtersWrapper    : '.rhea-properties-filter-widget .filter-wrapper',
                openCollapseWrap  : '.collapse-button',
                mainWrapper       : '#properties-listing'
            }
        };
    }

    getDefaultElements() {
        const settings  = this.getSettings();
        const selectors = settings.selectors;

        return {
            $widgetMainWrapper : this.$element.find( selectors.widgetMainWrapper ),
            $filterWidgetWrap  : this.$element.find( selectors.filterWidgetWrap ),
            $filterWrapper     : this.$element.find( selectors.filterWrapper ),
            $statsContainer    : this.$element.find( selectors.statsContainer ),
            $filtersWrapper    : this.$element.find( selectors.filtersWrapper ),
            $openCollapseWrap  : this.$element.find( selectors.openCollapseWrap ),
            $mainWrapper       : this.$element.find( selectors.mainWrapper )
        };
    }

    bindEvents() {
        this.loadFiltersWidget();
    }

    loadFiltersWidget( event ) {
        // jQuery $ target fix
        $ = jQuery;

        // Setting the necessary variables
        let widgetMainWrapper    = this.elements.$widgetMainWrapper,
            filterWidgetWrap     = this.elements.$filterWidgetWrap,
            filterWrapper        = this.elements.$filterWrapper,
            statsContainer       = this.elements.$statsContainer,
            pageID               = statsContainer.data( 'page-id' ),
            filtersWrapper       = this.elements.$filtersWrapper,
            targetWidgetID       = $( '[data-filterable-listing-widget-id]' ).first().data( 'filterable-listing-widget-id' ) || null,
            openCollapseWrap     = this.elements.$openCollapseWrap,
            propertiesSection    = jQuery( '#rhea-filterable-properties-container' ),
            mainWrapper          = this.elements.$mainWrapper,
            postsPerPage         = mainWrapper.data( 'properties-count' ),
            propertiesLayout     = 'grid',
            propertyCard         = 2,
            clearAllLabel        = 'Clear All', // It is just a fallback. Translatable string is overwriting it later
            targetWidgetWrap     = '',
            targetWidgetSettings = '',
            targetWidgetName     = '',
            currentAjaxRequest   = null;

        if ( 'undefined' !== typeof rheaPropertiesFilterStrings ) {
            clearAllLabel = rheaPropertiesFilterStrings.clear_all_text;
        }

        // Use a selector to find the wrapper div by its data-id
        let targetWidgetWrapperDiv = document.querySelector( `[data-id="${targetWidgetID}"]` );

        if ( targetWidgetWrapperDiv ) {

            targetWidgetWrap = targetWidgetWrapperDiv.querySelector( '.rhea-listing-properties-wrapper' );
            targetWidgetName = targetWidgetWrapperDiv.dataset.widget_type;

            if ( targetWidgetName ) {
                targetWidgetName = targetWidgetName.replace( '.default', '' );
            }

            // Get the JSON settings from the data-settings attribute
            targetWidgetSettings = targetWidgetWrapperDiv.getAttribute( 'data-settings' );

            try {
                // Parse the JSON string into an object
                targetWidgetSettings = JSON.parse( targetWidgetSettings );

                // Use the settings object as needed
            } catch ( error ) {
                console.error( 'Failed to parse settings JSON:', error );
            }
        } else {
            console.error( 'No wrapper found for widget ID:', targetWidgetID );
        }

        // Checking if object exists.
        // Also updating the related variables if each object element is defined
        if ( typeof localizedFilters !== "undefined" ) {
            if ( localizedFilters.filterStrings.clearAll !== undefined ) {
                clearAllLabel = localizedFilters.filterStrings.clearAll;
            }
        }

        // Check if targetWidgetSettings exists and is not null
        if ( typeof targetWidgetSettings !== 'undefined' && targetWidgetSettings !== null && Object.keys( targetWidgetSettings ).length ) {
            // Check if each property exists and is not empty
            if ( targetWidgetSettings.layout ) {
                propertiesLayout = targetWidgetSettings.layout;
            }

            if ( targetWidgetSettings.grid_columns ) {
                propertyCard = targetWidgetSettings.grid_columns;
            }

            if ( targetWidgetSettings.posts_per_page ) {
                postsPerPage = targetWidgetSettings.posts_per_page;
            }
        } else {
            // Handle the case where targetWidgetSettings is undefined or empty
            console.warn( 'Target Widget Settings are not available.' );
        }

        if ( ! parseInt( postsPerPage ) ) {
            postsPerPage = 6;
        }

        // Declaring an object to use globally for filtering properties
        let searchFieldValues = {};

        // Setting posts per page
        searchFieldValues['filter_posts_count'] = postsPerPage;

        // Properties layout
        searchFieldValues['layout'] = propertiesLayout;

        // Property card variation
        searchFieldValues['card_variation'] = propertyCard;

        // ID of the target listing widget
        searchFieldValues['target_widget_id'] = targetWidgetID;

        // Calling the document.ready here after variable initialization based on design variation and other on page targets
        $( document ).ready( function () {

            // Adding filters display wrapper to show on the top of properties on listing templates
            if ( 0 !== targetWidgetWrap.length ) {
                if ( 0 === $( '.rhea-filters-display' ).length ) {
                    $( targetWidgetWrap ).before( '<div class="rhea-filters-display empty"><span class="clear-all-filters">' + clearAllLabel + '</span></div>' );
                }
            }

        } );

        // Controlling the toggle using headings for all filter widget sections
        filterWidgetWrap.on( 'click', '.filter-wrapper h4', function ( e ) {
            $( this ).siblings( '.filter-section' ).slideToggle( 300 );
            $( this ).toggleClass( 'collapsed' );
        } );


        // Controlling opening and collapsing of all filter sections of the sidebar widget
        openCollapseWrap.on( 'click', 'span', function ( e ) {
            let thisClasses = $( this ).attr( 'class' );
            $( this ).addClass( 'hidden' ).siblings( 'span' ).removeClass( 'hidden' );
            if ( thisClasses.includes( 'pop-collapse-all' ) ) {
                filterWrapper.children( 'h4' ).addClass( 'collapsed' ).siblings( '.filter-section' ).hide( 200 );
            } else {
                filterWrapper.children( 'h4' ).removeClass( 'collapsed' ).siblings( '.filter-section' ).show( 200 );
            }
        } );

        var $targetWidgetWrapperDiv = $( targetWidgetWrapperDiv ),
            paginationWrap          = $targetWidgetWrapperDiv.find( '.rhea-listing-pagination-wrap' ),
            filterSortProperties    = targetWidgetWrapperDiv.querySelector( '#sort-properties' );

        if ( filterSortProperties ) {
            // Unbind the existing event handler before changing the ID
            $( '#sort-properties' ).off( 'change' );

            // Change the ID to prevent conflicts
            filterSortProperties.id = 'filter-sort-properties';

            // Now handle changes for the newly modified dropdown separately
            $( '#filter-sort-properties' ).on( 'change', function ( e ) {
                e.preventDefault();
                searchFieldValues['properties_sort_by'] = $( this ).val();

                // Hide all loaders and only show the first one
                $( '.rhea-pagination-loader' ).hide().first().slideDown( 200 );

                paginationWrap.slideUp( 200 );
                rhea_trigger_filters_ajax( searchFieldValues );
            } );
        }



        /* Handling view more functionality for property taxonomy filters and post type filters
       sections of the widget like (type, location, status, features, agents, agencies) */
        filtersWrapper.on( 'click', '.filter-section > a', function ( e ) {
            let thisItem  = $( this ),
                thisClass = thisItem.attr( 'class' );

            if ( thisClass === 'view-more' ) {
                thisItem.hide( 0, function () {
                    thisItem.siblings( '.view-less' ).show();
                } );
                thisItem.siblings( '.items-view-more' ).show( 200 );
            } else {
                thisItem.hide( 0, function () {
                    thisItem.siblings( '.view-more' ).show();
                    thisItem.siblings( '.items-view-more' ).hide( 300 );
                } );
            }
        } );


        // Triggering the taxonomy filters on click (types, locations, statuses, features)
        filtersWrapper.on( 'click', '.terms-list > div > span', function ( event ) {
            event.preventDefault();

            let currentFilter     = $( this ),
                currentSlug       = currentFilter.data( 'term-slug' ),
                parentWrap        = currentFilter.parent( 'div' ).parent( 'div' ),
                sectionHeading    = parentWrap.siblings( 'h4' ),
                currentTaxonomy   = parentWrap.data( 'taxonomy' ),
                currentTitle      = parentWrap.data( 'display-title' ),
                viewMoreBtn       = $( parentWrap ).find( '.view-more' ),
                filterDisplayHTML = '';

            // Showing hidden meta items when clicked on any item
            viewMoreBtn.hide( 0, function () {
                $( this ).siblings( '.view-less' ).show().siblings( '.items-view-more' ).show( 200 );
            } );

            // Actions for already active checkbox
            if ( currentFilter.hasClass( 'active' ) ) {
                currentFilter.removeClass( 'active' );
                let thisIndex = rhea_find_object_value_index( searchFieldValues, currentTaxonomy, currentSlug );
                if ( thisIndex > -1 && searchFieldValues[currentTaxonomy][thisIndex] === currentSlug ) {
                    delete searchFieldValues[currentTaxonomy][thisIndex];
                }

                // Removing this item from filters list at the top of listing
                $( ' .rhea-filters-display #' + currentSlug + '-filter-label' ).remove();

                // Process section heading filters counter
                rhea_process_counter( sectionHeading, 'decrease' );

            } else {

                // Adding selected checkbox value to the set
                currentFilter.addClass( 'active' );
                let taxonomySet = searchFieldValues[currentTaxonomy];
                if ( ! Array.isArray( taxonomySet ) ) {
                    taxonomySet = [];
                }

                // Adding current value to tax set and main object
                taxonomySet.push( currentSlug );
                searchFieldValues[currentTaxonomy] = taxonomySet;

                // Adding filter display on the top of property listing
                let filterDisplayName = currentSlug.replace( /-/g, ' ' );
                filterDisplayName     = rheaCapitalizeWords( filterDisplayName );
                filterDisplayHTML += '<span id="' + currentSlug + '-filter-label" data-key-type="' + currentTaxonomy + '"><span class="filter-name" data-filter-value="' + currentSlug + '">' + currentTitle + ': </span>' + filterDisplayName + '<i></i></span>';
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

                // Process section heading filters counter
                rhea_process_counter( sectionHeading, 'increase' );
            }

            // Ajax trigger after setting the related values
            rhea_trigger_filters_ajax( searchFieldValues );

        } );

        // Triggering filter ajax based on radio list selections. (Price ranges, Area ranges)
        filtersWrapper.on( 'click', '.range-list .radio-wrap input', function ( event ) {

            let currentFilter     = $( this ),
                currentSlug       = currentFilter.parent( '.radio-wrap' ).data( 'meta-name' ),
                currentValue      = currentFilter.val(),
                filtersDisplay    = $( '.rhea-filters-display' ),
                filterDisplayHTML = '',
                filterLabelTitle  = currentFilter.parent( '.radio-wrap' ).data( 'display-title' ),
                filterLabelValue  = currentFilter.data( 'display-value' ),
                currentNumMetaID  = currentSlug + '-filter-label',
                currentValueArray = currentValue.split( ' - ' );

            // Adding price range to main object
            if ( 'price' === currentSlug ) {
                if ( 'All' === currentValue ) {
                    searchFieldValues['price']    = [];
                    searchFieldValues['minPrice'] = [];
                    searchFieldValues['maxPrice'] = [];
                } else {
                    searchFieldValues['price']    = currentValue;
                    searchFieldValues['minPrice'] = parseInt( currentValueArray[0] );
                    searchFieldValues['maxPrice'] = parseInt( currentValueArray[1] );
                }
            }

            // Adding area range to main object
            if ( 'area' === currentSlug ) {
                if ( 'All' === currentValue ) {
                    searchFieldValues['area']    = [];
                    searchFieldValues['minArea'] = [];
                    searchFieldValues['maxArea'] = [];
                } else {
                    searchFieldValues['area']    = currentValue;
                    searchFieldValues['minArea'] = parseInt( currentValueArray[0] );
                    searchFieldValues['maxArea'] = parseInt( currentValueArray[1] );
                }
            }

            // Adding filter display on the top of property listing
            $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
            if ( 'All' !== currentValue ) {
                filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + currentSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + filterLabelTitle + ': </span>' + filterLabelValue + '<i></i></span>';
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );
            }

            // Ajax trigger after setting the related values
            rhea_trigger_filters_ajax( searchFieldValues );

        } );

        /**
         * Managing range sliders
         * */
        $( '.filter-section.range-slider' ).each( function ( e ) {
            let thisSlider      = $( this ),
                sliderTrigger   = thisSlider.children( '.range-slider-trigger' ),
                minRange        = thisSlider.children( '.ranges' ).children( '.min-value' ).data( 'range' ),
                maxRange        = thisSlider.children( '.ranges' ).children( '.max-value' ).data( 'range' ),
                currentMinPrice = thisSlider.children( '.current-values' ).children( '.min-value' ),
                currentMaxPrice = thisSlider.children( '.current-values' ).children( '.max-value' ),
                valueSign       = thisSlider.data( 'values-sign' ),
                signPosition    = thisSlider.data( 'sign-position' ),
                signBefore      = '',
                signAfter       = '';

            // Variables to be used in sub functions.
            // var is used because the variables will be used in nested functions
            var currentSlug      = thisSlider.data( 'meta-name' ),
                currentNumMetaID = currentSlug + '-filter-label',
                filtersDisplay   = '.rhea-filters-display',
                filterLabelTitle = thisSlider.data( 'display-title' ),
                currentValue     = '',
                filterLabelValue = '';

            // Setting before and after sign values
            if ( signPosition === 'after' ) {
                signAfter = valueSign;
            } else {
                signBefore = valueSign;
            }

            // Setting random min max ranges if not set from widget settings. (Unlikely)
            if ( ! minRange ) {
                minRange = 0;
            }
            if ( ! maxRange ) {
                maxRange = 20000;
            }

            let defaultMin = minRange + Math.round( ( maxRange - minRange ) / 4 ), // Increasing the min default value a bit further from start
                defaultMax = maxRange - Math.round( ( maxRange - minRange ) / 4 ); // Decreasing the max default value a bit lesser from start

            // Getting local storage to set the default slider values
            let filterDisplayValues = window.localStorage.getItem( 'filterDisplayValues' );
            if ( null !== filterDisplayValues && 0 < filterDisplayValues.length ) {
                let filterValuesObj = JSON.parse( filterDisplayValues );

                for ( const [key, value] of Object.entries( filterValuesObj ) ) {
                    if ( typeof value.keyType !== 'undefined' && value.keyType === currentSlug ) {
                        let storedValues = value.dataValue.split( " - " );
                        defaultMin       = storedValues[0];
                        defaultMax       = storedValues[1];
                    }
                }
            }

            // Triggering slider for each occurrence
            sliderTrigger.slider( {
                range  : false,
                min    : minRange,
                max    : maxRange,
                values : [defaultMin, defaultMax],
                slide  : function ( event, ui ) {
                    currentMinPrice.html( signBefore + ui.values[0] + signAfter );
                    currentMaxPrice.html( signBefore + ui.values[1] + signAfter );
                },
                stop   : function ( event, ui ) {
                    currentValue                   = sliderTrigger.slider( 'values', 0 ) + ' - ' + sliderTrigger.slider( 'values', 1 );
                    filterLabelValue               = signBefore + sliderTrigger.slider( 'values', 0 ) + signAfter + ' - ' + signBefore + sliderTrigger.slider( 'values', 1 ) + signAfter;
                    searchFieldValues[currentSlug] = currentValue;

                    // Adding filter display on the top of property listing
                    $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
                    let filterDisplayHTML = '<span id="' + currentNumMetaID + '" data-key-type="' + currentSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + filterLabelTitle + ': </span>' + filterLabelValue + '<i></i></span>';
                    $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

                    // Ajax trigger after setting the related values
                    rhea_trigger_filters_ajax( searchFieldValues );
                }
            } );

            currentMinPrice.html( signBefore + sliderTrigger.slider( 'values', 0 ) + signAfter );
            currentMaxPrice.html( signBefore + sliderTrigger.slider( 'values', 1 ) + signAfter );
        } );

        // Triggering filter ajax based on number selections. (Bedrooms, Bathrooms, Garages)
        filtersWrapper.on( 'click', '.number-option-wrap .option-num input', function ( event ) {

            let currentFilter     = $( this ),
                parentWrap        = currentFilter.parent( '.option-num' )
                .parent( '.number-option-wrap' )
                .parent( '.filter-section' ),
                currentMeta       = parentWrap.data( 'meta-name' ),
                currentTitle      = parentWrap.data( 'display-title' ),
                currentValue      = currentFilter.val(),
                relatedMetas      = ['bedrooms', 'bathrooms', 'garages'],
                currentNumMetaID  = currentMeta + '-filter-label',
                filterDisplayHTML = '';

            // Managing buttoned number controls in the main object
            if ( relatedMetas.includes( currentMeta ) ) {
                if ( 0 === currentValue || '0' === currentValue ) {
                    searchFieldValues[currentMeta] = [];
                    $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
                } else {
                    // Adding filter display on the top of property listing
                    searchFieldValues[currentMeta] = parseInt( currentValue );
                    $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
                    filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + currentMeta + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentValue + '<i></i></span>';
                    $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );
                }
            }

            // Ajax trigger after setting the related values
            rhea_trigger_filters_ajax( searchFieldValues );

        } );


        // Triggering the post type filters. (agents, agencies)
        filtersWrapper.on( 'click', '.posts-list > div > div', function ( event ) {

            // Prevent the default event of click just in case
            event.preventDefault();

            let currentFilter     = $( this ),
                currentSlug       = currentFilter.data( 'post-id' ),
                parentWrap        = currentFilter.parent( 'div' ).parent( 'div' ),
                sectionHeading    = parentWrap.siblings( 'h4' ),
                currentPostType   = parentWrap.data( 'meta-name' ),
                currentTitle      = parentWrap.data( 'display-title' ),
                viewMoreBtn       = $( parentWrap ).find( '.view-more' ),
                currentTargetID   = currentSlug.split( '|' ),
                filterDisplayHTML = '';

            currentTargetID = currentTargetID[1];
            currentTargetID = currentTargetID.replace( /\s+/g, '-' ).toLowerCase();

            // Showing hidden checkbox items upon click on any item
            viewMoreBtn.hide( 0, function () {
                $( this ).siblings( '.view-less' ).show().siblings( '.items-view-more' ).show( 200 );
            } );

            // Managing checkboxes of post type filters
            if ( currentFilter.hasClass( 'active' ) ) {

                currentFilter.removeClass( 'active' );

                let thisIndex = rhea_find_object_value_index( searchFieldValues, currentPostType, currentSlug );

                if ( thisIndex > -1 && searchFieldValues[currentPostType][thisIndex] === currentSlug ) {
                    delete searchFieldValues[currentPostType][thisIndex];
                }

                // Removing this item from filters list at the top of listing
                $( ' .rhea-filters-display #' + currentTargetID + '-filter-label' ).remove();

                // Process section heading filters counter
                rhea_process_counter( sectionHeading, 'decrease' );
            } else {

                // Adding selected checkbox value to the set
                currentFilter.addClass( 'active' );
                let postTypeSet = searchFieldValues[currentPostType];
                if ( ! Array.isArray( postTypeSet ) ) {
                    postTypeSet = [];
                }
                postTypeSet.push( currentSlug );
                searchFieldValues[currentPostType] = postTypeSet;

                // Adding filter display on the top of property listing
                let currentPTValue = currentSlug.split( '|' );
                filterDisplayHTML += '<span id="' + currentTargetID + '-filter-label" data-key-type="' + currentPostType + '"><span class="filter-name" data-filter-value="' + currentSlug + '">' + currentTitle + ': </span>' + currentPTValue[1] + '<i></i></span>';
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

                // Process section heading filters counter
                rhea_process_counter( sectionHeading, 'increase' );
            }

            // Ajax trigger after setting the related values
            rhea_trigger_filters_ajax( searchFieldValues );

        } );


        // Triggering filter ajax based property ID field in sidebar widget
        let typeDelayTimer; // Timer delay variable to be used later on
        filtersWrapper.on( 'keyup', '.input-filter .input-wrap #property-id', function ( event ) {

            let thisItem          = $( this ),
                currentMeta       = thisItem.parent( 'p' ).data( 'meta-name' ),
                currentValue      = thisItem.val(),
                currentTitle      = thisItem.parent( 'p' ).parent( 'div' ).siblings( 'h4' ).html(),
                currentNumMetaID  = currentMeta + '-filter-label',
                filterDisplayHTML = '';

            // Clearing previous keyup requests
            clearTimeout( typeDelayTimer );

            // Input field request after 1 second timeout to keep the request minimum
            typeDelayTimer = setTimeout( function () {
                searchFieldValues['propertyID'] = thisItem.val();

                // Adding filter display on the top of property listing
                $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
                if ( 0 < currentValue.length ) {
                    filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + currentMeta + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentValue + '<i></i></span>';
                }
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

                // Ajax trigger after setting the related values
                rhea_trigger_filters_ajax( searchFieldValues );
            }, 1000 );

        } );


        // Triggering additional detail input text type filters
        filtersWrapper.on( 'keyup', '.additional-item .input-wrap input', function ( event ) {

            // Prevent the default event of click just in case
            event.preventDefault();

            let thisItem           = $( this ),
                fieldSlug          = thisItem.attr( 'name' ),
                currentTitle       = thisItem.siblings( 'label' ).html(),
                currentNumMetaID   = fieldSlug + '-filter-label',
                currentValue       = thisItem.val(),
                filtersDisplayWrap = $( '.rhea-filters-display' ),
                filterDisplayHTML  = '';

            // Clearing previous keyup requests
            clearTimeout( typeDelayTimer );

            // Input field request after 1 second timeout to keep the request minimum
            typeDelayTimer = setTimeout( function () {

                searchFieldValues[fieldSlug] = currentValue;

                // Adding filter display on the top of property listing
                $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
                if ( 0 < currentValue.length ) {
                    filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + fieldSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentValue + '<i></i></span>';
                }
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

                // Ajax trigger after setting the related filters
                rhea_trigger_filters_ajax( searchFieldValues );
            }, 1000 );

        } );

        // Triggering additional detail select type dropdown filters
        filtersWrapper.on( 'change', '.additional-item .select-wrap select', function ( event ) {

            let thisItem          = $( this ),
                fieldSlug         = thisItem.attr( 'name' ),
                currentTitle      = thisItem.siblings( 'label' ).html(),
                currentNumMetaID  = fieldSlug + '-filter-label',
                currentValue      = thisItem.val(),
                currentDisplay    = '',
                fieldType         = thisItem.parent( 'p' ).data( 'field-type' ), //capitalizeWords( currentValue ),
                filterDisplayHTML = '';

            if ( 'select' === fieldType ) {
                let multiSelect = thisItem.parent( 'p' ).data( 'multiselect' );
                if ( 'yes' === multiSelect ) {
                    let totalValues        = currentValue.length - 1;
                    let multiSelectedItems = [];
                    $.each( currentValue, function ( key, value ) {
                        currentDisplay += capitalizeWords( value );
                        if ( key < totalValues ) {
                            currentDisplay += ', ';
                        }
                        multiSelectedItems.push( value );
                    } );
                    searchFieldValues[fieldSlug] = multiSelectedItems;

                } else {
                    currentDisplay               = capitalizeWords( currentValue );
                    searchFieldValues[fieldSlug] = currentValue;
                }
            } else {
                currentDisplay               = capitalizeWords( currentValue );
                searchFieldValues[fieldSlug] = currentValue;
            }

            // Adding filter display on the top of property listing
            $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
            if ( 0 < currentValue.length ) {
                filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + fieldSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentDisplay + '<i></i></span>';
            }
            $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

            // Ajax trigger after setting the related filters
            rhea_trigger_filters_ajax( searchFieldValues );

        } );

        // Triggering additional detail select type dropdown filters
        filtersWrapper.on( 'click', '.additional-item .multi-select-wrap .ad-select-item', function ( event ) {

            let thisItem           = $( this ),
                parentWrap         = thisItem.parent( 'div' ).parent( 'div' ),
                fieldSlug          = parentWrap.data( 'meta-key' ),
                currentTitle       = parentWrap.siblings( 'h4' ).html(),
                currentDisplay     = '',
                filterDisplayHTML  = '',
                multiSelectedItems = [];

            if ( thisItem.hasClass( 'active' ) ) {
                thisItem.removeClass( 'active' );
                let thisValue = thisItem.data( 'filter-value' );
                $( filterDisplayWrap + ' #' + thisValue + '-filter-label' ).remove();
            } else {
                thisItem.addClass( 'active' );
            }

            var selectedValues = parentWrap.find( '.ad-select-item.active' );
            $.each( selectedValues, function ( key, value ) {
                let currentValue = value.dataset.filterValue;
                currentDisplay += capitalizeWords( currentValue );
                if ( key < selectedValues.length - 1 ) {
                    currentDisplay += ', ';
                }
                multiSelectedItems.push( currentValue );
            } );
            searchFieldValues[fieldSlug] = multiSelectedItems;

            for ( let [key, currentValue] of Object.entries( multiSelectedItems ) ) {

                let currentNumMetaID = currentValue + '-filter-label',
                    currentDisplay   = capitalizeWords( currentValue );
                selectedValues.push( currentValue );

                $( '.rhea-filters-display' + ' .ad-multiselect-display-item' ).remove();
                if ( 0 < currentValue.length && 0 === $( filterDisplayWrap + ' #' + currentNumMetaID ).length ) {
                    filterDisplayHTML += '<span id="' + currentNumMetaID + '" class="ad-multiselect-display-item" data-key-type="' + fieldSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentDisplay + '<i></i></span>';
                }
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );
            }

            // Ajax trigger after setting the related filters
            rhea_trigger_filters_ajax( searchFieldValues );

        } );


        // Triggering additional detail radio field type filters
        filtersWrapper.on( 'click', '.additional-item .radio-wrap input', function ( event ) {

            let thisItem          = $( this ),
                fieldSlug         = $( this ).attr( 'name' ),
                currentTitle      = thisItem.parent( 'p' ).siblings( 'h5' ).html(),
                currentNumMetaID  = fieldSlug + '-filter-label',
                currentValue      = thisItem.val(),
                currentDisplay    = capitalizeWords( currentValue ),
                filterDisplayHTML = '';

            // Assigning the current range value to global object
            searchFieldValues[fieldSlug] = currentValue;

            // Adding filter display on the top of property listing
            $( '.rhea-filters-display' + ' #' + currentNumMetaID ).remove();
            if ( 0 < currentValue.length ) {
                filterDisplayHTML += '<span id="' + currentNumMetaID + '" data-key-type="' + fieldSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + currentTitle + ': </span>' + currentDisplay + '<i></i></span>';
            }
            $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );

            // Ajax trigger after setting the related filters
            rhea_trigger_filters_ajax( searchFieldValues );

        } );


        // Triggering additional detail checkbox field type filters
        filtersWrapper.on( 'click', '.additional-item .checkbox-wrap input', function ( event ) {

            let thisItem          = $( this ),
                thisWrap          = thisItem.parent( 'p' ).parent( '.checkbox-filter' ),
                fieldSlug         = $( this ).attr( 'name' ),
                currentTitle      = thisItem.parent( 'p' ).siblings( 'h5' ),
                currentTitleVal   = currentTitle.html(),
                inputValues       = $( thisWrap ).find( 'input' ),
                selectedValues    = [],
                filterDisplayHTML = '';

            for ( let [key, checkbox] of Object.entries( inputValues ) ) {
                $( filterDisplayWrap + ' #' + checkbox.value + '-filter-label' ).remove();
            }

            let selectedCheckBoxes = Array.prototype.slice.call( inputValues ).filter( ch => ch.checked === true );

            // Adding/Creating mini counter to additional checkboxes heading after check if container already exists
            if ( 0 < selectedCheckBoxes.length ) {
                if ( 0 < currentTitle.children( '.counter' ).length ) {
                    currentTitle.children( '.counter' )
                    .html( '(' + '<i>' + selectedCheckBoxes.length + '</i> ' + localizedFilters.filterStrings.selected + ')' );
                } else {
                    currentTitle.append( '<span class="counter">(' + '<i>' + selectedCheckBoxes.length + '</i> ' + localizedFilters.filterStrings.selected + ')</span>' );
                }
                currentTitle.children( '.counter' ).show();
            } else {
                currentTitle.children( '.counter' ).hide();
                $( filterDisplayWrap + ' .ad-checkbox-display-item' ).remove();
            }

            for ( let [key, checkbox] of Object.entries( selectedCheckBoxes ) ) {
                let currentValue     = checkbox.value,
                    currentNumMetaID = currentValue + '-filter-label',
                    currentDisplay   = capitalizeWords( currentValue );
                selectedValues.push( currentValue );

                $( '.rhea-filters-display' + ' .ad-checkbox-display-item' ).remove();
                if ( 0 < currentValue.length && 0 === $( filterDisplayWrap + ' #' + currentNumMetaID ).length ) {
                    let OptionTitle = currentTitleVal.split( '<span' )[0];
                    filterDisplayHTML += '<span id="' + currentNumMetaID + '" class="ad-checkbox-display-item" data-key-type="' + fieldSlug + '"><span class="filter-name" data-filter-value="' + currentValue + '">' + OptionTitle + ': </span>' + currentDisplay + '<i></i></span>';
                }
                $( '.rhea-filters-display' ).children( '.clear-all-filters' ).before( filterDisplayHTML );
            }

            searchFieldValues[fieldSlug] = selectedValues;
            rhea_trigger_filters_ajax( searchFieldValues );

        } );

        // Triggering the properties listing bottom pagination for filtered properties
        $( document ).on( 'click', '.rhea-listing-pagination-wrap .pagination a', function ( event ) {

            // Prevent default <a> tag behaviour
            event.preventDefault();

            let $this = $( this );

            if ( $this.hasClass( 'current' ) ) {
                return;
            }

            let paginationWrap = $targetWidgetWrapperDiv.find( '.rhea-listing-pagination-wrap' ),
                pageNum        = parseInt( $this.data( 'page' ) ),
                currentPageNum = parseInt( paginationWrap.data( 'paged-now' ) ),
                pagiLoader     = $targetWidgetWrapperDiv.find( '.rhea-pagination-loader' );

            // Scroll to widget wrapper smoothly
            targetWidgetWrapperDiv.scrollIntoView( {
                behavior : 'smooth',
                block    : 'start'
            } );

            // Start dimming out the property section before the AJAX call
            propertiesSection.css( {
                opacity    : 0.3,
                transition : 'opacity 300ms ease'
            } );

            pagiLoader.slideDown( 100 );
            searchFieldValues['pagination_trigger'] = true;
            searchFieldValues['page']               = pageNum;
            pagiLoader.css( 'height', '32px' );

            $.ajax( {
                url     : ajaxurl,
                type    : 'post',
                data    : {
                    action                 : 'rhea_properties_ajax_filter',
                    filterValues           : searchFieldValues,
                    target_widget_settings : targetWidgetSettings,
                    widget_name            : targetWidgetName
                },
                success : ( response ) => {

                    // After loader is hidden, update content and fade it back in
                    propertiesSection.html( response.data.search_results );

                    // Trigger the fade-in
                    requestAnimationFrame( () => {
                        propertiesSection.css( 'opacity', 1 );
                    } );

                    pagiLoader.slideUp( 200 );

                    // Handle pagination HTML if present
                    if ( Array.isArray( response.data.pagination ) || typeof response.data.pagination === 'string' ) {
                        if ( response.data.pagination.length > 0 ) {
                            targetWidgetWrap.insertAdjacentHTML( 'beforeend', response.data.pagination );
                        }
                    }

                    let paginationWrap = $targetWidgetWrapperDiv.find( '.rhea-listing-pagination-wrap' );
                    currentPageNum     = response.data.search_query.query.paged;
                    propertiesSection.removeClass( 'loading' );
                    paginationWrap.children( '.pagination' ).children( 'a' ).removeClass( 'current' );
                    paginationWrap.children( '.pagination' ).children( ':nth-child(' + currentPageNum + ')' ).addClass( 'current' );

                    // Map update via second AJAX call
                    $.ajax( {
                        url     : ajaxurl,
                        type    : 'post',
                        data    : {
                            action       : 'rhea_map_properties_data',
                            filterValues : searchFieldValues
                        },
                        success : ( response ) => {
                            if ( response.data ) {
                                const rheaUpdateMapData = $.Event( 'rheaUpdateMapData', {
                                    mapProperties : JSON.stringify( response.data )
                                } );
                                $( document ).trigger( rheaUpdateMapData );
                            }

                            searchFieldValues['pagination_trigger'] = false;
                        }
                    } );
                }
            } );
        } );


        $( document ).on( 'click', '.rh-properties-widget-v14-toggle-layout a', function ( e ) {
            e.preventDefault(); // stop default navigation

            let $this         = $( this ),
                href          = $this.attr( 'href' ),
                layoutValue   = href.substring( href.lastIndexOf( '=' ) + 1 ),
                columns_count = 1;

            if ( $this.hasClass( 'current' ) ) {
                return;
            }

            $this.addClass( 'current' ).siblings( 'a' ).removeClass( 'current' );
            searchFieldValues['current-layout'] = layoutValue;

            propertiesSection.addClass( 'loading' ).css( 'opacity', '0' ).one( 'transitionend', function () {
                if ( $targetWidgetWrapperDiv.hasClass( 'elementor-widget-rh-properties-widget-v14' ) ) {

                    $targetWidgetWrapperDiv.find( '.rh-properties-widget-v14-list-layout, .rh-properties-widget-v14-grid-layout' ).removeClass( 'rh-properties-widget-v14-list-layout rh-properties-widget-v14-grid-layout' ).addClass( 'rh-properties-widget-v14-' + layoutValue + '-layout' );
                    $targetWidgetWrapperDiv.find( '.rh-properties-container-inner' ).removeClass( 'rh-properties-widget-v14-list-layout rh-properties-widget-v14-grid-layout' ).addClass( 'rh-properties-' + layoutValue + '-columns' );

                    if ( 'grid' === layoutValue ) {
                        columns_count = 2;
                        if ( undefined !== targetWidgetSettings.grid_columns ) {
                            columns_count = targetWidgetSettings.grid_columns;
                        }
                    }

                    $targetWidgetWrapperDiv.find( '.rh-properties-container-inner' ).removeClass( function ( index, className ) {
                        return ( className.match( /rh-properties-(grid|list)-columns-\d+/g ) || [] ).join( ' ' );
                    } ).addClass( 'rh-properties-' + layoutValue + '-columns-' + columns_count );
                }

                rhea_trigger_filters_ajax( searchFieldValues );
            } );

        } );


        // Clearing all the filters
        $( document ).on( 'click', '.rhea-filters-display .clear-all-filters', function () {

            // Saving existing sortby value
            let currentSortBy = searchFieldValues['properties_sort_by'],
                localItems    = JSON.parse( localStorage.getItem( 'filterSectionValues' ) ),
                layout        = localItems['current-layout'] ?? localItems.layout;

            for ( let filter in searchFieldValues ) {
                searchFieldValues = {
                    filter_posts_count : postsPerPage,
                    properties_sort_by : currentSortBy
                };
            }

            // Setting current layout to get rid of any issues
            searchFieldValues['current-layout'] = layout;

            let $this = $( this );
            $this.siblings( 'span' ).remove();
            $this.removeClass( 'active' );
            $( '.rhea-filters-display .clear-all-filters' ).removeClass( 'active' );
            $this.closest('.rhea-filters-display').addClass( 'empty' );

            // Resetting all input fields in filters widget
            $( filterWidgetWrap ).find( 'input[type=text]' ).val( '' );

            // Resetting all select dropdown fields in filters widget
            $( filterWidgetWrap ).find( 'select' ).prop( 'selectedIndex', 0 );

            // Resetting all radio fields in filters widget
            $( filterWidgetWrap ).find( '.radio-wrap:first-of-type input' ).prop( 'checked', true );

            // Resetting all taxonomy terms and post type items in filters widget
            $( filterWidgetWrap ).find( '.posts-list span, .posts-list .pt-item, .terms-list span' ).removeClass( 'active' );

            // Resetting all numbers fields in filters widget
            $( filterWidgetWrap ).find( '.option-num:first-child input' ).prop( 'checked', true );

            // Resetting all checkbox fields in filters widget
            $( filterWidgetWrap ).find( '.cb-wrap input' ).prop( 'checked', 0 );

            // Resetting all addition multiselect tiles in filters widget
            $( filterWidgetWrap ).find( '.ad-select-item.multiselect' ).removeClass( 'active' );

            // Triggering the ajax call after clearing all filters
            rhea_trigger_filters_ajax( searchFieldValues );

            // Resting heading counters of all the filter sections in the widget
            $( '.filter-wrapper > h4' ).children( '.counter' ).remove();

            // Clearing up all available range sliders
            $( '.filter-section.range-slider' ).each( function ( e ) {
                let $this = $( this ),
                    sliderTrigger = $this.children( '.range-slider-trigger' ),
                    minRange      = $this.children( '.ranges' ).children( '.min-value' ).data( 'range' ),
                    maxRange      = $this.children( '.ranges' ).children( '.max-value' ).data( 'range' );

                if ( ! minRange ) {
                    minRange = 0;
                }
                if ( ! maxRange ) {
                    maxRange = 20000;
                }

                let defaultMin = minRange + Math.round( ( maxRange - minRange ) / 4 ), // Increasing the min default value a bit further from start
                    defaultMax = maxRange - Math.round( ( maxRange - minRange ) / 4 ); // Decreasing the max default value a bit lesser from start
                sliderTrigger.slider( "values", [defaultMin, defaultMax] );
            } );
        } );


        /**
         * This function triggers the ajax call to fetch properties
         * according to the selected/provided filters
         *
         * @since 2.4.0
         *
         * @param searchFieldValues array
         *
         * */
        let rhea_trigger_filters_ajax = ( searchFieldValues ) => {

            if ( typeof targetWidgetSettings === 'undefined' || targetWidgetSettings === null || ! Object.keys( targetWidgetSettings ).length ) {
                return;
            }

            // To reset pagination to first after re-filtering of the properties
            if ( 0 !== Object.keys( searchFieldValues ).length ) {
                $( ' .rhea-filters-display .clear-all-filters' ).addClass( 'active' );
                searchFieldValues['page'] = 1;
            }

            let resultCountElement = targetWidgetWrapperDiv.querySelector( '.properties-result-count' );
            let spanCount          = $targetWidgetWrapperDiv.find( '.rhea-filters-display' ).children( 'span' ).length;
            let pagiLoader         = $targetWidgetWrapperDiv.find( '.rhea-pagination-loader' );

            if ( 2 > spanCount ) {
                $targetWidgetWrapperDiv.find( '.rhea-filters-display' ).addClass( 'empty' );
                $targetWidgetWrapperDiv.find( '.rhea-filters-display .clear-all-filters' ).removeClass( 'active' );
            } else {
                $targetWidgetWrapperDiv.find( '.rhea-filters-display' ).removeClass( 'empty' );
            }

            // Removing pagination trigger to regenrate it
            searchFieldValues['pagination_trigger'] = false;

            // Setting search field values to local storage
            localStorage.setItem( 'filterSectionValues', JSON.stringify( searchFieldValues ) );

            // Updating filters tags on top of properties listing based on local storage values
            rhea_update_storage_display();

            propertiesSection.addClass( 'loading' ).css( 'opacity', '0.2' );

            pagiLoader.slideDown( 100, function(){
                // Remove all existing pagination wrappers to prevent duplicates
                document.querySelectorAll('.rhea-pagination-loader').forEach(el => el.remove());
            } );

            // Remove all existing pagination wrappers to prevent duplicates
            document.querySelectorAll('.rhea-listing-pagination-wrap').forEach(el => el.remove());

            // Abort previous request if still active
            if ( currentAjaxRequest && currentAjaxRequest.readyState !== 4 ) {
                currentAjaxRequest.abort();
            }

            currentAjaxRequest = $.ajax( {
                url     : ajaxurl,
                type    : 'post',
                data    : {
                    action                 : 'rhea_properties_ajax_filter',
                    filterValues           : searchFieldValues,
                    target_widget_settings : targetWidgetSettings,
                    widget_name            : targetWidgetName
                },
                success : ( response ) => {
                    propertiesSection.html( response.data.search_results );

                    if ( Array.isArray( response.data.pagination ) || typeof response.data.pagination === 'string' ) {
                        if ( response.data.pagination.length > 0 ) {
                            $( '.rhea-listing-pagination-wrap' ).remove();
                            targetWidgetWrap.insertAdjacentHTML( 'beforeend', response.data.pagination );
                        }
                    }

                    if ( resultCountElement ) {
                        let spanCountElement = resultCountElement.querySelector( 'span' );

                        if ( spanCountElement ) {
                            if ( 0 < response.data.total_properties ) {
                                spanCountElement.textContent = response.data.total_properties;
                            } else {
                                spanCountElement.textContent = 0;
                            }
                        }
                    }

                    $( '.rh_page > .rh_pagination, .rh_page > .svg-loader' ).remove();
                    propertiesSection.removeClass( 'loading' ).css( 'opacity', '1' );

                    if ( undefined !== response.data.search_query ) {
                        let searchQuery = response.data.search_query.query_vars,
                            currentPage = 1,
                            stats       = response.data.search_query,
                            per_page    = searchQuery.posts_per_page,
                            foundPosts  = stats.found_posts;

                        if ( undefined !== searchQuery.page && 0 < parseInt( searchQuery.page ) ) {
                            currentPage = searchQuery.page;
                        }

                        let currentPostsStart = ( currentPage - 1 ) * per_page + 1;
                        let currentPostsEnd   = per_page * currentPage;

                        if ( currentPostsEnd >= foundPosts ) {
                            currentPostsEnd = foundPosts;
                        }

                    }

                    // Binding Favorites & Compare Properties Features
                    realhomes_update_favorites();
                    realhomes_update_compare_properties();

                    // Trigger the map according to the properties object
                    $.ajax( {
                        url     : ajaxurl,
                        type    : 'post',
                        data    : {
                            action       : 'rhea_map_properties_data',
                            filterValues : searchFieldValues
                        },
                        success : ( response ) => {

                            let propertiesMapData = '[]';

                            if ( undefined !== response.data ) {
                                propertiesMapData = JSON.stringify( response.data );
                            }

                            const rheaUpdateMapData = $.Event( 'rheaUpdateMapData', {
                                mapProperties : propertiesMapData
                            } );
                            $( document ).trigger( rheaUpdateMapData );
                        }
                    } );
                }
            } );

        }


        /**
         * Getting the index of an array item based of the given key and value
         *
         * @since 2.4.0
         *
         * @param targetItem mixed
         * @param key string
         * @param value string
         *
         * @return number
         *
         */
        let rhea_find_object_value_index = ( targetItem, key, value ) => {
            for ( let i = 0; i < targetItem[key].length; i += 1 ) {
                if ( targetItem[key][i] === value ) {
                    return i;
                }
            }
            return -1;
        }

        // Handling close trigger for displayed filter on the top of properties
        $( document ).on( 'click', '.rhea-filters-display span i', function () {
            let thisItem      = $( this ).parent( 'span' );
            let thisValue     = $( this ).siblings( 'span' ).data( 'filter-value' );
            let thisFilterKey = $( this ).parent( 'span' ).data( 'key-type' );

            // Remove entry with current key and value
            for ( let [filter, values] of Object.entries( searchFieldValues ) ) {
                if ( thisFilterKey === filter ) {
                    if ( Array.isArray( values ) ) {
                        for ( let [key, value] of Object.entries( values ) ) {
                            if ( value === thisValue ) {
                                delete searchFieldValues[filter][key];
                            }
                        }
                    } else {
                        if ( values === thisValue || parseInt( values ) === parseInt( thisValue ) ) {
                            delete searchFieldValues[filter];
                        }
                    }
                    thisItem.remove();
                    rhea_trigger_filters_ajax( searchFieldValues );
                }
            }

            // TODO: This functionality should handle an object intelligently to minimize the code and do it more efficiently

            let filterSection = $( '.property-filters .filter-section' );

            // Going through each filter section
            for ( let i = 0; i < filterSection.length; i++ ) {

                // Checking all taxonomy terms checkbox lists sections (types,locations,statuses,features)
                if ( filterSection[i].className.includes( 'terms-list' ) ) {

                    // Getting the taxonomy name from section data attribute
                    if ( thisFilterKey === filterSection[i].dataset.taxonomy ) {
                        let filterTargetWraps = filterSection[i].children;

                        // Removing/Decreasing filters' section counter
                        rhea_process_filter_counter_display_tag_removal( filterSection[i] );

                        for ( let j = 0; j < filterTargetWraps.length; j++ ) {
                            if ( filterTargetWraps[j] instanceof HTMLDivElement ) {
                                let filterTargetItems = filterTargetWraps[j].children;
                                for ( let k = 0; k < filterTargetItems.length; k++ ) {
                                    let thisTermValue = filterTargetItems[k].dataset.termSlug;
                                    if ( thisTermValue === thisValue ) {
                                        filterTargetItems[k].className = '';
                                    }
                                }
                            }
                        }
                    }
                    // Checking all post types checkbox lists sections (agents, agencies)
                } else if ( filterSection[i].className.includes( 'posts-list' ) ) {

                    // Getting the meta name from section data attribute
                    if ( thisFilterKey === filterSection[i].dataset.metaName ) {
                        let filterTargetWraps = filterSection[i].children;

                        // Removing/Decreasing filters' section counter
                        rhea_process_filter_counter_display_tag_removal( filterSection[i] );

                        for ( let j = 0; j < filterTargetWraps.length; j++ ) {
                            if ( filterTargetWraps[j] instanceof HTMLDivElement ) {
                                let filterTargetItems = filterTargetWraps[j].children;
                                for ( let k = 0; k < filterTargetItems.length; k++ ) {
                                    let thisPostValue = filterTargetItems[k].dataset.postId;
                                    if ( thisPostValue === thisValue ) {
                                        filterTargetItems[k].classList.remove( "active" );
                                    }
                                }
                            }
                        }
                    }
                    // Checking all buttons form number lists sections (beds, baths, garages)
                } else if ( filterSection[i].className.includes( 'buttons-list' ) ) {
                    let thisMetaName     = filterSection[i].dataset.metaName,
                        filterTargetWrap = filterSection[i].children;

                    for ( let j = 0; j < filterTargetWrap.length; j++ ) {
                        let thisMetaWrap = filterTargetWrap[j].children;
                        for ( let k = 0; k < thisMetaWrap.length; k++ ) {
                            if ( undefined !== thisMetaName && thisMetaName === thisFilterKey ) {

                                // Unchecking the checkbox if the value is matched with the clicked one
                                $( thisMetaWrap[0] ).find( 'input' ).prop( 'checked', true );
                            }
                        }
                    }
                    // Checking all radio input type ranges lists (price, area)
                } else if ( filterSection[i].className.includes( 'range-list' ) ) {
                    let filterTargetWrap = filterSection[i].children,
                        targetMetaName   = filterTargetWrap[0].dataset.metaName;

                    if ( targetMetaName === thisFilterKey ) {

                        // Resetting the buttons to first item which is usually 'All' for these sections
                        $( filterTargetWrap[0] ).find( 'input' ).prop( 'checked', true );
                    }
                    // Checking text input filters (property id)
                } else if ( filterSection[i].className.includes( 'range-slider' ) ) {
                    let filterTargetWrap = filterSection[i].children,
                        targetMetaName   = filterSection[i].dataset.metaName;

                    if ( targetMetaName === thisFilterKey ) {
                        let minRange = filterTargetWrap[0].children[0].dataset.range,
                            maxRange = filterTargetWrap[0].children[1].dataset.range;

                        if ( ! minRange ) {
                            minRange = 0;
                        }
                        if ( ! maxRange ) {
                            maxRange = 20000;
                        }

                        let defaultMin = parseInt( minRange ) + Math.round( ( maxRange - minRange ) / 4 ), // Increasing the min default value a bit further from start
                            defaultMax = parseInt( maxRange ) - Math.round( ( maxRange - minRange ) / 4 ); // Decreasing the max default value a bit lesser from start

                        // Resetting the related slider
                        $( filterTargetWrap[1] ).slider( "values", [defaultMin, defaultMax] );
                    }
                    // Checking text input filters (property id)
                } else if ( filterSection[i].className.includes( 'input-filter' ) ) {
                    let filterTargetWrap = filterSection[i].children,
                        targetMetaName   = filterTargetWrap[0].dataset.metaName;

                    // Removing the input value based on removed filter display tag
                    if ( targetMetaName === thisFilterKey ) {
                        $( filterTargetWrap[0] ).find( 'input' ).val( '' );
                    }
                    // Checking each additional field items
                } else if ( filterSection[i].className.includes( 'additional-items' ) ) {
                    let filterTargetWrap = filterSection[i].children;
                    for ( let j = 0; j < filterTargetWrap.length; j++ ) {
                        if ( filterTargetWrap[j].className.includes( 'additional-item' ) ) {

                            let thisFieldWrap = filterTargetWrap[j].children;
                            for ( let k = 0; k < thisFieldWrap.length; k++ ) {

                                // Checking if the additional field type of field is text type input
                                if ( thisFieldWrap[k].className.includes( 'input-filter' ) ) {
                                    let fieldSlug = thisFieldWrap[k].children[1].id;
                                    if ( fieldSlug === thisFilterKey ) {

                                        // Removing the input value upon reset
                                        $( thisFieldWrap[k] ).find( 'input' ).val( '' );
                                    }
                                    // Checking if the addition field type is a select dropdown
                                } else if ( thisFieldWrap[k].className.includes( 'select-filter' ) ) {
                                    let fieldSlug = thisFieldWrap[k].children[1].id;
                                    if ( fieldSlug === thisFilterKey ) {

                                        // Resetting the dropdown option to first item which is usually 'None' for these sections
                                        thisFieldWrap[k].children[1].selectedIndex = 0;
                                    }
                                    // Checking if the additional field type is multiselect
                                } else if ( thisFieldWrap[k].className.includes( 'multi-select-wrap' ) ) {
                                    let fieldSlug = thisFieldWrap[k].children[1].dataset.metaKey;
                                    if ( fieldSlug === thisFilterKey ) {
                                        let thisChildElements = thisFieldWrap[k].children[1].querySelectorAll( '*' );
                                        // Resetting all the multiselect options
                                        thisChildElements.forEach( el => el.classList.remove( 'active' ) );
                                    }
                                    // Checking if the additional field type is radio type input
                                } else if ( thisFieldWrap[k].className.includes( 'radio-filter' ) ) {
                                    let fieldSlug = thisFieldWrap[k].dataset.fieldSlug;
                                    if ( fieldSlug === thisFilterKey ) {

                                        // Resetting the radio to first item which is the default one usually
                                        $( thisFieldWrap[k] ).find( 'p:first-of-type input' ).prop( 'checked', true );
                                    }
                                    // Checking if additional field type is checkbox type input
                                } else if ( thisFieldWrap[k].className.includes( 'checkbox-filter' ) ) {
                                    let fieldSlug    = thisFieldWrap[k].dataset.fieldSlug;
                                    let filterCBWrap = thisFieldWrap[k].children;
                                    for ( let l = 0; l < filterCBWrap.length; l++ ) {
                                        if ( filterCBWrap[l].className.includes( 'cb-wrap' ) ) {
                                            let thisCBValue = filterCBWrap[l].children[0].value;
                                            if ( thisCBValue === thisValue ) {

                                                // Unchecking the checkbox field if the value is matched with the clicked one
                                                filterCBWrap[l].children[0].checked = false;
                                            }
                                        }

                                    } // Ending for loop of filterCBWrap
                                } // Ending checkbox filter else condition of if statement
                            } // Ending for loop of thisFieldWrap addition field sections
                        } // Ending addition item wrap if condition
                    } // Ending for loop for filterTargetWrap of additional items
                } // Ending if statement of additional items section
            } // Ending the filterSections for loop
        } );

        /**
         * Capitalize the given string
         *
         * @since 2.4.0
         *
         * @param string string
         *
         * @return string
         *
         */
        function rheaCapitalizeWords( string ) {

            return string.toLowerCase().split( ' ' ).map( function ( word ) {
                return ( word.charAt( 0 ).toUpperCase() + word.slice( 1 ) );
            } ).join( ' ' );

        }

        /**
         * Process filter section heading counter for sections containing multi-select options
         *
         *
         * @since 2.4.0
         *
         * @param target
         * @param type
         *
         * @return null
         */
        function rhea_process_counter( target, type ) {

            let counter      = 0,
                selectedText = 'Selected'; // It is just a fallback. Translatable string is overwriting it later

            if ( 'undefined' !== typeof rheaPropertiesFilterStrings ) {
                selectedText = rheaPropertiesFilterStrings.selected_text;
            }

            // Managing counter value if counter container already exists
            if ( 0 < target.children( '.counter' ).length ) {

                counter = parseInt( target.children( '.counter' ).children( 'i' ).html() );

                if ( 'decrease' === type ) {
                    counter--;
                } else {
                    counter++;
                }

                target.children( '.counter' )
                .html( '(' + '<i>' + counter + '</i> ' + selectedText + ')' );

            } else {

                counter = 1;
                target.append( '<span class="counter">(' + '<i>' + counter + '</i> ' + selectedText + ')</span>' );

            }

            // Handling counter display based on values count
            if ( counter === 0 ) {
                target.children( '.counter' ).hide();
            } else {
                target.children( '.counter' ).show();
            }
        }

        /**
         * Process filter section heading counter for filters display tags when those are removed from
         * the top of property listings
         *
         * @since 2.4.0
         *
         * @param target
         *
         * @return null
         */
        function rhea_process_filter_counter_display_tag_removal( target ) {

            let counterWrap = target.previousElementSibling.children,
                counterDOM  = $( counterWrap ).children( 'i' ),
                counter     = $( counterWrap ).children( 'i' ).html();
            counterDOM.html( --counter );

            if ( counter < 1 ) {
                $( counterWrap ).hide();
            }
        }

        /**
         * Managing selected filter values stored in local storage and populating/selecting/checking those
         * in the filters' widget list sections according to their types
         *
         * @since 4.2.0
         */
        function rhea_manage_storage_values() {


            let filterWidgetStorageItem = window.localStorage.getItem( 'filterSectionValues' );
            if ( filterWidgetStorageItem !== null ) {
                let taxonomies          = ['types', 'locations', 'statuses', 'features'],
                    radioTypes          = ['price', 'area'],
                    postTypes           = ['agent', 'agencies'],
                    numberTypes         = ['bedrooms', 'bathrooms', 'garages'],
                    filterWidgetObj     = JSON.parse( filterWidgetStorageItem ),
                    filterKeys          = Object.keys( filterWidgetObj ),
                    targetFilterSection = '';

                // Managing assigned items one by one
                filterKeys.forEach( ( key ) => {

                    // Checking through number types (bed, bath, garage)
                    radioTypes.forEach( ( radioFilter ) => {
                        if ( key === radioFilter ) {
                            let filterValue = filterWidgetObj[key];
                            if ( filterValue !== 'all' && 0 < filterValue.length ) {
                                let radioTargetID = filterValue.replaceAll( ' ', '' ),
                                    radioItem     = $( '.' + radioFilter + '-ranges .range-list .radio-wrap #' + radioFilter + '-' + radioTargetID );
                                radioItem.prop( "checked", true );
                                searchFieldValues[radioFilter] = filterValue;
                                let minMaxValues               = radioTargetID.split( '-' ),
                                    minTarget                  = 'minArea',
                                    maxTarget                  = 'maxArea';
                                if ( radioFilter === 'price' ) {
                                    minTarget = 'minPrice';
                                    maxTarget = 'maxPrice';
                                }
                                searchFieldValues[minTarget] = minMaxValues[0];
                                searchFieldValues[maxTarget] = minMaxValues[1];
                            } else {
                                $( '.' + radioFilter + '-ranges .range-list .radio-wrap #' + radioFilter + '-All' )
                                .prop( "checked", true );
                            }
                        }
                    } );

                    // Checking through taxonomies
                    taxonomies.forEach( ( taxFilter ) => {
                        if ( key === taxFilter ) {
                            targetFilterSection = 'property-' + key;
                            let filterValues    = filterWidgetObj[key];
                            if ( Array.isArray( filterValues ) ) {
                                filterValues.forEach( ( filterItem ) => {
                                    let spanItems = $( '.' + targetFilterSection + ' .terms-list span' );

                                    for ( let i = 0; i < spanItems.length; i++ ) {
                                        let termDataSet   = spanItems[i].dataset.termSlug,
                                            parentHeading = spanItems[i].parentNode.parentNode.previousElementSibling;
                                        if ( filterItem === termDataSet ) {
                                            spanItems[i].classList.add( 'active' );

                                            // Process section heading filters counter
                                            rhea_process_counter( $( parentHeading ), 'increase' );
                                            let taxonomySet = searchFieldValues[taxFilter];
                                            if ( ! Array.isArray( taxonomySet ) ) {
                                                taxonomySet = [];
                                            }
                                            // Adding current value to tax set and main object
                                            taxonomySet.push( termDataSet );
                                            searchFieldValues[taxFilter] = taxonomySet;
                                        }
                                    }
                                } );
                            }
                        }
                    } );

                    // Checking through post types
                    postTypes.forEach( ( postTypeFilter ) => {
                        if ( key === postTypeFilter ) {
                            let filterValues = filterWidgetObj[key];
                            if ( key === 'agent' ) {
                                targetFilterSection = 'agent-options';
                            } else if ( key === 'agencies' ) {
                                targetFilterSection = 'agency-options';
                            }
                            if ( Array.isArray( filterValues ) ) {
                                filterValues.forEach( ( filterItem ) => {
                                    let spanItems = $( '.' + targetFilterSection + ' .posts-list .pt-item' );
                                    for ( let i = 0; i < spanItems.length; i++ ) {
                                        let ptDataSet     = spanItems[i].dataset.postId,
                                            parentHeading = spanItems[i].parentNode.parentNode.previousElementSibling;
                                        if ( filterItem === ptDataSet ) {
                                            spanItems[i].classList.add( 'active' );

                                            // Process section heading filters counter
                                            rhea_process_counter( $( parentHeading ), 'increase' );
                                            let postTypeSet = searchFieldValues[postTypeFilter];
                                            if ( ! Array.isArray( postTypeSet ) ) {
                                                postTypeSet = [];
                                            }
                                            postTypeSet.push( ptDataSet );
                                            searchFieldValues[postTypeFilter] = postTypeSet;
                                        }
                                    }
                                } );
                            }
                        }
                    } );

                    // Checking through number types (bed, bath, garage)
                    numberTypes.forEach( ( numberFilter ) => {
                        if ( key === numberFilter ) {
                            let filterValue = filterWidgetObj[key],
                                numberClass = numberFilter.slice( 0, -1 );
                            let numberItems = $( '.radio-buttons .buttons-list .' + numberClass + '-options .option-num input#min-' + numberClass + '-' + filterValue );
                            numberItems.prop( "checked", true );
                            searchFieldValues[numberFilter] = parseInt( filterValue );
                        }
                    } );
                } );
            }

            /* Injecting the selected filters tags on the top of property
               listings based on values saved in local storage */
            rhea_inject_storage_display_tags( filterWidgetStorageItem );
        }

        /**
         * Update display filters tags to the local storage
         *
         * @since 4.2.0
         * */
        function rhea_update_storage_display() {
            let filterDisplayValues = {};

            let filterElements = $( '.rhea-filters-display > span' );
            for ( let i = 0; i < filterElements.length; i++ ) {
                if ( filterElements[i].classList[0] !== 'clear-all-filters' ) {
                    let thisChildren       = filterElements[i].children[0],
                        currentLabel       = filterElements[i].childNodes[1];
                    filterDisplayValues[i] = {
                        'id'          : filterElements[i].id,
                        'keyType'     : filterElements[i].dataset.keyType,
                        'dataValue'   : thisChildren.dataset.filterValue,
                        'filterValue' : currentLabel.textContent,
                        'typeLabel'   : thisChildren.textContent
                    };
                }
            }
            localStorage.setItem( 'filterDisplayValues', JSON.stringify( filterDisplayValues ) );
        }

        /**
         * Display all saved tags in local storage
         *
         * @param data array
         *
         * @since 4.2.0
         * */
        function rhea_inject_storage_display_tags( data ) {

            let filterDisplayValues = window.localStorage.getItem( 'filterDisplayValues' ),
                filtersDisplayWrap  = $( '.rhea-filters-display' );

            // Populating filter display tags if exists in local storage
            if ( null !== filterDisplayValues && 0 < filterDisplayValues.length ) {
                let filterValuesObj = JSON.parse( filterDisplayValues );
                if ( 0 < Object.keys( filterValuesObj ).length ) {
                    for ( let key in filterValuesObj ) {
                        let id        = filterValuesObj[key]['id'],
                            type      = filterValuesObj[key]['keyType'],
                            dataValue = filterValuesObj[key]['dataValue'],
                            value     = filterValuesObj[key]['filterValue'],
                            typeLabel = filterValuesObj[key]['typeLabel'];

                        $( filtersDisplayWrap )
                        .children( '.clear-all-filters' )
                        .before( '<span id="' + id + '" data-key-type="' + type + '"><span class="filter-name" data-filter-value="' + dataValue + '">' + typeLabel + ' </span>' + value + '<i></i></span>' );
                    }
                    $( '.clear-all-filters' ).addClass( 'active' );
                    filtersDisplayWrap.removeClass( 'empty' );
                }

                // Triggering the filters ajax function for property population according to the saved settings
                rhea_trigger_filters_ajax( searchFieldValues );
            }
        }

        // Calling the storage management function after the page is loaded
        $( document ).ready( function () {

            // Making sure that filter widget is active
            if ( $( '.property-filters' ).length ) {
                rhea_manage_storage_values();
            }
        } );

    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
    const RHEAPropertiesFilterWidgetHandler = ( $element ) => {
        elementorFrontend.elementsHandler.addHandler( RHEAPropertiesFilterWidget, {
            $element
        } );
    };

    elementorFrontend.hooks.addAction( 'frontend/element_ready/rhea-properties-filter.default', RHEAPropertiesFilterWidgetHandler );
} );