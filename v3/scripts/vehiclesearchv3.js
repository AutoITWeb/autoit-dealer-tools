// This script is loaded both on the frontend page and in the Visual Builder.

/**
 * The main vehicle search script
 *
 */
function Biltorvet($) {
    var vehicleSearch = $(document).find('.bdt .vehicle_search');
    var vehicleSearchResults = $(document).find('.bdt .vehicle_search_results');
    var root_url = "";
    var frontpageSearch = document.getElementById("frontpage_vehicle_search");
    var frontpageSearchButton = document.getElementById("vehicle_search_frontpage_button");
    var searchFilterOptionsXHR = null;
    var loadingAnimation = vehicleSearch.find('.lds-ring');
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
        VehicleStates: null,
        FullTextSearch: null,
        PriceMin: null,
        PriceMax: null,
        ConsumptionMin: null,
        ConsumptionMax: null,
        Start: null, // will be overwritten by paging
        Limit: null, // will be overwritten by paging
        OrderBy: null, // OrderByEnum
        Ascending: null, // Bool
    };

    // To avoid splitting the code up in a frontpage search and main search
    // checks to see if the user is on the frontpage or not are done throughout the code
    if(frontpageSearch)
    {
        root_url = document.getElementById("root_url").textContent;
    }

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
                min: 0,
                max:10000000,
                range: true,
                value: [0,10000000],
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
        this.PlaceholderShuffler();
    }

    // Fulltextsearch suffle placeholder
    this.PlaceholderShuffler = function()
    {
        element = vehicleSearch.find('input[name=fullTextSearch]');
        var owner = this;
        var placeholders = [
            'Søg på mærke, model, farve, udstyr mm...          ',
            'Stationcar anhængertræk...                        ',
            'Hvid varevogn...                                  ',
            'Diesel SUV...                                     '
        ];

        var randomIndex = Math.floor(Math.random() * 4);
        var placeholder = placeholders[randomIndex];
        var i = 0;
        var interval = setInterval(function(){
            if(i === placeholder.length) {
                clearInterval(interval);
                setTimeout(() => owner.PlaceholderShuffler(), 400)
                return;
            }
            element.attr('placeholder', placeholder.substr(0, i +1));
            i++;
        }, 100)
    }

    /**
     * Starts the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     * @param {bool} True = empty filter, false the current filter
     *
     */
    this.ReloadUserFilterSelection = function(getFromSession)
    {
        if(vehicleSearch.length > 0)
        {
            if(searchFilterOptionsXHR !== null)
            {
                searchFilterOptionsXHR.abort();
            }

            StartLoadingAnimation();
            // Get the current filters set by the user
            GetUserFilterSettings();

            // Deactive filters
            DeactivateSearchFields();

            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            searchFilterOptionsXHR = $.ajax({
                url: ajax_config.restUrl + 'autoit-dealer-tools/v1/filteroptions',
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'get_filter_options',
                    'filter': getFromSession ? emptyFilter : filter // On page reload, we prefer to load the session stored filter on the serverside, instead of pushing the current selection
                },
                cache: false,
                //dataType: 'application/json;encoding=utf8',
                success: function(response){

                    SetFilters(response);

                    // Select the previously selected values
                    if(response.values)
                    {
                        if(response.values.companyIds && response.values.companyIds[0])
                        {
                            vehicleSearch.find('select[name=company] option[value="' + response.values.companyIds[0] + '"]').prop('selected', true);
                        }
                        if(response.values.fullTextSearch && response.values.fullTextSearch[0])
                        {
                            vehicleSearch.find('input[name=fullTextSearch]').val(response.values.fullTextSearch[0]);
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
                        if(response.values.vehicleStates && response.values.vehicleStates[0])
                        {
                            vehicleSearch.find('select[name=vehicleState] option[value="' + response.values.vehicleStates[0] + '"]').prop('selected', true);
                        }
                    }
                },
                complete: function()
                {
                    searchFilterOptionsXHR = null;
                    StopLoadingAnimation();
                    StopLoadingAnimationPaging();
                }
            });
        }
    }

    this.ResetFilters = function()
    {
        return $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/resetfilteroptions',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'reset_filter_options'
            },
            cache: false,
            success: function(response){

                 SetFilters(response);
            },
            complete: function()
            {
                StopLoadingAnimation();
                //StopLoadingAnimationPaging();
            }
        });
    }

    this.ResetFilter = function()
    {
        StartLoadingAnimation();

        DeactivateSearchFields();

        filter = null;

        // The VehicleSearch() function is called directly as we don't want to scrollTop when resetting the filter
        this.ResetFilters().then(VehicleSearch).done(function() {
            //console.log("done")
        });
    }

    this.ResetFrontpageFilter = function()
    {
        StartLoadingAnimation();

        DeactivateSearchFields();

        filter = null;

        this.ResetFilters();
    }

    this.StartVehicleSearch = function ()
    {
        StartLoadingAnimation();

        // Fetch results, append to dom and then scrollTop
        $.when(VehicleSearch()).then(function(){
            $('html, body').animate({
                scrollTop: $('.vehicle-row').offset().top - 150
            }, 500);
        });
    }

    /**
     * Search button has been clicked - time to fetch some vehicles
     * @return {response} - replaces the current vehicles with the ones from this result
     *
     */
    function VehicleSearch()
    {
        return $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search',
                'filter': filter
            },
            cache: false,
            success: function(response){

                $('#vehicle_search_results').html(response);

            },
            complete: function()
            {
                StopLoadingAnimationPaging();
                StopLoadingAnimation();
            }
        });
    }

    this.SaveUserFilterSettings = function()
    {
        StartLoadingAnimation();
        GetUserFilterSettings();

        SaveFilter();

        StopLoadingAnimation();
    }

    this.StartOrderByAndAscDesc = function()
    {
        OrderByAndAscDesc();
    }

    /**
     * Handles order_by and ascending / descending options
     *
     */
    function OrderByAndAscDesc()
    {
        // Start loader / spinner?
        StartLoadingAnimationPaging();

        GetUserFilterSettings();

        filter.OrderBy = vehicleSearchResults.find('select[name=orderBy]').val() === '' ? null : vehicleSearchResults.find('select[name=orderBy]').val();
        filter.Ascending = vehicleSearchResults.find('select[name=ascDesc]').val() === 'asc' ? true : false;

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search',
                'filter': filter
            },
            cache: false,
            success: function(response){

                $('#vehicle_search_results').html(response);
            },
            complete: function()
            {
                StopLoadingAnimationPaging();
            }
        });
    }

    this.PagingFetchMore = function()
    {
        FetchMore();
    };

    /**
     * Returns more vehicles from the current filter
     *
     */
    function FetchMore(){

        // Load start spinner?
        StartLoadingAnimationPaging();

        // Get all current elements with the animate class
        var currentVehicleCards = document.querySelectorAll(".animate__animated");

        // Remove the animate class on all elements to avoid flickering when new vehicles are appended
        currentVehicleCards.forEach(vehicleCard => {
           vehicleCard.classList.remove('animate__animated');
        });

        // Get paging data from the paging button
        const pagingData = document.querySelector('#paging-button');

        var currentPage = parseInt(pagingData.dataset.currentPage);
        var amountOfPages = parseInt(pagingData.dataset.amountOfPages);
        var limit = parseInt(pagingData.dataset.limit);
        var start = parseInt(pagingData.dataset.end);

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search_paging',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search_paging',
                'filter': filter,
                'currentPage': currentPage,
                'start': start,
                'limit': limit
            },
            cache: false,
            success: function(response){

                // Append data
                const vehicleList = document.querySelector('#vehicle-row');
                //vehicleList.append(response);
                vehicleList.innerHTML += response;

                // Should I remove the paging button?
                if(amountOfPages !== currentPage +1) {
                    pagingData.dataset.currentPage = String(currentPage +1);
                    pagingData.dataset.end = String((start + limit));

                } else {
                    pagingData.remove();
                }
            },
            complete: function()
            {
                // Load stop animation / spinner?
                StopLoadingAnimationPaging();
            }
         });
    }

    /**
     * Used by the frontpage search functionality
     * Saves the current filter and redirects the user to vehicle search page (set in the plugin settings
     *
     */
    function SaveFilter()
    {
        StartLoadingAnimation();

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/filteroptions/savefilter',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'save_filter',
                'filter': filter
            },
            cache: false,
            success: function(response){

                window.location.href = root_url + "/";
            },
            complete: function()
            {
                StopLoadingAnimation();
            }
        });
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

    /**
     * Starts the main spinner (the one in the filters area)
     *
     */
    function StartLoadingAnimation()
    {
        if(loadingAnimation.hasClass('d-none'))
        {
            loadingAnimation.css('opacity', 0).css('display', 'block').removeClass('d-none');
        }
        loadingAnimation.animate({opacity: 1}, 1000);
    }

    /**
     * Stops the main spinner (the one in the filters area)
     *
     */
    function StopLoadingAnimation()
    {
        loadingAnimation.animate({opacity: 0}, 200, function(){ $(this).css('display', 'none').addClass('d-none'); });
    }

    /**
     * Starts the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     *
     */
    function StartLoadingAnimationPaging()
    {
        var loadingAnimationPaging = vehicleSearchResults.find('.lds-ring-paging');

        if(loadingAnimationPaging.hasClass('d-none'))
        {
            loadingAnimationPaging.css('opacity', 0).css('display', 'block').removeClass('d-none');
        }
        loadingAnimationPaging.animate({opacity: 1}, 1000);
    }

    /**
     * Stops the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     *
     */
    function StopLoadingAnimationPaging()
    {
        var loadingAnimationPaging = vehicleSearchResults.find('.lds-ring-paging');

        loadingAnimationPaging.animate({opacity: 0}, 200, function(){ $(this).css('display', 'none').addClass('d-none'); });
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

    function SetFilters(response)
    {
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

        // Update filters
        var fullTextSearch = '';
        for(var i in response.fullTextSearch)
        {
            fullTextSearch += response.fullTextSearch.value;
        }

        vehicleSearch.find('input[name=fullTextSearch]').val(fullTextSearch);
        if(fullTextSearch !== '')
        {
            vehicleSearch.find('input[name=fullTextSearch]').removeAttr('disabled');
        }

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

        var vehicleStates = '';
        for(var i in response.vehicleStates)
        {
            vehicleStates += '<option value="' + response.vehicleStates[i].name + '">' + response.vehicleStates[i].name + '</option>';
        }
        vehicleSearch.find('select[name=vehicleState]').find('option:not(:first-child)').remove().end().append(vehicleStates);
        if(vehicleStates !== '')
        {
            vehicleSearch.find('select[name=vehicleState]').removeAttr('disabled');
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
    }

    /**
     * Fetches the curren filters settings
     *
     */
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
            FullTextSearch: vehicleSearch.find('input[name=fullTextSearch]').val() === '' ? null : [vehicleSearch.find('input[name=fullTextSearch]').val()],
            Propellants: vehicleSearch.find('select[name=propellant]').val() === '' ? null : [vehicleSearch.find('select[name=propellant]').val()],
            Makes: vehicleSearch.find('select[name=make]').val() === '' ? null : [vehicleSearch.find('select[name=make]').val()],
            Models: vehicleSearch.find('select[name=model]').val() === '' ? null : [vehicleSearch.find('select[name=model]').val()],
            BodyTypes: vehicleSearch.find('select[name=bodyType]').val() === '' ? null : [vehicleSearch.find('select[name=bodyType]').val()],
            ProductTypes: vehicleSearch.find('select[name=productType]').val() === '' ? null : [vehicleSearch.find('select[name=productType]').val()],
            VehicleStates: vehicleSearch.find('select[name=vehicleState]').val() === '' ? null : [vehicleSearch.find('select[name=vehicleState]').val()],
            PriceMin: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('min') !== priceMin ? priceMin : null) : null,
            PriceMax: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('max') !== priceMax ? priceMax : null) : null,
            ConsumptionMin: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('min') !== consumptionMin ? consumptionMin : null) : null,
            ConsumptionMax: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('max') !== consumptionMax ? consumptionMax : null) : null,
            Start: null,
            Limit: null,
            OrderBy: vehicleSearchResults.find('select[name=orderBy]').val() === '' ? null : vehicleSearchResults.find('select[name=orderBy]').val(),
            Ascending: vehicleSearchResults.find('select[name=ascDesc]').val() === 'asc' ? true : false,
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


