// This script is loaded both on the frontend page and in the Visual Builder.

function Biltorvet($) {
    var vehicleSearch = $(document).find('.bdt .vehicle_search');
    var vehicleSearchResults = $(document).find('.bdt .vehicle_search_results');
    var root_url = "";
    var frontpageSearch = document.getElementById("frontpage_vehicle_search");
    var frontpageSearchButton = document.getElementById("vehicle_search_frontpage_button");
    var searchFilterOptionsXHR = null;
    var loadingAnimation = vehicleSearch.find('.lds-ring');
    var makesFilter = null;
    var emptyFilter = null;
    var priceRangeSlider = null;
    var consumptionRangeSlider = null;
    var sliderAlternativeNamespace = false;
    var filter = {
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
        Ascending: null, // Bool
        BrandNew: null // Bool
    };

    if(frontpageSearch)
    {
        root_url = document.getElementById("root_url").textContent;
    }

    // if(vehicleSearch.length > 0 && vehicleSearch.data('makeids'))
    // {
    //     makesFilter = [];
    //     var mIds = vehicleSearch.data('makeids');
    //     if(!isNaN(mIds))
    //     {
    //         makesFilter.push(mIds   );
    //     } else {
    //         for(var i in mIds.split(','))
    //         {
    //             makesFilter.push(parseInt(mIds.split(',')[i]));
    //         }
    //     }
    //     emptyFilter = { MakeIds: makesFilter };
    // }

    this.Init = function() {
        // There can be a situation, namely with AVADA themes, where there's another .slider bound to the jQuery object. IF that's the case, we'll switch to an alternative namespace.
        // This alternative namespace only exists if there's been a conflict, so it can't be always used by default.
        if($.bootstrapSlider)
        {
            sliderAlternativeNamespace = true;
        }
        if($('#priceRange').length > 0)
        {
            var prsC = {
                id: $(this).attr('id'),
                min: 100,
                max:10000000,
                range: true,
                value: [1000,10000000],
                step:1,
                tooltip: 'hide' // Bootstrap v4 tooltips not supported by this plugin.
            };
            priceRangeSlider = sliderAlternativeNamespace ? $('#priceRange').bootstrapSlider(prsC) : $('#priceRange').slider(prsC);
        }
        if($('#consumptionRange').length > 0)
        {
            var crsC = {
                id: $(this).attr('id'),
                min: 0,
                max:10,
                range: true,
                value: [0,10],
                step:1,
                tooltip: 'hide' // Bootstrap v4 tooltips not supported by this plugin.
            };
            consumptionRangeSlider = sliderAlternativeNamespace ? $('#consumptionRange').bootstrapSlider(crsC) : $('#consumptionRange').slider(crsC);
        }
        this.ReloadUserFilterSelection(true);
    }

    this.ReloadUserFilterSelection = function(getFromSession)
    {
        if(vehicleSearch.length > 0)
        {
            if(searchFilterOptionsXHR !== null)
            {
                searchFilterOptionsXHR.abort();
            }

            StartLoadingAnimation();
            GetUserFilterSettings();
            DeactivateSearchFields();

            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            searchFilterOptionsXHR = $.ajax({
                url: ajax_config.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'get_filter_options',
                    'filter': getFromSession ? emptyFilter : filter // On page reload, we prefer to load the session stored filter on the serverside, instead of pushing the current selection
                },
                cache: false,
                //dataType: 'application/json;encoding=utf8',
                success: function(response){

                    var companies = '';
                    for(var i in response.companies)
                    {
                        companies += '<option value="' + response.companies[i].key + '">' + response.companies[i].value + '</option>';
                    }
                    vehicleSearch.find('select[name=company]').find('option:not(:first-child)').remove().end().append(companies);
                    if(companies !== '')
                    {
                        vehicleSearch.find('select[name=company]').removeAttr('disabled');
                    }

                    var makes = '';
                    for(var i in response.makes)
                    {
                        // if(makesFilter !== null && makesFilter.indexOf(response.makes[i].id) === -1)
                        // {
                        //     continue;
                        // }
                        makes += '<option value="' + response.makes[i].name + '">' + response.makes[i].name + '</option>';
                    }
                    vehicleSearch.find('select[name=make]').find('option:not(:first-child)').remove().end().append(makes);
                    if(makes !== '')
                    {
                        vehicleSearch.find('select[name=make]').removeAttr('disabled');
                    }

                    var models = '';
                    for(var i in response.models)
                    {
                        models += '<option value="' + response.models[i].name + '">' + response.models[i].name + '</option>';
                    }
                    vehicleSearch.find('select[name=model]').find('option:not(:first-child)').remove().end().append(models);
                    if(models !== '')
                    {
                        vehicleSearch.find('select[name=model]').removeAttr('disabled');
                    }

                    var propellants = '';
                    for(var i in response.propellants)
                    {
                        propellants += '<option value="' + response.propellants[i].name + '">' + response.propellants[i].name + '</option>';
                    }
                    vehicleSearch.find('select[name=propellant]').find('option:not(:first-child)').remove().end().append(propellants);
                    if(propellants !== '')
                    {
                        vehicleSearch.find('select[name=propellant]').removeAttr('disabled');
                    }

                    var bodyTypes = '';
                    for(var i in response.bodyTypes)
                    {
                        bodyTypes += '<option value="' + response.bodyTypes[i].name + '">' + response.bodyTypes[i].name + '</option>';
                    }
                    vehicleSearch.find('select[name=bodyType]').find('option:not(:first-child)').remove().end().append(bodyTypes);
                    if(bodyTypes !== '')
                    {
                        vehicleSearch.find('select[name=bodyType]').removeAttr('disabled');
                    }

                    var productTypes = '';
                    for(var i in response.productTypes)
                    {
                        productTypes += '<option value="' + response.productTypes[i].name + '">' + response.productTypes[i].name + '</option>';
                    }
                    vehicleSearch.find('select[name=productType]').find('option:not(:first-child)').remove().end().append(productTypes);
                    if(productTypes !== '')
                    {
                        vehicleSearch.find('select[name=productType]').removeAttr('disabled');
                    }

                    // The frontpage search doesn't respect language settings - refactoring needed
                    if(frontpageSearch)
                    {
                        frontpageSearchButton.setAttribute("data-labelpattern", "Vis " + response.totalResults + " resultater");
                        frontpageSearchButton.innerText = "Vis " + response.totalResults + " resultater";
                    }
                    else
                    {
                        vehicleSearch.find('.search').text(vehicleSearch.find('.search').data('labelpattern').replace('%u', response.totalResults));
                    }

                    if(consumptionRangeSlider !== null)
                    {
                        var crsI = sliderAlternativeNamespace ? consumptionRangeSlider.bootstrapSlider(response.consumptionMin === response.consumptionMax ? "disable" : "enable") : consumptionRangeSlider.slider(response.consumptionMin === response.consumptionMax ? "disable" : "enable");
                        crsI.data('slider')
                            .setAttribute('min', response.consumptionMin)
                            .setAttribute('max', response.consumptionMax)
                            .setValue([response.values.consumptionMin === null ? response.consumptionMin : response.values.consumptionMin, response.values.consumptionMax === null ? response.consumptionMax : response.values.consumptionMax], true, true);
                    }
                    if(priceRangeSlider !== null)
                    {
                        var prsI = sliderAlternativeNamespace ? priceRangeSlider.bootstrapSlider(response.priceMin === response.priceMax ? "disable" : "enable") : priceRangeSlider.slider(response.priceMin === response.priceMax ? "disable" : "enable");
                        prsI.data('slider')
                            .setAttribute('min', response.priceMin)
                            .setAttribute('max', response.priceMax)
                            .setValue([response.values.priceMin === null ? response.priceMin : response.values.priceMin, response.values.priceMax === null ? response.priceMax : response.values.priceMax], true, true);
                    }

                    // Select the previously selected values

                    if(response.values)
                    {
                        if(response.values.companyIds && response.values.companyIds[0])
                        {
                            vehicleSearch.find('select[name=company] option[value="' + response.values.companyIds[0] + '"]').prop('selected', true);
                        }
                        if(response.values.makes && response.values.makes[0])
                        {
                            vehicleSearch.find('select[name=make] option[value="' + response.values.makes[0] + '"]').prop('selected', true);
                        }
                        if(response.values.models && response.values.models[0])
                        {
                            vehicleSearch.find('select[name=model] option[value="' + response.values.models[0] + '"]').prop('selected', true);
                        }
                        if(response.values.propellants && response.values.propellants[0])
                        {
                            vehicleSearch.find('select[name=propellant] option[value="' + response.values.propellants[0] + '"]').prop('selected', true);
                        }
                        if(response.values.bodyTypes && response.values.bodyTypes[0])
                        {
                            vehicleSearch.find('select[name=bodyType] option[value="' + response.values.bodyTypes[0] + '"]').prop('selected', true);
                        }
                        if(response.values.productTypes && response.values.productTypes[0])
                        {
                            vehicleSearch.find('select[name=productType] option[value="' + response.values.productTypes[0] + '"]').prop('selected', true);
                        }
                    }
                },
                complete: function()
                {
                    searchFilterOptionsXHR = null;
                    StopLoadingAnimation();
                }
            });
        }
    }

    this.SaveUserFilterSettings = function()
    {
        StartLoadingAnimation();
        GetUserFilterSettings();
        SaveFilter();
    }

    this.ResetFilter = function()
    {
        StartLoadingAnimation();
        filter = null;
        var urlPath = window.location.pathname;
        var urlPathElements = urlPath.split('/');

        window.history.replaceState(null, null, '/' + urlPathElements[1]);
        SaveFilter();
    }

    // "Private" functions
    function SaveFilter()
    {
        StartLoadingAnimation();

        $.ajax({
            url: ajax_config.ajax_url,
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'save_filter',
                'filter': filter
            },
            cache: false,
            success: function(response){
                // When page number is ommitted, the session with search settings gets deleted.
                // That's why, unless we're resetting the filter, we need to redirect to "the first page of results"
                // var firstResultsPage = window.location.href.replace(/\/$/, '').replace(/\/\d+$/, '');
                // if(filter !== null)
                // {
                //     firstResultsPage = firstResultsPage + '/1'; // get rid of the trailing slash, if present.
                // }
                // window.location.href = firstResultsPage;

                updateUrl(filter, function () {
                    window.location.reload();
                });
            },
            complete: function()
            {
                StopLoadingAnimation();
            }
        });
    }

    function updateUrl(filter, callback){

        var urlPath = window.location.pathname;
        var filterUrlPath = urlPath;
        var urlPathElements = urlPath.split('/');

        for (var property in filter) {

            if (property == 'Models'  || property == 'Makes') {

                var filterValue = filter[property]

                if (Array.isArray(filter[property])) {
                    filterValue = filter[property].shift();
                }

                filterUrlPath = buildFilterUrl(urlPathElements, property, filterValue);
            }
        }

        // issues with "+" decoding in browsers - temp. solution is to replace "+" signs in the urlpath with "%2b"
        window.history.pushState(null, null, filterUrlPath.replace("+", "%2B"));

        return callback();
    }

    function buildFilterUrl(urlPathElements, filterKey, filterValue) {

        var urlstring = '';
        /*
            urlPathElements Mapping
            0 = empty
            1 = page slug
            2 = pagination page
            3 = make
            4 = model
        */

        switch (filterKey) {
            case 'Makes':
                urlPathElements[3] = filterValue;
                break;
            case 'Models':
                urlPathElements[4] = filterValue;
                break;
        }

        if (urlPathElements[2] === "") {
            urlPathElements[2] = 1;
        }
        // Make sure pagination is set to 1 when setting new filters
        else if (urlPathElements[2] != ""){

            urlPathElements[2] = 1;
        }

        // Specialcase - Frontpage seach / mini search
        if(frontpageSearch)
        {
            urlPathElements[1] = root_url
            urlPathElements[2] = 1;
        }

        for (var i = 1; i < urlPathElements.length; i++ ) {
            if (urlPathElements[i] !== null) {
                urlstring += '/' + urlPathElements[i];
            }
        }

        if(frontpageSearch)
        {
            urlstring = urlstring.substr(1);
        }

        return urlstring;
    }

    function DeactivateSearchFields()
    {
        if(sliderAlternativeNamespace)
        {
            consumptionRangeSlider.bootstrapSlider("disable");
            priceRangeSlider.bootstrapSlider("disable");
        } else {
            consumptionRangeSlider.slider("disable");
            priceRangeSlider.slider("disable");
        }
        vehicleSearch.find('select').prop('disabled', true);
    }

    function StartLoadingAnimation()
    {
        if(loadingAnimation.hasClass('d-none'))
        {
            loadingAnimation.css('opacity', 0).css('display', 'block').removeClass('d-none');
        }
        loadingAnimation.animate({opacity: 1}, 1000);
    }

    function StopLoadingAnimation()
    {
        loadingAnimation.animate({opacity: 0}, 200, function(){ $(this).css('display', 'none').addClass('d-none'); });
    }

    function FormatDKPrice(nbr) {
        return nbr.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function FindNumberSeries(nbr)
    {
        var numberSeries = '1';
        for(var i = 1; i < (''+nbr+'').length; i++)
        {
            numberSeries += '0';
        }
        return parseInt(numberSeries);
    }

    function GetUserFilterSettings()
    {
        // Don't send min/max values if the selected values equal min and max.
        // This allows to return results which have no values set on these fields.
        var priceMin = priceRangeSlider.data('slider').getValue()[0];
        priceMin = priceMin === -1 ? null : priceMin;
        var priceMax = priceRangeSlider.data('slider').getValue()[1];
        priceMax = priceMax === -1 ? null : priceMax;
        var consumptionMin = consumptionRangeSlider.data('slider').getValue()[0];
        consumptionMin = consumptionMin === -1 ? null : consumptionMin;
        var consumptionMax = consumptionRangeSlider.data('slider').getValue()[1];
        consumptionMax = consumptionMax === -1 ? null : consumptionMax;
        filter = {
            CompanyIds: vehicleSearch.find('select[name=company]').val() === '' ? null : [vehicleSearch.find('select[name=company]').val()],
            Propellants: vehicleSearch.find('select[name=propellant]').val() === '' ? null : [vehicleSearch.find('select[name=propellant]').val()],
            Makes: vehicleSearch.find('select[name=make]').val() === '' ? null : [vehicleSearch.find('select[name=make]').val()],
            Models: vehicleSearch.find('select[name=model]').val() === '' ? null : [vehicleSearch.find('select[name=model]').val()],
            BodyTypes: vehicleSearch.find('select[name=bodyType]').val() === '' ? null : [vehicleSearch.find('select[name=bodyType]').val()],
            ProductTypes: vehicleSearch.find('select[name=productType]').val() === '' ? null : [vehicleSearch.find('select[name=productType]').val()],
            PriceMin: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('min') !== priceMin ? priceMin : null) : null,
            PriceMax: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('max') !== priceMax ? priceMax : null) : null,
            ConsumptionMin: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('min') !== consumptionMin ? consumptionMin : null) : null,
            ConsumptionMax: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('max') !== consumptionMax ? consumptionMax : null) : null,
            Start: null,
            Limit: null,
            OrderBy: vehicleSearchResults.find('select[name=orderBy]').val() === '' ? null : vehicleSearchResults.find('select[name=orderBy]').val(),
            Ascending: vehicleSearchResults.find('select[name=ascDesc]').val() === 'asc' ? true : false,
            BrandNew: null
        }
    }

    // Fire the "Constructor"
    this.Init();
}

