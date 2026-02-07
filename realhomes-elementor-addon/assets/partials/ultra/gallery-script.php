<?php
/**
 * This file contains the script for card gallery variation
 *
 * @version 2.4.0
 **/
global $settings;
?>
<script>
    ( function ( $ ) {
        'use strict';
        $( document ).ready( function () {
            let autoplayDelay = <?php echo ! empty( $settings['gallery_autoplay_speed'] ) ? $settings['gallery_autoplay_speed'] : 1000; ?>;

            $( '.rhea-properties-gallery-card' ).each( function () {
                let slider    = $( this ).slick( {
                    autoplay         : true,
                    centerMode       : true,
                    slidesToShow     : 1,
                    fade             : true,
                    speed            : <?php echo ! empty( $settings['gallery_animation_speed'] ) ? $settings['gallery_animation_speed'] : 1000; ?>,
                    pauseOnFocus     : false,
                    pauseOnHover     : false,
                    pauseOnDotsHover : false,
                    arrows           : false,
                    dots             : false,
                    lazyLoad         : 'ondemand'
                } ).slick( "slickPause" );
                autoplayDelay = autoplayDelay + 300;
                setTimeout( function () {
                    slider.slick( "slickPlay" );
                }, autoplayDelay );
            } );
        } );
    } )( jQuery );
</script>