/**
 * This part listens to changes in the frontend - .on('click', 'change') etc.
 *
 */
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

            bdt.ReloadUserFilterSelection(false);
        })
        .on('click', '.bdt .reset', function(e){
            e.preventDefault();

            $(this).closest('.bdt .vehicle_search').find('select').val('');

            bdt.ResetFilter();
        })
        .on('click', '.bdt .resetFrontpage', function(e){
            e.preventDefault();

            $(this).closest('.bdt .vehicle_search').find('select').val('');

            bdt.ResetFrontpageFilter();
        })
        .on('click', '.bdt .search', function(e){
            e.preventDefault();

            bdt.StartVehicleSearch();
        })
        .on('click', '#vehicle_search_frontpage_button', function(e){
            e.preventDefault();

            bdt.SaveUserFilterSettings();
        })
        .on('change', '.searchFilter select', function(e) {
            e.preventDefault();

            bdt.StartOrderByAndAscDesc();
        })
        .on('click', '.bdt .paging-button', function(e){
            e.preventDefault();

            bdt.PagingFetchMore();
        })

        // FullTextSearch, input field
        .on('blur', '.fullTextSearch', function(){
            var vehicleSearch = $(this).closest('.bdt .vehicle_search');

            // using the fulltext search field will reset all other fields.
            vehicleSearch.find('select[name=company]').val('');
            vehicleSearch.find('select[name=vehicleState]').val('');
            vehicleSearch.find('select[name=make]').val('');
            vehicleSearch.find('select[name=model]').val('');
            vehicleSearch.find('select[name=bodyType]').val('');
            vehicleSearch.find('select[name=productType]').val('');
            vehicleSearch.find('select[name=propellant]').val('');
            vehicleSearch.find('select[name=priceMinMax]').val('');
            vehicleSearch.find('select[name=priceMinMax]').val('');
            vehicleSearch.find('select[name=consumptionMin]').val('');
            vehicleSearch.find('select[name=consumptionMax]').val('');

            bdt.ReloadUserFilterSelection(false);
        })

        // Select fields
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
            // selecting vehicle state will reset all other fields.
            if($(this).attr('name') === 'vehicleState')
            {
                vehicleSearch.find('select[name=make]').val('');
                vehicleSearch.find('select[name=model]').val('');
                vehicleSearch.find('select[name=bodyType]').val('');
                vehicleSearch.find('select[name=productType]').val('');
                vehicleSearch.find('select[name=propellant]').val('');
                vehicleSearch.find('select[name=priceMinMax]').val('');
                vehicleSearch.find('select[name=priceMinMax]').val('');
                vehicleSearch.find('select[name=consumptionMin]').val('');
                vehicleSearch.find('select[name=consumptionMax]').val('');
            }
            // selecting a new company will reset all other fields.
            if($(this).attr('name') === 'company')
            {
                vehicleSearch.find('select[name=vehicleState]').val('');
                vehicleSearch.find('select[name=make]').val('');
                vehicleSearch.find('select[name=model]').val('');
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