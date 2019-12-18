jQuery(function ($) {
    let filter = {
        CompanyIds: null,
        Propellant: null,
        Makes: null,
        Models: null,
        BodyTypes: null,
        ProductTypes: null,
        PriceMin: null,
        PriceMax: null,
        ConsumptionMin: null,
        ConsumptionMax: null,
        Start: null, // will be overwritten by paging
        Limit: null, // will be overwritten by paging
        OrderBy: null, // OrderByEnum
        Ascending: false, // Bool
        BrandNew: null // Bool
    };
    let urlFilterMake = null;
    let urlFilterModel = null;

    $(document).ready(function () {
        getUrlPathFilters
            .then(function () {

                $.ajax({
                    url: ajax_config.ajax_url,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        'action': 'get_filter_options',
                        'filter': filter
                    },
                    cache: false,
                    success: function (filters) {
                        var _vehicleSearch = $('.bdt .vehicle_search');

                        if (filters.models !== null) {
                            var _modelSelect = _vehicleSearch.find('select[name="model"]');
                            var modelOptions = '';

                            for (var i = 0; i < filters.models.length; i++) {
                                modelOptions += '<option value="' + filters.models[i].name + '">' + filters.models[i].name + '</option>';
                            }

                            _modelSelect.find(':not(:first-child)').remove().end().append(modelOptions);

                            _modelSelect.removeAttr('disabled');
                        }

                        if (urlFilterMake !== null) {
                            _vehicleSearch.find('select[name=make] option[value="' + decodeURIComponent(urlFilterMake) + '"]').prop('selected', true);
                        }

                        if (urlFilterModel !== null) {
                            _vehicleSearch.find('select[name=model] option[value="' + decodeURIComponent(urlFilterModel) + '"]').prop('selected', true);
                        }

                        _vehicleSearch.find('.search').text(_vehicleSearch.find('.search').data('labelpattern').replace('%u', $('.vehicle_search_results').attr('data-totalResults')));

                        var frontpageSearch = document.getElementById("frontpage_vehicle_search");

                        if(frontpageSearch == null) {
                            $('html, body').animate({
                                scrollTop: $('.vehicle_search_results').offset().top - 150
                            }, 500);
                        }
                    }

                });

            })
            .catch(function (error) {
                console.log(error.message);
            })
    });

    const getUrlPathFilters = new Promise(
        function (resolve, reject) {
            let urlPathElements = window.location.pathname.split('/');

            if (urlPathElements[2] !== "") {

                if (urlPathElements[2] !== "" && urlPathElements[2] !== filter.Start) {
                    filter.Start = urlPathElements[2];
                }

                if (urlPathElements[3] !== "" && urlPathElements[3] !== filter.Makes) {
                    filter.Makes = [decodeURIComponent(urlPathElements[3])];
                    urlFilterMake = urlPathElements[3];
                }

                if (urlPathElements[4] !== "") {
                    urlFilterModel = decodeURIComponent(urlPathElements[4]);
                }

                resolve({
                    'status': 'ok'
                })
            } else {
                reject(new Error('No url parameters'));
            }

        }
    );

});

