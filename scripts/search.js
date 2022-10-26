jQuery(function ($) {
    $(document).ready(function () {
        var frontpageSearch = document.getElementById("frontpage_vehicle_search");

            if(frontpageSearch == null) {

                const params = new URLSearchParams(window.location.search);

                if(params.has('scroll') && params.get('scroll') === 'true')
                {
                    $('html, body').animate({
                        scrollTop: $('.vehicle-row').offset().top - 150
                    }, 500);
                }
            }
        });
    });