function FormatPrice(x, suffix)
{
    if(x === -1)
    {
        return x;
    }
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + (suffix ? ',-' : '');
}











jQuery(function($) {
    var bdt = new Biltorvet($);

    $(document)
        .on('change', '.bdtSlider', function(e){
            var min = e.value.newValue[0];
            var max = e.value.newValue[1];
            if($(e.target).attr('id') === 'priceRange')
            {
                min = FormatPrice(min, false);
                max = FormatPrice(max, true);
            }
            if(min === -1)
            {
                min = '';
            }
            if(max === -1)
            {
                max = '';
            }

            $(this).closest('.bdtSliderContainer')
                .find('.bdtSliderMinVal')
                .text(min)
                .end()
                .find('.bdtSliderMaxVal')
                .text(max);
        })
        .on('slideStop', '.bdtSlider', function(e){
            // var min = e.value[0];
            // var max = e.value[1];
            bdt.ReloadUserFilterSelection(false);
        })
        .on('click', '.bdt .reset', function(e){
            e.preventDefault();

            $(this).closest('.bdt .vehicle_search').find('select').val('');
            bdt.ResetFilter();
        })
        .on('click', '.bdt .search', function(e){
            e.preventDefault();

            bdt.SaveUserFilterSettings();
        })
        .on('change', '.bdt .vehicle_search_results', function(){
            bdt.SaveUserFilterSettings();
        })
        .on('change', '.bdt .vehicle_search select', function(){
            var vehicleSearch = $(this).closest('.bdt .vehicle_search');
            // Selecting a different make will reset the selected model
            if($(this).attr('name') === 'make')
            {
                vehicleSearch.find('select[name=model]').val('').trigger('change');
            }
            // selecting a new model will reset all other fields.
            if($(this).attr('name') === 'model')
            {
                vehicleSearch.find('select[name=bodyType]').val('');
                vehicleSearch.find('select[name=productType]').val('');
                vehicleSearch.find('select[name=propellant]').val('');
                vehicleSearch.find('select[name=priceMinMax]').val('');
                vehicleSearch.find('select[name=priceMinMax]').val('');
                vehicleSearch.find('select[name=consumptionMin]').val('');
                vehicleSearch.find('select[name=consumptionMax]').val('');
            }
            bdt.ReloadUserFilterSelection(false);
        })

});