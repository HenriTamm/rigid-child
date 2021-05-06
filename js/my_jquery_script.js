
(function($){
    $(document).ready(function(){
        /**
         * Show specified tab
         * @param {object} specifiedTab jQuery DOM element
         */
        function showSpecifiedTab(specifiedTab) {
            var $tab          = specifiedTab,
                $tabs_wrapper = $( '.wc-tabs-wrapper, .woocommerce-tabs' ),
                $tabs         = $tabs_wrapper.find( '.wc-tabs, ul.tabs' ),
                target        = $( $(this).attr('scrollto') );

            // hide all tabs
            $tabs.find( 'li' ).removeClass( 'active' );
            $tabs_wrapper.find( '.wc-tab, .panel:not(.panel .panel)' ).hide();

            // show the correct tab
            $tab.closest( 'li' ).addClass( 'active' );
            $tabs_wrapper.find( $tab.find('a').attr( 'href' ) ).show();

            // scroll to the tab content
            if( target.length ) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 150}, 'slow');
            }
        }

        /**
         * Sample usage
         *
         * Clicking on <a class="button price-table">Price Table</a> will show the tab #tab-title-price_table
         * Im using on <a href="#tab-title-seller_enquiry_form" class="smooth-goto button">Saada päring</a>
         */
        $('.smooth-goto.button').on('click', function() {
          showSpecifiedTab($('#tab-title-seller_enquiry_form'));
        });
    });
})(jQuery);


// vajalik kuna funktsiooni scroll to tab ei toota
jQuery('.smooth-goto').on('click', function() {
    jQuery('html, body').animate({scrollTop: jQuery('#tab-title-seller_enquiry_form').offset().top - 150}, 'slow');
    return false;
});

// jquery for popup
// (function ($) {
//     $(document).ready(function () {
//         $('.my-popup a').magnificPopup({
//             mainClass: 'rigid-product-popup-content mfp-fade',
//             type: 'inline',
//             midClick: true
//         });
//     });
// })(window.jQuery);
