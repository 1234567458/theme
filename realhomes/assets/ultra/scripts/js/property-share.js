( function ( $ ) {
    "use strict";
    /*-----------------------------------------------------------------------------------*/
    /* Share Button
    /* https://github.com/carrot/share-button
    /*-----------------------------------------------------------------------------------*/
    $( document ).ready( function () {
        var propertyTitle         = $( '.single-property-title, .rh_page__title' ).text(),
            propertyThumbnail     = '',
            propertyDescription   = $.trim( $( '.entry-content p:first' ).text() ),
            descriptionTextLength = 100, // Description Test Length for Social Media
            descriptionTextLabel  = 'Property URL'; // Label for URL you'd like to share via email

        if ( typeof printProperty !== 'undefined' ) {
            propertyThumbnail = printProperty.propertyThumbnail;
        }

        var config = {
            title       : propertyTitle,
            image       : propertyThumbnail,
            description : propertyDescription.substring( 0, descriptionTextLength ),
            ui          : {
                flyout : $( 'body' ).hasClass( 'rtl' ) ? 'center right' : 'center left'
            },
            networks    : {
                email : {
                    title       : propertyTitle,
                    description : propertyDescription + '%0A%0A' + descriptionTextLabel + ': ' + window.location.href
                }
            }
        };

        var social_share     = new Share( ".share-this", config );
        var social_share_btn = $( '.rh-ultra-share' );
        social_share_btn.on( 'click', function ( e ) {
            e.preventDefault();
            social_share.toggle();
        } );
    } );
} )( jQuery );