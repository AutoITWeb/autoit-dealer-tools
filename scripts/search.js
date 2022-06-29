/**
 * The once mighty search.js
 * Handles the scrolling when a user has used the frontpage search functionality,
 * is redirected to the vehicle search page (Set in the plugin settings), the vehicle search page is loaded
 * and we want the user scrolled to the results.
 *
 */

jQuery(function ($) {

    $(document).ready(function () {

        var frontpageSearch = document.getElementById("frontpage_vehicle_search");

        if(frontpageSearch == null) {
            $('html, body').animate({
                scrollTop: $('.vehicle_search_results').offset().top - 150
            }, 500);
        }
    });
});

